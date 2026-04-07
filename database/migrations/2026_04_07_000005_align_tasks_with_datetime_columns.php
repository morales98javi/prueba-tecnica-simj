<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'started_at')) {
                $table->dateTime('started_at')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('tasks', 'ended_at')) {
                $table->dateTime('ended_at')->nullable()->after('started_at');
            }
        });

        DB::table('tasks')
            ->whereNull('started_at')
            ->update(['started_at' => DB::raw('created_at')]);

        DB::table('tasks')
            ->select(['id', 'started_at', 'duration_hours'])
            ->whereNull('ended_at')
            ->orderBy('id')
            ->chunkById(200, function ($tasks): void {
                foreach ($tasks as $task) {
                    if (!$task->started_at) {
                        continue;
                    }

                    $minutes = max((int) round(((float) $task->duration_hours) * 60), 1);
                    $endedAt = Carbon::parse($task->started_at)->addMinutes($minutes);

                    DB::table('tasks')
                        ->where('id', $task->id)
                        ->update(['ended_at' => $endedAt]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'ended_at')) {
                $table->dropColumn('ended_at');
            }

            if (Schema::hasColumn('tasks', 'started_at')) {
                $table->dropColumn('started_at');
            }
        });
    }
};
