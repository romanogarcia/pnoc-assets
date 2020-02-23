<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnLocationIdInUploadedDataDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uploaded_data_details', function (Blueprint $table) {
            if(!Schema::hasColumn('uploaded_data_details', 'location_id')){
                $table->unsignedBigInteger('location_id')->after('uploaded_by')->nullable();
                $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade')->onUpdate('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uploaded_data_details', function (Blueprint $table) {
            if(Schema::hasColumn('uploaded_data_details', 'location_id')){
                $table->dropForeign(['location_id']); // fk first
                $table->dropColumn('location_id'); // then column
            }
        });
    }
}
