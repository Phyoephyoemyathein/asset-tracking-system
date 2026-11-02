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
        Schema::table('assets', function (Blueprint $table) {
        $table->integer('min_stock_level')->default(5)->after('quantity'); // ပုံသေ အနေဖြင့် ၅ ခုအောက်ရောက်လျှင် သတိပေးရန်
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
        $table->dropColumn('min_stock_level');
    });
    }
};
