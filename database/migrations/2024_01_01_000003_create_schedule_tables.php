<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ngày trong lịch trình
        Schema::create('trip_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->unsignedTinyInteger('day_number');
            $table->string('title')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['trip_id', 'date']);
            $table->unique(['trip_id', 'day_number']);
        });

        // Hoạt động trong từng ngày
        Schema::create('trip_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_day_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', [
                'transport',
                'accommodation',
                'food',
                'sightseeing',
                'activity',
                'other',
            ])->default('activity');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->decimal('estimated_cost', 15, 0)->default(0);
            $table->string('reference_url')->nullable();
            $table->enum('status', ['suggested', 'approved', 'rejected'])->default('suggested');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Bình chọn hoạt động
        Schema::create('activity_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('trip_activities')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('vote', ['up', 'down']);
            $table->timestamps();

            $table->unique(['activity_id', 'user_id']);
        });

        // Comment trên hoạt động
        Schema::create('activity_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('trip_activities')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_comments');
        Schema::dropIfExists('activity_votes');
        Schema::dropIfExists('trip_activities');
        Schema::dropIfExists('trip_days');
    }
};
