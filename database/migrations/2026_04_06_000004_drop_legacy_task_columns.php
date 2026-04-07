<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("tasks", function (Blueprint $table) {
            $table->dropColumn(["date", "end_date", "start_time", "end_time"]);
        });
    }

    public function down(): void
    {
        Schema::table("tasks", function (Blueprint $table) {
            $table->date("date")->after("user_id");
            $table->date("end_date")->after("date");
            $table->time("start_time")->after("end_date");
            $table->time("end_time")->after("start_time");
        });
    }
};
