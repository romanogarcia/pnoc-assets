<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnConditionAndAccountingTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            if(!Schema::hasColumn('assets', 'accounting_tag')){
                $table->string('accounting_tag')->nullable()->after('po_number'); // add column
            }
            if(!Schema::hasColumn('assets', 'condition')){
                $table->string('condition')->nullable()->after('accounting_tag'); // add column
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
        Schema::table('assets', function (Blueprint $table) {
            if(Schema::hasColumn('assets', 'accounting_tag')){
                $table->dropColumn('accounting_tag'); // delete column
            }
            if(Schema::hasColumn('assets', 'condition')){
                $table->dropColumn('condition'); // delete column
            }
        });
    }
}
