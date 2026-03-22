<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_box_id')->constrained('lead_boxes')->cascadeOnDelete();
            $table->string('lead_slot_key');
            $table->string('page_key')->nullable();
            $table->text('source_url');
            $table->string('type'); // resource | service | offer
            $table->string('first_name');
            $table->string('email');
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['lead_slot_key', 'page_key']);
            $table->index(['email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
