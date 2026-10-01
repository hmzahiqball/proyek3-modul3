<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Category;
use App\Services\RegistrationService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class AtomicRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_activity_accepts_a_valid_registration(): void
    {
        $activity = $this->createActivity('published');

        $response = $this->post(route('activities.registrations.store', $activity), $this->registrationData());

        $response->assertRedirect()
            ->assertSessionHas('success', 'Pendaftaran berhasil.');
        $this->assertDatabaseHas('registrations', [
            'activity_id' => $activity->id,
            'email' => 'peserta@example.com',
        ]);
        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'registered_count' => 1,
        ]);
    }

    public function test_duplicate_email_on_same_activity_is_rejected(): void
    {
        $activity = $this->createActivity('published');
        $this->post(route('activities.registrations.store', $activity), $this->registrationData());

        $this->post(route('activities.registrations.store', $activity), $this->registrationData())
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('registrations', 1);
        $this->assertDatabaseHas('activities', ['id' => $activity->id, 'registered_count' => 1]);
    }

    public function test_draft_activity_rejects_registration(): void
    {
        $activity = $this->createActivity('draft');

        $this->post(route('activities.registrations.store', $activity), $this->registrationData())
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_full_activity_rejects_registration(): void
    {
        $activity = $this->createActivity('published', capacity: 1);
        $activity->update(['registered_count' => 1]);

        $this->post(route('activities.registrations.store', $activity), $this->registrationData())
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_activity_that_has_started_rejects_registration(): void
    {
        $activity = $this->createActivity('published', startAt: '2026-09-30');

        $this->post(route('activities.registrations.store', $activity), $this->registrationData())
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_failure_after_registration_creation_rolls_back_both_changes(): void
    {
        $activity = $this->createActivity('published');
        $service = app(RegistrationService::class);

        try {
            $service->register($activity, $this->registrationData(), simulateFailure: true);
            $this->fail('Expected the simulated registration failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulasi kegagalan setelah pendaftaran dibuat.', $exception->getMessage());
        }

        $this->assertDatabaseCount('registrations', 0);
        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'registered_count' => 0,
        ]);
    }

    public function test_service_rejects_invalid_registration_rules(): void
    {
        $activity = $this->createActivity('draft');
        $service = app(RegistrationService::class);

        $this->expectException(DomainException::class);
        $service->register($activity, $this->registrationData());
    }

    private function createActivity(
        string $status,
        int $capacity = 25,
        string $startAt = '2026-12-10'
    ): Activity {
        $category = Category::create([
            'name' => 'Workshop',
            'slug' => 'workshop-'.uniqid(),
        ]);

        return Activity::create([
            'category_id' => $category->id,
            'code' => 'WS-'.uniqid(),
            'title' => 'Workshop Registration',
            'start_at' => $startAt,
            'end_at' => $startAt,
            'capacity' => $capacity,
            'status' => $status,
        ]);
    }

    private function registrationData(): array
    {
        return [
            'participant_name' => 'Peserta Uji',
            'email' => 'peserta@example.com',
        ];
    }
}
