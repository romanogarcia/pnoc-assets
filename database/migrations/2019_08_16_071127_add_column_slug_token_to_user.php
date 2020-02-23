<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSlugTokenToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("users", function ($table) {
            if(!Schema::hasColumn('users', 'slug_token')){
                $table->string('slug_token')->after('is_locked')->unique(); 
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
        Schema::table("users", function ($table) {
            if(Schema::hasColumn('users', 'slug_token')){
                $table->dropColumn('slug_token'); // delete column
            }
        });
    }
}
