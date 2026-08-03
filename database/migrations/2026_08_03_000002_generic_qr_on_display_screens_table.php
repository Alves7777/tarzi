<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('display_screens', function (Blueprint $table): void {
            $table->string('qr_url')->nullable()->after('carousel_seconds');
            $table->string('qr_label')->nullable()->after('qr_url');
            $table->string('qr_caption')->nullable()->after('qr_label');
        });

        if (Schema::hasColumn('display_screens', 'tune_ride_uuid')) {
            foreach (DB::table('display_screens')->whereNotNull('tune_ride_uuid')->get() as $screen) {
                $base = rtrim($screen->tune_base_url ?: 'https://tune.zeivoll.com.br', '/');
                DB::table('display_screens')->where('id', $screen->id)->update([
                    'qr_url' => $base.'/ride/'.$screen->tune_ride_uuid,
                    'qr_label' => 'Zeivoll Tune',
                    'qr_caption' => 'Escaneie o QR Code',
                ]);
            }

            Schema::table('display_screens', function (Blueprint $table): void {
                $table->dropColumn(['tune_base_url', 'tune_ride_uuid']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('display_screens', function (Blueprint $table): void {
            $table->string('tune_base_url')->nullable()->after('carousel_seconds');
            $table->uuid('tune_ride_uuid')->nullable()->after('tune_base_url');
        });

        Schema::table('display_screens', function (Blueprint $table): void {
            $table->dropColumn(['qr_url', 'qr_label', 'qr_caption']);
        });
    }
};
