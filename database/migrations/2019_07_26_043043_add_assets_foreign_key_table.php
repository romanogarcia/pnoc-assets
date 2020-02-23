<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssetsForeignKeyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assets', function(Blueprint $table) {
            $table->foreign('added_by')->references('id')->on('employees')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('accountable_employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict')->onUpdate('cascade');
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
            $table->dropForeign(['added_by']); // fk first
            $table->dropForeign(['accountable_employee_id']); // fk first
            $table->dropForeign(['location_id']); // fk first$table->dropForeign(['user_id']); // fk first
            $table->dropForeign(['category_id']); // fk first
            $table->dropForeign(['supplier_id']); // fk first
            $table->dropForeign(['department_id']); // fk first

            $table->dropColumn('added_by'); // then column
            $table->dropColumn('accountable_employee_id'); // then column
            $table->dropColumn('location_id'); // then column
            $table->dropColumn('category_id'); // then column
            $table->dropColumn('supplier_id'); // then column
            $table->dropColumn('department_id'); // then column
        });
    }
}
