<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('sri_environment', 1)->default('1')->after('sri_access_key');
            $table->string('sri_status')->nullable()->after('sri_environment');
            $table->text('sri_response')->nullable()->after('sri_status');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['sri_environment', 'sri_status', 'sri_response']);
        });
    }
};
