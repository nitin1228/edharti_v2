<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresentStatusItemTableSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'group_id'    => 17013,
                'item_code'   => 'ENC',
                'item_name'   => 'Encroachment',
                'color_code'  => null,
                'item_order'  => 1,
                'is_active'   => true,
            ],
            [
                'group_id'    => 17013,
                'item_code'   => 'OCC',
                'item_name'   => 'Occupied',
                'color_code'  => null,
                'item_order'  => 2,
                'is_active'   => true,
            ],
            [
                'group_id'    => 17013,
                'item_code'   => 'VAC',
                'item_name'   => 'Vacant',
                'color_code'  => null,
                'item_order'  => 3,
                'is_active'   => true,
            ],
            [
                'group_id'    => 17013,
                'item_code'   => 'UNA',
                'item_name'   => 'Unauthorised',
                'color_code'  => null,
                'item_order'  => 4,
                'is_active'   => true,
            ],
        ];

        foreach ($items as $item) {
            DB::table('items')->updateOrInsert(
                ['item_code' => $item['item_code']], // Unique key
                array_merge($item, [
                    'created_at'  => Carbon::now(),
                    'created_by'  => null,
                    'updated_at'  => Carbon::now(),
                    'updated_by'  => null,
                ])
            );
        }
    }
}
