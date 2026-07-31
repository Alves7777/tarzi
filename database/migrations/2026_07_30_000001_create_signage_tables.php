<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('document')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('registration_fee_cents')->default(0);
            $table->timestamps();
        });

        Schema::create('display_screens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('carousel_seconds')->default(8);
            $table->timestamps();
        });

        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertiser_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('media_type')->default('image');
            $table->string('media_path');
            $table->string('click_url')->nullable();
            $table->unsignedSmallInteger('duration_seconds')->default(8);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ad_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('display_screen_id')->nullable()->constrained()->nullOnDelete();
            $table->string('placement');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('price_cents')->default(0);
            $table->timestamps();

            $table->index(['placement', 'is_active']);
        });

        Schema::create('billing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('monthly_price_cents')->default(0);
            $table->unsignedInteger('ad_slot_price_cents')->default(0);
            $table->unsignedInteger('registration_fee_cents')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertiser_id')->constrained()->cascadeOnDelete();
            $table->foreignId('billing_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->unique();
            $table->string('description');
            $table->unsignedInteger('amount_cents');
            $table->string('status')->default('pending');
            $table->date('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('billing_plans');
        Schema::dropIfExists('ad_placements');
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('display_screens');
        Schema::dropIfExists('advertisers');
    }
};
