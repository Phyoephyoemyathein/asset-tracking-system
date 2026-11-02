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
        Schema::create('assets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('sku');
    $table->text('qr_code')->nullable();
    $table->integer('quantity')->default(0);
    $table->enum('status', ['available', 'in_use', 'maintenance', 'damaged'])->default('available');
    $table->unique(['sku', 'warehouse_id']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
