<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['is_public']);
            $table->unsignedBigInteger('visibility_id');

            $table->foreign('visibility_id')->references('id')->on('visibilities');
        }) ;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['visibility_id']);

            $table->dropColumn(['visibility_id']);
        }) ;
    }
};
