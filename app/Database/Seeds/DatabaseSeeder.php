<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Call each seeder class
        $this->call('UserSeeder');
        // Add other seeders here
    }
}
