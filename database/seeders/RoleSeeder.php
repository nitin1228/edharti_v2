<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        /* $roles = [
            'super-admin',
            'admin',
            'user',
            'CDV',
            'CDN',
            'applicant',
            'section-officer',
            'supritendent',
            'assistent-section-officer',
            'deputy-lndo',
            'lndo',
            'engineer-officer',
            'AE',
            'JE',
            'AO',
            'audit-cell',
            'vegillence',
            'it-cell',
        ]; */
        $roles = [
            ['name' => 'super-admin', 'title' => 'Super Admin'],
            ['name' => 'admin', 'title' => 'Admin'],
            ['name' => 'user', 'title' => 'User'],
            ['name' => 'CDV', 'title' => 'CDN'],
            ['name' => 'CDN', 'title' => 'CDN'],
            ['name' => 'applicant', 'title' => 'Applicant'],
            ['name' => 'section-officer', 'title' => 'SO'],
            ['name' => 'supritendent', 'title' => 'Suptd.'],
            ['name' => 'assistent-section-officer', 'title' => 'ASO'],
            ['name' => 'deputy-lndo', 'title' => 'Dy. L&DO'],
            ['name' => 'lndo', 'title' => 'L&DO'],
            ['name' => 'engineer-officer', 'title' => 'EO'],
            ['name' => 'AE', 'title' => 'AE'],
            ['name' => 'JE', 'title' => 'JE'],
            ['name' => 'AO', 'title' => 'AO'],
            ['name' => 'audit-cell', 'title' => 'IAC'],
            ['name' => 'vegillence', 'title' => 'Vigilance'],
            ['name' => 'it-cell', 'title' => 'IT Cell'],
        ];
        foreach ($roles as $role) {
            Role::create($role); // Pass associative array directly
        }
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
