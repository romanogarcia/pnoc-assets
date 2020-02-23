<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUsedByHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("assets", function ($table) {
            if(!Schema::hasColumn('assets', 'used_by_history')){
                $table->text('used_by_history')->after('warranty')->nullable();
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
        Schema::table("assets", function ($table) {
            if(Schema::hasColumn('assets', 'used_by_history')){
                $table->dropColumn('used_by_history')->nullable(); // delete column
            }
        });
    }
}
