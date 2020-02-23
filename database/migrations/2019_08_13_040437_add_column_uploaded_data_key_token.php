<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUploadedDataKeyToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("uploaded_data", function ($table) {
            if(!Schema::hasColumn('uploaded_data', 'slug_token')){
                $table->string('slug_token')->after('description')->unique(); 
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
        Schema::table("uploaded_data", function ($table) {
            if(Schema::hasColumn('uploaded_data', 'slug_token')){
                $table->dropColumn('slug_token'); // delete column
            }
        });
    }
}
