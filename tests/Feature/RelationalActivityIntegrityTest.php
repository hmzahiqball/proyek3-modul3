<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationalActivityIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_can_be_created_with_a_valid_category(): void
    {
        $category = $this->createCategory('workshop');

        $response = $this->post(route('activities.store'), $this->activityData([
            'category_id' => $category->id,
            'code' => 'WS-001',
        ]));

        $activity = Activity::where('code', 'WS-001')->firstOrFail();

        $response->assertRedirect(route('activities.show', $activity));
        $this->assertSame($category->id, $activity->category->id);
    }

    public function test_activity_with_an_unknown_category_is_rejected(): void
    {
        $response = $this->from(route('activities.create'))
            ->post(route('activities.store'), $this->activityData(['category_id' => 999]));

        $response->assertRedirect(route('activities.create'))
            ->assertSessionHasErrors('category_id');
        $this->assertDatabaseCount('activities', 0);
    }

    public function test_duplicate_activity_code_is_rejected(): void
    {
        $category = $this->createCategory('workshop');
        Activity::create($this->activityData([
            'category_id' => $category->id,
            'code' => 'WS-001',
        ]));

        $response = $this->from(route('activities.create'))
            ->post(route('activities.store'), $this->activityData([
                'category_id' => $category->id,
                'code' => 'WS-001',
            ]));

        $response->assertRedirect(route('activities.create'))
            ->assertSessionHasErrors('code');
        $this->assertDatabaseCount('activities', 1);
    }

    public function test_update_can_keep_the_activity_own_code(): void
    {
        $category = $this->createCategory('workshop');
        $activity = Activity::create($this->activityData([
            'category_id' => $category->id,
            'code' => 'WS-001',
        ]));

        $response = $this->put(route('activities.update', $activity), $this->activityData([
            'category_id' => $category->id,
            'code' => 'WS-001',
            'title' => 'Workshop Diperbarui',
        ]));

        $response->assertRedirect(route('activities.show', $activity));
        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'code' => 'WS-001',
            'title' => 'Workshop Diperbarui',
        ]);
    }

    public function test_category_in_use_cannot_be_deleted(): void
    {
        $category = $this->createCategory('workshop');
        Activity::create($this->activityData(['category_id' => $category->id]));

        $response = $this->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'))
            ->assertSessionHas('error', 'Kategori "Workshop" tidak dapat dihapus karena masih digunakan oleh 1 kegiatan.');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_category_referenced_by_a_trashed_activity_cannot_be_deleted(): void
    {
        $category = $this->createCategory('workshop');
        $activity = Activity::create($this->activityData(['category_id' => $category->id]));
        $activity->delete();

        $response = $this->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'))
            ->assertSessionHas('error', 'Kategori "Workshop" tidak dapat dihapus karena masih digunakan oleh 1 kegiatan.');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_empty_category_can_be_deleted(): void
    {
        $category = $this->createCategory('workshop');

        $response = $this->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'))
            ->assertSessionHas('success', 'Kategori "Workshop" berhasil dihapus!');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_is_available_in_forms_and_activity_views(): void
    {
        $category = $this->createCategory('workshop');
        $activity = Activity::create($this->activityData(['category_id' => $category->id]));

        $this->get(route('activities.create'))
            ->assertOk()
            ->assertSee('Workshop');
        $this->get(route('activities.index'))
            ->assertOk()
            ->assertSee('Workshop');
        $this->get(route('activities.show', $activity))
            ->assertOk()
            ->assertSee('Workshop');
    }

    private function createCategory(string $slug): Category
    {
        return Category::create([
            'name' => 'Workshop',
            'slug' => $slug,
        ]);
    }

    private function activityData(array $overrides = []): array
    {
        return array_merge([
            'category_id' => 1,
            'code' => 'ACT-001',
            'title' => 'Kegiatan Uji',
            'description' => 'Deskripsi uji.',
            'start_at' => '2026-10-10',
            'end_at' => '2026-10-10',
            'capacity' => 25,
        ], $overrides);
    }
}
