<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('popups', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['general', 'buyer', 'seller', 'consultation', 'resource'])->default('general');
            $table->boolean('is_active')->default(true);

            // Content
            $table->string('eyebrow')->nullable();
            $table->string('headline');
            $table->text('body')->nullable();
            $table->string('cta_text')->default('Get Started');
            $table->text('success_message')->nullable();

            // Appearance
            $table->string('layout')->default('centered'); // centered, split, banner

            // Trigger rules
            $table->enum('trigger_type', ['time', 'scroll', 'exit', 'click'])->default('time');
            $table->unsignedInteger('trigger_delay')->nullable();  // seconds
            $table->unsignedInteger('trigger_scroll')->nullable(); // percent

            // Targeting
            $table->json('target_pages')->nullable();
            $table->enum('device', ['all', 'desktop', 'mobile'])->default('all');
            $table->enum('frequency', ['once_session', 'once_day', 'always'])->default('once_session');

            // Form behavior
            $table->json('form_fields')->nullable();
            $table->enum('lead_type', ['general', 'buyer', 'seller'])->default('general');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popups');
    }
};
