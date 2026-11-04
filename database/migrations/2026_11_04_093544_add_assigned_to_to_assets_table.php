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
        $table->unsignedBigInteger('assigned_to')->nullable()->after('warehouse_id');
        $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('assets', function (Blueprint $table) {
        $table->dropForeign(['assigned_to']);
        $table->dropColumn('assigned_to');
    });
}

};
