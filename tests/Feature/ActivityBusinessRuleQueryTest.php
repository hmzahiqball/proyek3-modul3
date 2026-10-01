<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityBusinessRuleQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_activity_is_always_created_as_draft(): void
    {
        $category = $this->createCategory('workshop');

        $this->post(route('activities.store'), $this->activityData([
            'category_id' => $category->id,
            'status' => 'completed',
        ]))->assertRedirect();

        $this->assertDatabaseHas('activities', [
            'code' => 'ACT-001',
            'status' => 'draft',
        ]);
    }

    public function test_draft_can_be_published_through_publish_action(): void
    {
        $activity = $this->createActivity('draft');

        $response = $this->post(route('activities.publish', $activity));

        $response->assertRedirect(route('activities.show', $activity));
        $this->assertDatabaseHas('activities', ['id' => $activity->id, 'status' => 'published']);
    }

    public function test_only_published_activity_can_be_completed(): void
    {
        $draft = $this->createActivity('draft', 'DR-001');
        $published = $this->createActivity('published', 'PB-001');

        $this->post(route('activities.complete', $draft))
            ->assertSessionHasErrors('status');
        $this->assertDatabaseHas('activities', ['id' => $draft->id, 'status' => 'draft']);

        $this->post(route('activities.complete', $published))
            ->assertRedirect(route('activities.show', $published));
        $this->assertDatabaseHas('activities', ['id' => $published->id, 'status' => 'completed']);
    }

    public function test_general_edit_form_does_not_expose_status_input(): void
    {
        $activity = $this->createActivity('draft');

        $this->get(route('activities.edit', $activity))
            ->assertOk()
            ->assertDontSee('name="status"', false);
    }

    public function test_combined_query_filters_and_sort_are_applied(): void
    {
        $workshop = $this->createCategory('workshop');
        $seminar = $this->createCategory('seminar');

        $this->createActivity('published', 'WS-001', $workshop, 'Laravel Workshop', '2026-10-20');
        $this->createActivity('published', 'WS-002', $workshop, 'Docker Workshop', '2026-10-10');
        $this->createActivity('draft', 'WS-003', $workshop, 'Laravel Draft', '2026-10-30');
        $this->createActivity('published', 'SM-001', $seminar, 'Laravel Seminar', '2026-10-05');

        $response = $this->get(route('activities.index', [
            'search' => 'Workshop',
            'category_id' => $workshop->id,
            'status' => 'published',
            'sort' => 'oldest',
        ]));

        $response->assertOk()
            ->assertSee('Docker Workshop')
            ->assertSee('Laravel Workshop')
            ->assertDontSee('Laravel Draft')
            ->assertDontSee('Laravel Seminar');

        $content = $response->getContent();

        $this->assertLessThan(
            strpos($content, 'Laravel Workshop'),
            strpos($content, 'Docker Workshop')
        );
    }

    public function test_pagination_preserves_combined_query_parameters(): void
    {
        $category = $this->createCategory('workshop');

        foreach (range(1, 11) as $number) {
            $this->createActivity('published', "WS-{$number}", $category, "Workshop {$number}", "2026-10-{$number}");
        }

        $response = $this->get(route('activities.index', [
            'search' => 'Workshop',
            'category_id' => $category->id,
            'status' => 'published',
            'sort' => 'newest',
            'page' => 2,
        ]));

        $response->assertOk()
            ->assertSee('search=Workshop')
            ->assertSee('category_id='.$category->id)
            ->assertSee('status=published')
            ->assertSee('sort=newest');
    }

    private function createCategory(string $slug): Category
    {
        return Category::create(['name' => ucfirst($slug), 'slug' => $slug]);
    }

    private function createActivity(
        string $status,
        string $code = 'ACT-001',
        ?Category $category = null,
        string $title = 'Kegiatan Uji',
        string $startAt = '2026-10-10'
    ): Activity {
        $category ??= $this->createCategory('workshop-'.strtolower($code));

        return Activity::create($this->activityData([
            'category_id' => $category->id,
            'code' => $code,
            'title' => $title,
            'start_at' => $startAt,
            'end_at' => $startAt,
            'status' => $status,
        ]));
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
