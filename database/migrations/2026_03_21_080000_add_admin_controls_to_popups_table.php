<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            $table->string('role')->default('standard')->after('type');
            $table->unsignedInteger('priority')->default(100)->after('role');
            $table->string('audience')->default('guests')->after('frequency');
            $table->boolean('suppress_if_lead_captured')->default(true)->after('audience');
            $table->string('suppression_scope')->default('all_lead_popups')->after('suppress_if_lead_captured');
            $table->string('post_submit_action')->default('message')->after('lead_type');
            $table->string('post_submit_redirect_url', 2048)->nullable()->after('post_submit_action');
        });
    }

    public function down(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'priority',
                'audience',
                'suppress_if_lead_captured',
                'suppression_scope',
                'post_submit_action',
                'post_submit_redirect_url',
            ]);
        });
    }
};
