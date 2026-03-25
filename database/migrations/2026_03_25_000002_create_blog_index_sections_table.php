<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_index_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique();
            $table->boolean('enabled')->default(true);
            $table->string('source_type')->default('latest');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title_override')->nullable();
            $table->timestamps();
        });

        $now = now();

        DB::table('blog_index_sections')->insert([
            [
                'section_key' => 'wide_section',
                'enabled' => true,
                'source_type' => 'latest',
                'category_id' => null,
                'title_override' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'section_key' => 'cluster_left',
                'enabled' => true,
                'source_type' => 'latest',
                'category_id' => null,
                'title_override' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'section_key' => 'cluster_right',
                'enabled' => true,
                'source_type' => 'latest',
                'category_id' => null,
                'title_override' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_index_sections');
    }
};
