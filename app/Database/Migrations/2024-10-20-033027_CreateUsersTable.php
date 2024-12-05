<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'name'        => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'email'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'phone_no'    => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'password'    => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'privilege'    => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'default'    => 'user',
            ],
            'created_at'  => [
                'type'       => 'DATETIME',
                'null' => false,
            ],
            'updated_at'  => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);  // Primary key
        $this->forge->createTable('users');


    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
