<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'      => 'admin',
            'email'     => 'admin@gmail.com',
            'phone_no'  => '1234567890', // Adjust as necessary
            'password'  => password_hash('root1234', PASSWORD_DEFAULT),
            'priviledge'=> 'admin',
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s'),
        ];

        // Using the query builder
        $this->db->table('users')->insert($data);
    }
}
