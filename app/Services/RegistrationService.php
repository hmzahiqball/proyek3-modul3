<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Registration;
use DomainException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RegistrationService
{
    public function register(Activity $activity, array $data, bool $simulateFailure = false): Registration
    {
        $this->ensureRegistrationAllowed($activity, $data['email']);

        return DB::transaction(function () use ($activity, $data, $simulateFailure): Registration {
            $lockedActivity = Activity::query()
                ->whereKey($activity->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureRegistrationAllowed($lockedActivity, $data['email']);

            $registration = $lockedActivity->registrations()->create([
                'participant_name' => $data['participant_name'],
                'email' => strtolower($data['email']),
                'registered_at' => now(),
            ]);

            if ($simulateFailure) {
                throw new RuntimeException('Simulasi kegagalan setelah pendaftaran dibuat.');
            }

            $lockedActivity->increment('registered_count');

            return $registration;
        });
    }

    private function ensureRegistrationAllowed(Activity $activity, string $email): void
    {
        if ($activity->status !== 'published') {
            throw new DomainException('Pendaftaran hanya tersedia untuk kegiatan yang published.');
        }

        if ($activity->start_at->isPast()) {
            throw new DomainException('Pendaftaran ditutup karena kegiatan sudah dimulai.');
        }

        if ($activity->registered_count >= $activity->capacity) {
            throw new DomainException('Pendaftaran ditutup karena kapasitas kegiatan sudah penuh.');
        }

        if ($activity->registrations()->where('email', strtolower($email))->exists()) {
            throw new DomainException('Email tersebut sudah terdaftar pada kegiatan ini.');
        }
    }
}
