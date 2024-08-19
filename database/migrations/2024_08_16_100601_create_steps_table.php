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
        Schema::create('steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedInteger('step_number')->nullable(false);
            $table->json('content');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('parent_id')->references('id')->on('steps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('steps');
    }
};
