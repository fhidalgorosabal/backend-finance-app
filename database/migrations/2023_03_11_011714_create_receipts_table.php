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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('concept_id');
            $table->string('description', 150)->nullable();
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('currency_id');
            $table->decimal('actual_amount', 10, 2);
            $table->timestamps();

            $table->foreign('concept_id')->references('id')->on('concepts');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipts');
    }
};
