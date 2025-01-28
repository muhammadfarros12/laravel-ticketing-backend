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
        Schema::create('skus', function (Blueprint $table) {
            $table->id();
            // name
            $table->string('name');
            // category
            $table->string('category');
            // price
            $table->decimal('price', 10, 2);
            // event id
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            // stock
            $table->integer('stock');
            // day type string
            $table->string('day_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skus');
    }
};
