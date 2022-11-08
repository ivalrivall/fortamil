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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('total_amount', 16, 2)->default(0);
            $table->decimal('outstanding_amount', 16, 2)->default(0);
            $table->unsignedBigInteger('payment_method_id')->nullable();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount', 16, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['total_amount','outstanding_amount','payment_method_id']);
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['amount']);
        });
    }
};
