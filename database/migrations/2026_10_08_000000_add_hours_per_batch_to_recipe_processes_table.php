<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipe_processes', function (Blueprint $table) {
            $table->decimal('hours_per_batch', 8, 2)->nullable()->after('process_id');
        });
    }

    public function down(): void
    {
        Schema::table('recipe_processes', function (Blueprint $table) {
            $table->dropColumn('hours_per_batch');
        });
    }
};
