<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('organization_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price_per_hour', 8, 2);
            $table->json('facilities')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization_rooms');
    }
};
