<?php

namespace Database\Seeders;

use App\Models\FloorAreaRatio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FloorAreaRatioSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            /*
            |--------------------------------------------------------------------------
            | Up to 31.07.1990
            |--------------------------------------------------------------------------
            | Area Unit: Square Yards
            */

            [
                'effective_from'  => null,
                'effective_to'    => '1990-07-31',
                'area_from'       => 1,
                'area_to'         => 100,
                'area_unit'       => 'sq_yd',
                'far'             => 150,
                'ground_coverage' => null,
            ],
            [
                'effective_from'  => null,
                'effective_to'    => '1990-07-31',
                'area_from'       => 101,
                'area_to'         => 600,
                'area_unit'       => 'sq_yd',
                'far'             => 100,
                'ground_coverage' => null,
            ],
            [
                'effective_from'  => null,
                'effective_to'    => '1990-07-31',
                'area_from'       => 600,
                'area_to'         => null,
                'area_unit'       => 'sq_yd',
                'far'             => 75,
                'ground_coverage' => null,
            ],

            /*
            |--------------------------------------------------------------------------
            | 01.08.1990 - 22.07.1998
            |--------------------------------------------------------------------------
            */

            [
                'effective_from' => '1990-08-01',
                'effective_to' => '1998-07-22',
                'area_from' => 0,
                'area_to' => 50,
                'area_unit' => 'sq_mt',
                'far' => 150,
                'ground_coverage' => 75,
            ],
            [
                'effective_from' => '1990-08-01',
                'effective_to' => '1998-07-22',
                'area_from' => 50,
                'area_to' => 100,
                'area_unit' => 'sq_mt',
                'far' => 180,
                'ground_coverage' => 75,
            ],
            [
                'effective_from' => '1990-08-01',
                'effective_to' => '1998-07-22',
                'area_from' => 100,
                'area_to' => 250,
                'area_unit' => 'sq_mt',
                'far' => 160,
                'ground_coverage' => 66.66,
            ],
            [
                'effective_from' => '1990-08-01',
                'effective_to' => '1998-07-22',
                'area_from' => 250,
                'area_to' => 500,
                'area_unit' => 'sq_mt',
                'far' => 140,
                'ground_coverage' => 50,
            ],
            [
                'effective_from' => '1990-08-01',
                'effective_to' => '1998-07-22',
                'area_from' => 500,
                'area_to' => 1000,
                'area_unit' => 'sq_mt',
                'far' => 100,
                'ground_coverage' => 40,
            ],
            [
                'effective_from' => '1990-08-01',
                'effective_to' => '1998-07-22',
                'area_from' => 1000,
                'area_to' => null,
                'area_unit' => 'sq_mt',
                'far' => 83,
                'ground_coverage' => 33.33,
            ],

            /*
            |--------------------------------------------------------------------------
            | 23.07.1998 - 06.02.2007
            |--------------------------------------------------------------------------
            */

            [
                'effective_from' => '1998-07-23',
                'effective_to' => '2007-02-06',
                'area_from' => 0,
                'area_to' => 32,
                'area_unit' => 'sq_mt',
                'far' => 225,
                'ground_coverage' => 75,
            ],
            [
                'effective_from' => '1998-07-23',
                'effective_to' => '2007-02-06',
                'area_from' => 32,
                'area_to' => 50,
                'area_unit' => 'sq_mt',
                'far' => 225,
                'ground_coverage' => 75,
            ],
            [
                'effective_from' => '1998-07-23',
                'effective_to' => '2007-02-06',
                'area_from' => 50,
                'area_to' => 100,
                'area_unit' => 'sq_mt',
                'far' => 225,
                'ground_coverage' => 75,
            ],
            [
                'effective_from' => '1998-07-23',
                'effective_to' => '2007-02-06',
                'area_from' => 100,
                'area_to' => 250,
                'area_unit' => 'sq_mt',
                'far' => 200,
                'ground_coverage' => 66.66,
            ],
            [
                'effective_from' => '1998-07-23',
                'effective_to' => '2007-02-06',
                'area_from' => 250,
                'area_to' => 500,
                'area_unit' => 'sq_mt',
                'far' => 150,
                'ground_coverage' => 50,
            ],
            [
                'effective_from' => '1998-07-23',
                'effective_to' => '2007-02-06',
                'area_from' => 500,
                'area_to' => 1000,
                'area_unit' => 'sq_mt',
                'far' => 120,
                'ground_coverage' => 40,
            ],
            [
                'effective_from' => '1998-07-23',
                'effective_to' => '2007-02-06',
                'area_from' => 1000,
                'area_to' => null,
                'area_unit' => 'sq_mt',
                'far' => 100,
                'ground_coverage' => 33.33,
            ],

            /*
            |--------------------------------------------------------------------------
            | 07.02.2007 Onwards
            |--------------------------------------------------------------------------
            */

            [
                'effective_from' => '2007-02-07',
                'effective_to' => null,
                'area_from' => 0,
                'area_to' => 32,
                'area_unit' => 'sq_mt',
                'far' => 350,
                'ground_coverage' => 90,
            ],
            [
                'effective_from' => '2007-02-07',
                'effective_to' => null,
                'area_from' => 32,
                'area_to' => 50,
                'area_unit' => 'sq_mt',
                'far' => 350,
                'ground_coverage' => 90,
            ],
            [
                'effective_from' => '2007-02-07',
                'effective_to' => null,
                'area_from' => 50,
                'area_to' => 100,
                'area_unit' => 'sq_mt',
                'far' => 350,
                'ground_coverage' => 90,
            ],
            [
                'effective_from' => '2007-02-07',
                'effective_to' => null,
                'area_from' => 100,
                'area_to' => 250,
                'area_unit' => 'sq_mt',
                'far' => 300,
                'ground_coverage' => 75,
            ],
            [
                'effective_from' => '2007-02-07',
                'effective_to' => null,
                'area_from' => 250,
                'area_to' => 750,
                'area_unit' => 'sq_mt',
                'far' => 225,
                'ground_coverage' => 75,
            ],
            [
                'effective_from' => '2007-02-07',
                'effective_to' => null,
                'area_from' => 750,
                'area_to' => 1000,
                'area_unit' => 'sq_mt',
                'far' => 150,
                'ground_coverage' => 50,
            ],
            [
                'effective_from' => '2007-02-07',
                'effective_to' => null,
                'area_from' => 1000,
                'area_to' => null,
                'area_unit' => 'sq_mt',
                'far' => 120,
                'ground_coverage' => 40,
            ],
        ];

        foreach ($data as $row) {
            FloorAreaRatio::create($row);
        }
    }
}
