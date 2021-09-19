<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('customer_title');
            $table->string('customer_email');
            $table->string('pdf_link')->nullable();
            $table->double('total', 10, 2);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
        });

        Schema::create('proposal_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proposal_id');
            $table->unsignedBigInteger('car_id');
            $table->string('title');
            $table->double('quantity', 5, 2);
            $table->double('price', 10, 2);
            $table->double('total', 10, 2);
            $table->timestamps();

            $table->foreign('car_id')->references('id')->on('cars');
            $table->foreign('proposal_id')->references('id')->on('proposals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proposals');
        Schema::dropIfExists('proposals_items');
    }
}
