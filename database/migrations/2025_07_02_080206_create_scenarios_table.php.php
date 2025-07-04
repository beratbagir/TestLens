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
        Schema::create('scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->json('steps')->nullable();       // Adımlar JSON formatında
            $table->json('screenshots')->nullable(); // Çoklu görsel saklama
            $table->json('videos')->nullable();      // Çoklu video saklama
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenarios');
    }
};
