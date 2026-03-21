<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('popup_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('popup_id')->constrained('popups')->cascadeOnDelete();
            $table->string('page_key')->nullable()->index();
            $table->string('source_url', 2048)->nullable();
            $table->enum('lead_type', ['general', 'buyer', 'seller'])->default('general')->index();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone', 50)->nullable();
            $table->text('message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popup_leads');
    }
};
