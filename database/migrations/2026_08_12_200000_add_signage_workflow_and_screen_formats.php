<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('advertiser_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('advertisements', function (Blueprint $table): void {
            $table->string('status')->default('draft')->after('is_active');
            $table->timestamp('submitted_at')->nullable()->after('status');
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('reviewed_by');
            $table->unsignedSmallInteger('video_total_seconds')->nullable()->after('duration_seconds');
        });

        Schema::table('display_screens', function (Blueprint $table): void {
            $table->string('format')->default('landscape_16_9')->after('location');
            $table->unsignedSmallInteger('width_px')->default(1920)->after('format');
            $table->unsignedSmallInteger('height_px')->default(1080)->after('width_px');
            $table->unsignedSmallInteger('ads_before_video')->default(3)->after('carousel_seconds');
            $table->unsignedSmallInteger('video_segment_seconds')->default(30)->after('ads_before_video');
        });
    }

    public function down(): void
    {
        Schema::table('display_screens', function (Blueprint $table): void {
            $table->dropColumn(['format', 'width_px', 'height_px', 'ads_before_video', 'video_segment_seconds']);
        });

        Schema::table('advertisements', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['status', 'submitted_at', 'reviewed_at', 'rejection_reason', 'video_total_seconds']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('advertiser_id');
        });
    }
};
