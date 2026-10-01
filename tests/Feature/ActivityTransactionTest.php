<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class ActivityTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_exception_rolls_back_category_and_activity(): void
    {
        try {
            DB::transaction(function (): void {
                $category = Category::create([
                    'name' => 'Eksperimen Transaction',
                    'slug' => 'eksperimen-transaction',
                ]);

                Activity::create($this->activityData($category->id));

                throw new RuntimeException('Simulasi kegagalan.');
            });
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulasi kegagalan.', $exception->getMessage());
        }

        $this->assertDatabaseMissing('categories', ['slug' => 'eksperimen-transaction']);
        $this->assertDatabaseCount('activities', 0);
    }

    public function test_transaction_commits_category_and_activity_when_callback_finishes(): void
    {
        DB::transaction(function (): void {
            $category = Category::create([
                'name' => 'Eksperimen Transaction Berhasil',
                'slug' => 'eksperimen-transaction-berhasil',
            ]);

            Activity::create($this->activityData($category->id));
        });

        $this->assertDatabaseHas('categories', ['slug' => 'eksperimen-transaction-berhasil']);
        $this->assertDatabaseCount('activities', 1);
    }

    private function activityData(int $categoryId): array
    {
        return [
            'category_id' => $categoryId,
            'code' => 'TX-001',
            'title' => 'Kegiatan Transaction',
            'start_at' => '2026-10-01',
            'end_at' => '2026-10-01',
            'capacity' => 10,
            'status' => 'draft',
        ];
    }
}
