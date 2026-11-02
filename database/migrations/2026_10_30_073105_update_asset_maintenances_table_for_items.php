<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_maintenances', function (Blueprint $table) {
            // asset_id ရှိပြီးသားဆိုမှ ဖျက်ရန်
            if (Schema::hasColumn('asset_maintenances', 'asset_id')) {
                $table->dropForeign(['asset_id']);
                $table->dropColumn('asset_id');
            }
            
            // asset_item_id မရှိသေးမှသာ အသစ်ထည့်ရန် (nullable ထည့်ထားခြင်းဖြင့် ဒေတာ Error မတက်စေပါ)
            if (!Schema::hasColumn('asset_maintenances', 'asset_item_id')) {
                $table->foreignId('asset_item_id')->nullable()->after('id')->constrained('asset_items')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asset_maintenances', function (Blueprint $table) {
            if (Schema::hasColumn('asset_maintenances', 'asset_item_id')) {
                $table->dropForeign(['asset_item_id']);
                $table->dropColumn('asset_item_id');
            }
        });
    }
};