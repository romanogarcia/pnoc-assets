<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadedDataDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploaded_data_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('uploaded_by')->index()->nullable(); //employee_id
            $table->unsignedBigInteger('uploaded_data_id')->index()->nullable();
            $table->unsignedBigInteger('asset_id')->index()->nullable();
            $table->string('barcode');
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('employees')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('uploaded_data_id')->references('id')->on('uploaded_data')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploaded_data_details');
    }
}
