<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('display_screens', function (Blueprint $table): void {
            $table->string('tune_base_url')->nullable()->after('carousel_seconds');
            $table->uuid('tune_ride_uuid')->nullable()->after('tune_base_url');
        });
    }

    public function down(): void
    {
        Schema::table('display_screens', function (Blueprint $table): void {
            $table->dropColumn(['tune_base_url', 'tune_ride_uuid']);
        });
    }
};
