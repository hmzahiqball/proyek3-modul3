<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ActivityQueryPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_lazy_loading_repeats_category_query(): void
    {
        $category = Category::create(['name' => 'Workshop', 'slug' => 'workshop']);
        $this->createActivities($category);

        DB::enableQueryLog();
        $activities = Activity::take(10)->get();

        foreach ($activities as $activity) {
            $activity->category?->name;
        }

        $this->assertSame(11, count(DB::getQueryLog()));
    }

    public function test_eager_loading_uses_one_query_for_activities_and_one_for_categories(): void
    {
        $category = Category::create(['name' => 'Workshop', 'slug' => 'workshop']);
        $this->createActivities($category);

        DB::enableQueryLog();
        $activities = Activity::with('category')->take(10)->get();

        foreach ($activities as $activity) {
            $activity->category?->name;
        }

        $this->assertSame(2, count(DB::getQueryLog()));
    }

    private function createActivities(Category $category): void
    {
        foreach (range(1, 10) as $number) {
            Activity::create([
                'category_id' => $category->id,
                'code' => "N1-{$number}",
                'title' => "Kegiatan {$number}",
                'start_at' => '2026-10-01',
                'end_at' => '2026-10-01',
                'capacity' => 10,
                'status' => 'draft',
            ]);
        }
    }
}
