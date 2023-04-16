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
        Schema::create('equipments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('equipment_type_id');
            $table->string('serial_number', 64);
            $table->text('desc')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at');

            $table
                ->foreign('equipment_type_id')
                ->references('id')
                ->on('equipment_types');

            $table->unique(['equipment_type_id', 'serial_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
