<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateHasPropertyInSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectionCodes = ['LS1', 'LS2A', 'LS2B', 'LS3', 'LS4', 'LS5', 'PS1', 'PS2', 'PS3', 'RPC'];

        DB::table('sections')
            ->whereIn('section_code', $sectionCodes)
            ->update(['has_property' => 1]);
    }
}
