<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('property_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('item_description')->nullable();
            $table->string('acquisition_cost')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->string('mr_number')->nullable();
            $table->string('asset_number')->nullable();
            $table->string('po_number')->nullable();
            $table->string('report_of_waste_material')->nullable();
            $table->string('disposal_number')->nullable();
            $table->string('warranty')->nullable();
            $table->string('slug_token')->unique();

            $table->unsignedBigInteger('added_by')->index();
            $table->unsignedBigInteger('department_id')->index();
            $table->unsignedBigInteger('accountable_employee_id')->index();
            $table->unsignedBigInteger('location_id')->index();
            $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('supplier_id')->index();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assets');
    }
}
