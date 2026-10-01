<?php

namespace App\Services;

use App\Models\Activity;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ActivityService
{
    /**
     * Transisi status yang diizinkan.
     *
     * Eksperimen 2 - Langkah 7:
     * Transisi status ditempatkan pada method khusus karena:
     * 1. Business rule transisi tidak bisa divalidasi hanya dari data request,
     *    melainkan bergantung pada STATE (status saat ini) dari Activity.
     * 2. Form Request hanya memvalidasi input, bukan state domain.
     * 3. Menempatkan logic ini di Service menjaga controller tetap tipis
     *    dan memudahkan pengujian serta reuse dari berbagai entry point
     *    (web, API, console).
     * 4. Mencegah perubahan status ilegal seperti completed -> draft.
     */
    private const TRANSITIONS = [
        'draft' => ['draft', 'published'],
        'published' => ['published', 'completed'],
        'completed' => ['completed'],
    ];

    /**
     * Field yang wajib diisi sebelum Activity bisa dipublish.
     */
    private const PUBLISH_REQUIRED_FIELDS = [
        'category_id',
        'code',
        'title',
        'start_at',
        'end_at',
        'capacity',
    ];

    public function create(array $data, ?UploadedFile $poster = null): Activity
    {
        unset($data['poster']);
        $data['status'] = 'draft';
        $newPosterPath = $this->storePoster($poster);

        if ($newPosterPath !== null) {
            $data['poster_path'] = $newPosterPath;
        }

        try {
            return Activity::create($data);
        } catch (Throwable $exception) {
            $this->deletePoster($newPosterPath);

            throw $exception;
        }
    }

    public function update(Activity $activity, array $data, ?UploadedFile $poster = null): Activity
    {
        unset($data['poster']);
        $nextStatus = $data['status'] ?? $activity->status;
        $this->ensureValidTransition($activity->status, $nextStatus);
        $oldPosterPath = $activity->poster_path;
        $newPosterPath = $this->storePoster($poster);

        if ($newPosterPath !== null) {
            $data['poster_path'] = $newPosterPath;
        }

        try {
            $activity->update($data);
        } catch (Throwable $exception) {
            $this->deletePoster($newPosterPath);

            throw $exception;
        }

        if ($newPosterPath !== null && $oldPosterPath !== null) {
            $this->deletePoster($oldPosterPath);
        }

        return $activity->refresh();
    }

    private function storePoster(?UploadedFile $poster): ?string
    {
        return $poster?->store('posters', 'public');
    }

    private function deletePoster(?string $posterPath): void
    {
        if ($posterPath !== null) {
            Storage::disk('public')->delete($posterPath);
        }
    }

    /**
     * Eksperimen 2 - Langkah 4 & 5:
     * Publish Activity. Method harus:
     * - Menolak Activity yang BUKAN draft (langkah 4)
     * - Memeriksa kelengkapan field wajib sebelum publish (langkah 5)
     */
    public function publish(Activity $activity): Activity
    {
        if ($activity->status !== 'draft') {
            throw new DomainException(
                "Hanya kegiatan berstatus 'draft' yang dapat dipublish. Status saat ini: {$activity->status}."
            );
        }

        // Periksa kelengkapan field wajib
        $missingFields = [];
        foreach (self::PUBLISH_REQUIRED_FIELDS as $field) {
            if (empty($activity->{$field})) {
                $missingFields[] = $field;
            }
        }

        if (! empty($missingFields)) {
            throw new DomainException(
                'Field berikut harus diisi sebelum publish: '.implode(', ', $missingFields)
            );
        }

        $activity->update(['status' => 'published']);

        return $activity->refresh();
    }

    public function complete(Activity $activity): Activity
    {
        if ($activity->status !== 'published') {
            throw new DomainException(
                "Hanya kegiatan berstatus 'published' yang dapat diselesaikan. Status saat ini: {$activity->status}."
            );
        }

        $activity->update(['status' => 'completed']);

        return $activity->refresh();
    }

    /**
     * Eksperimen 2 - Langkah 6:
     * Memastikan transisi status valid.
     * Contoh: completed -> draft TIDAK diizinkan.
     */
    private function ensureValidTransition(string $current, string $next): void
    {
        $allowed = self::TRANSITIONS[$current] ?? [];

        if (! in_array($next, $allowed, true)) {
            throw new DomainException(
                "Transisi status '{$current}' ke '{$next}' tidak diizinkan."
            );
        }
    }
}
