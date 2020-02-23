<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnEmployeeNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("employees", function ($table) {
            if(!Schema::hasColumn('employees', 'employee_no')){
                $table->string('employee_no')->after('id')->unique(); 
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
        Schema::table("employees", function ($table) {
            if(Schema::hasColumn('employees', 'employees')){
                $table->dropColumn('employees'); // delete column
            }
        });
    }
}
