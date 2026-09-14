<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class PresentCustodianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('present_custodians')->truncate();
        Schema::enableForeignKeyConstraints();

        $custodians = [
            ['item_code' => 'GOI', 'item_name' => 'Government of India'],
            ['item_code' => 'GOL', 'item_name' => 'Government Land'],
            ['item_code' => 'LDO', 'item_name' => 'L&DO'],
            ['item_code' => 'DGSD', 'item_name' => 'DGS&D'],
            ['item_code' => 'GUDA', 'item_name' => 'Gulbarga Urban Development Authority'],
            ['item_code' => 'HVOC', 'item_name' => 'Hindustan Vegetable & Oil Corporation (HVOC)'],
            ['item_code' => 'HMT', 'item_name' => 'HMT'],
            ['item_code' => 'KSHB', 'item_name' => 'Kerala State Housing Board'],
            ['item_code' => 'TCL', 'item_name' => 'TCL'],
            ['item_code' => 'SPD', 'item_name' => 'Salt Pan Department'],
            ['item_code' => 'STD', 'item_name' => 'Stationery Department'],
            ['item_code' => 'DMRC', 'item_name' => 'DMRC'],
            ['item_code' => 'DDA', 'item_name' => 'Delhi Development Authority'],
            ['item_code' => 'MUD', 'item_name' => 'Ministry of Urban Development'],
            ['item_code' => 'HPI', 'item_name' => 'Hemisphere Properties India Ltd'],
            ['item_code' => 'GOJ', 'item_name' => 'Government of Jharkhand'],
            ['item_code' => 'PPP', 'item_name' => 'Purchased from Private Property'],
            ['item_code' => 'CGOV', 'item_name' => 'Central Government'],
            ['item_code' => 'GOTN', 'item_name' => 'Government of Tamil Nadu'],
            ['item_code' => 'RDOC', 'item_name' => 'Revenue Divisional Officer Coimbatore'],
            ['item_code' => 'SGOV', 'item_name' => 'State Government'],
            ['item_code' => 'DPIIT', 'item_name' => 'DPIIT'],
            ['item_code' => 'NAZL', 'item_name' => 'Nazul Land'],
            ['item_code' => 'NAC', 'item_name' => 'NAC'],
            ['item_code' => 'DOP', 'item_name' => 'Directorate of Printing'],
            ['item_code' => 'UNK', 'item_name' => 'Not Known'],
            ['item_code' => 'OTH', 'item_name' => 'Other'],
        ];

        foreach ($custodians as $custodian) {
            DB::table('present_custodians')->insert([
                'item_code' => $custodian['item_code'],
                'item_name' => $custodian['item_name'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
