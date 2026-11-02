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
       Schema::create('stock_transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('asset_id')->constrained()->onDelete('cascade');
        $table->enum('type', ['in', 'out','transfer']); // ပစ္စည်းအဝင် (in) သို့မဟုတ် အထွက် (out)
        $table->integer('quantity'); // အရေအတွက်
        $table->string('reference')->nullable(); // မှတ်ချက် သို့မဟုတ် Voucher နံပါတ်
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
