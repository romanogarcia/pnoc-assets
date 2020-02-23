<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadedData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploaded_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('uploaded_by')->index()->nullable(); //employee_id
            $table->string('file');
            $table->string('file_name')->nullable();
            $table->string('file_extension')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('employees')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploaded_data');
    }
}
