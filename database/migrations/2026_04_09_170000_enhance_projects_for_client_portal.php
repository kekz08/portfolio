<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            if (! Schema::hasColumn('projects', 'budget')) {
                $table->string('budget')->nullable()->after('description');
            }

            if (! Schema::hasColumn('projects', 'deadline')) {
                $table->date('deadline')->nullable()->after('budget');
            }

            if (! Schema::hasColumn('projects', 'progress_percentage')) {
                $table->unsignedTinyInteger('progress_percentage')->default(0)->after('status');
            }

            if (! Schema::hasColumn('projects', 'start_date')) {
                $table->timestamp('start_date')->nullable()->after('approved_at');
            }
        });

        DB::table('projects')
            ->where('status', 'pending_review')
            ->update(['status' => 'pending']);

        if (Schema::hasColumn('projects', 'started_at')) {
            DB::statement("
                UPDATE projects
                SET start_date = COALESCE(start_date, started_at)
                WHERE started_at IS NOT NULL
            ");
        }

        DB::table('projects')
            ->whereNull('progress_percentage')
            ->update(['progress_percentage' => 0]);
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            if (Schema::hasColumn('projects', 'start_date')) {
                $table->dropColumn('start_date');
            }

            if (Schema::hasColumn('projects', 'progress_percentage')) {
                $table->dropColumn('progress_percentage');
            }

            if (Schema::hasColumn('projects', 'deadline')) {
                $table->dropColumn('deadline');
            }

            if (Schema::hasColumn('projects', 'budget')) {
                $table->dropColumn('budget');
            }
        });
    }
};

