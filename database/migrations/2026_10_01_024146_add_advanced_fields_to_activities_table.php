<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('code', 30)->unique()->after('category_id');
            $table->renameColumn('activity_date', 'start_at');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->date('end_at')->after('start_at');
            $table->integer('capacity')->default(1)->after('end_at');
            $table->string('status', 20)->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['end_at', 'capacity', 'code']);
            $table->renameColumn('start_at', 'activity_date');
            $table->string('status', 20)->default('Planned')->change();
        });
    }
};
