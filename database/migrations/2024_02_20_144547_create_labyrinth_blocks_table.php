<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('labyrinth_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('labyrinth_id');
            $table->integer('x');
            $table->integer('y');
            $table->boolean('passable')->default(true);
            $table->timestamps();

            $table->unique(['labyrinth_id', 'x', 'y']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labyrinth_blocks');
    }
};
