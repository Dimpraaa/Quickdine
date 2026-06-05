<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('table_id')->nullable()->change();

            $table->enum('order_type', ['dine_in', 'take_away'])->default('dine_in')->after('table_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('table_id')->nullable(false)->change();
            $table->dropColumn('order_type');
        });
    }
};
