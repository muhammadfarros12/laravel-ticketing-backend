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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // user_id foreign key
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // event_id foreign key
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            // quantity
            $table->integer('quantity');
            // total price
            $table->decimal('total_price', 10, 2);
            // event_date
            $table->date('event_date');
            // status_payment default pending
            $table->enum('status_payment', ['pending', 'success', 'cancel'])->default('pending');
            // payment_url
            $table->string('payment_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
