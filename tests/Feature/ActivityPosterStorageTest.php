<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActivityPosterStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_poster_is_stored_and_displayed(): void
    {
        Storage::fake('public');
        $category = $this->createCategory();

        $this->post(route('activities.store'), $this->activityData($category, [
            'poster' => UploadedFile::fake()->image('poster.jpg'),
        ]))->assertRedirect();

        $activity = Activity::firstOrFail();

        $this->assertNotNull($activity->poster_path);
        Storage::disk('public')->assertExists($activity->poster_path);
        $this->get(route('activities.show', $activity))
            ->assertOk()
            ->assertSee('storage/'.$activity->poster_path);
    }

    public function test_non_image_poster_is_rejected(): void
    {
        Storage::fake('public');
        $category = $this->createCategory();

        $this->from(route('activities.create'))
            ->post(route('activities.store'), $this->activityData($category, [
                'poster' => UploadedFile::fake()->create('poster.txt', 10, 'text/plain'),
            ]))
            ->assertRedirect(route('activities.create'))
            ->assertSessionHasErrors('poster');

        $this->assertDatabaseCount('activities', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_replacing_poster_removes_the_old_file_after_update(): void
    {
        Storage::fake('public');
        $category = $this->createCategory();
        $activity = Activity::create($this->activityData($category));
        $oldPosterPath = 'posters/old-poster.jpg';
        Storage::disk('public')->put($oldPosterPath, 'old');
        $activity->update(['poster_path' => $oldPosterPath]);

        $this->put(route('activities.update', $activity), $this->activityData($category, [
            'poster' => UploadedFile::fake()->image('new-poster.jpg'),
        ]))->assertRedirect();

        $activity->refresh();

        Storage::disk('public')->assertMissing($oldPosterPath);
        Storage::disk('public')->assertExists($activity->poster_path);
        $this->assertNotSame($oldPosterPath, $activity->poster_path);
    }

    public function test_soft_delete_keeps_poster_for_restore(): void
    {
        Storage::fake('public');
        $category = $this->createCategory();
        $activity = Activity::create($this->activityData($category));
        $posterPath = 'posters/restore-poster.jpg';
        Storage::disk('public')->put($posterPath, 'poster');
        $activity->update(['poster_path' => $posterPath]);

        $this->delete(route('activities.destroy', $activity));

        Storage::disk('public')->assertExists($posterPath);
        $this->patch(route('activities.restore', $activity->id));
        $this->assertSame($posterPath, $activity->fresh()->poster_path);
        Storage::disk('public')->assertExists($posterPath);
    }

    private function createCategory(): Category
    {
        return Category::create([
            'name' => 'Workshop',
            'slug' => 'workshop-'.uniqid(),
        ]);
    }

    private function activityData(Category $category, array $overrides = []): array
    {
        return array_merge([
            'category_id' => $category->id,
            'code' => 'WS-'.uniqid(),
            'title' => 'Workshop Poster',
            'description' => 'Deskripsi poster.',
            'start_at' => '2026-12-10',
            'end_at' => '2026-12-10',
            'capacity' => 25,
        ], $overrides);
    }
}
