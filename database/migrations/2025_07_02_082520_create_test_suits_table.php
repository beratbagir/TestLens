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
        Schema::create('test_suits', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Test suit ismi
            $table->json('scenario_ids');        // Senaryo ID'lerini JSON olarak saklar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_suits');
    }
};
