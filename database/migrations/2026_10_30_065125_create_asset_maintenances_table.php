<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('issue_description'); // ပစ္စည်းပျက်စီးမှု / ပြဿနာအကြောင်းအရာ
            $table->text('action_taken')->nullable(); // ပြင်ဆင်ခဲ့သည့် နည်းလမ်း
            $table->decimal('cost', 10, 2)->default(0); // ကုန်ကျစရိတ်
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->date('reported_date');
            $table->date('completed_date')->nullable();
            $table->foreignId('asset_item_id')->constrained('asset_items')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};