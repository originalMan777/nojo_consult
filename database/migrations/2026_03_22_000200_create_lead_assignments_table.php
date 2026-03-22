<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_slot_id')->constrained('lead_slots')->cascadeOnDelete();
            $table->foreignId('lead_box_id')->constrained('lead_boxes')->cascadeOnDelete();
            $table->string('override_title')->nullable();
            $table->text('override_short_text')->nullable();
            $table->string('override_button_text')->nullable();
            $table->timestamps();

            $table->unique('lead_slot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_assignments');
    }
};
