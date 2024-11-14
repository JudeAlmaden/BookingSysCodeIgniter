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
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name'        => [
                'type'       => 'VARCHAR',
                'constraint' => '120',
            ],
            'email'       => [
                'type'       => 'VARCHAR',
                'constraint' => '120',
            ],
            'phone_no'    => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'password'    => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'priviledge'    => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'default'    => 'user',
            ],
            'created_at'  => [
                'type'       => 'DATETIME',
                'null'       => true,
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
