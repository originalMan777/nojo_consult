<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // resource | service | offer
            $table->string('status')->default('draft'); // draft | active | inactive
            $table->string('internal_name');
            $table->string('title');
            $table->text('short_text')->nullable();
            $table->string('button_text')->nullable();
            $table->string('icon_key')->nullable();
            $table->json('content')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_boxes');
    }
};
