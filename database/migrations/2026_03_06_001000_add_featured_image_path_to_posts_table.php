<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'featured_image_path')) {
                $table->string('featured_image_path', 2048)->nullable()->after('sources');
            }

            if (! Schema::hasColumn('posts', 'featured_image_alt')) {
                $table->string('featured_image_alt')->nullable()->after('featured_image_path');
            }

            if (! Schema::hasColumn('posts', 'featured_image_caption')) {
                $table->string('featured_image_caption')->nullable()->after('featured_image_alt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'featured_image_caption')) {
                $table->dropColumn('featured_image_caption');
            }

            if (Schema::hasColumn('posts', 'featured_image_alt')) {
                $table->dropColumn('featured_image_alt');
            }

            if (Schema::hasColumn('posts', 'featured_image_path')) {
                $table->dropColumn('featured_image_path');
            }
        });
    }
};
