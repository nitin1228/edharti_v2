<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddStatusAndRemarksToItCellRegTransferHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get the ID of item_code "RS_NEW" from items table
        $rsNewItemId = DB::table('items')->where('item_code', 'RS_NEW')->value('id');
        
        Schema::table('it_cell_reg_transfer_histories', function (Blueprint $table) use ($rsNewItemId) {
            // Add status column after section_code
            if (!Schema::hasColumn('it_cell_reg_transfer_histories', 'status')) {
                $table->unsignedBigInteger('status')
                      ->nullable()
                      ->default($rsNewItemId)
                      ->after('section_code')
                      ->comment('Reference to items table status ID (default: RS_NEW)');
            }
            
            // Add remarks column after status
            if (!Schema::hasColumn('it_cell_reg_transfer_histories', 'remarks')) {
                $table->text('remarks')
                      ->nullable()
                      ->default(null)
                      ->after('status')
                      ->comment('Remarks for the transfer action');
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
        Schema::table('it_cell_reg_transfer_histories', function (Blueprint $table) {
            if (Schema::hasColumn('it_cell_reg_transfer_histories', 'status')) {
                $table->dropColumn('status');
            }
            
            if (Schema::hasColumn('it_cell_reg_transfer_histories', 'remarks')) {
                $table->dropColumn('remarks');
            }
        });
    }
}