<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('designations')->truncate();
     /*   $designations = [
            ['name' => 'DEPUTY L&amp;DO', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ASSISTANT DIRECTOR (OFFICAL LANGUAGES)', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SUPERINTENDENT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SECTION OFFICER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ASSISTANT SECTION OFFICER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SENIOR SECRETARIAT ASSISTANT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SECRETARIAT ASSISTANT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'JUNIOR SECRETARIAT ASSISTANT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'LOWER DIVISION CLERK', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'STENO GRADE-D', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'DATA ENTRY OPERATOR', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ASSISTANT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'CONSULTANT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

         ]; */
            $designations = [
            ['name' => 'DEPUTY L&amp;DO', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ASSISTANT DIRECTOR (OFFICAL LANGUAGES)', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SUPERINTENDENT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SECTION OFFICER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ASSISTANT SECTION OFFICER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SENIOR SECRETARIAT ASSISTANT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SECRETARIAT ASSISTANT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'JUNIOR SECRETARIAT ASSISTANT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'LOWER DIVISION CLERK', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'STENO GRADE-D', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'DATA ENTRY OPERATOR', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ASSISTANT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'CONSULTANT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'L&amp;DO', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ENGINEER OFFICER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ASSISTANT ENGINEER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'JUNIOR ENGINEER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'ACCOUNT OFFICER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'CDV', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'CDN', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'AUDIT CELL', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'VEGILLENCE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'IT CELL', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'SUB REGISTRAR', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];
        DB::table('designations')->insert($designations);
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
