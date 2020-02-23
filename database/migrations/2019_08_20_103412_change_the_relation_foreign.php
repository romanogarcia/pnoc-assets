<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTheRelationForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("assets", function ($table) {
            if(Schema::hasColumn('assets', 'added_by')){
                $table->dropForeign(['added_by']); //drop the foreign key first
            }

            $table->foreign('added_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
        });

        Schema::table("uploaded_data", function ($table) {
            if(Schema::hasColumn('uploaded_data', 'uploaded_by')){
                $table->dropForeign(['uploaded_by']); //drop the foreign key first
            }

            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });

        Schema::table("uploaded_data_details", function ($table) {
            if(Schema::hasColumn('uploaded_data_details', 'uploaded_by')){
                $table->dropForeign(['uploaded_by']); //drop the foreign key first
            }

            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
