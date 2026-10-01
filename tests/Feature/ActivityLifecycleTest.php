<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy_soft_deletes_activity_and_hides_it_from_index(): void
    {
        $activity = $this->createActivity();

        $this->delete(route('activities.destroy', $activity))
            ->assertRedirect(route('activities.index'));

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
        ]);
        $this->assertSoftDeleted('activities', [
            'id' => $activity->id,
        ]);
        $this->assertSame(0, Activity::count());
        $this->assertSame(1, Activity::withTrashed()->count());
        $this->assertSame(1, Activity::onlyTrashed()->count());

        $this->get(route('activities.index'))
            ->assertOk()
            ->assertDontSee($activity->title);
    }

    public function test_trash_page_lists_deleted_activity_and_restore_returns_it_to_index(): void
    {
        $activity = $this->createActivity();
        $activity->delete();

        $this->get(route('activities.trash'))
            ->assertOk()
            ->assertSee($activity->title)
            ->assertSee('Pulihkan');

        $this->patch(route('activities.restore', $activity->id))
            ->assertRedirect(route('activities.index'));

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'deleted_at' => null,
        ]);

        $this->get(route('activities.index'))
            ->assertOk()
            ->assertSee($activity->title);
    }

    private function createActivity(): Activity
    {
        $category = Category::create([
            'name' => 'Workshop',
            'slug' => 'workshop',
        ]);

        return Activity::create([
            'category_id' => $category->id,
            'code' => 'WS-001',
            'title' => 'Workshop Soft Delete',
            'start_at' => '2026-10-10',
            'end_at' => '2026-10-10',
            'capacity' => 25,
            'status' => 'draft',
        ]);
    }
}
