<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVehiclesTable extends Migration
{
    public function up(){
    $this->forge->addField([
        'id'          => [
            'type'           => 'INT',
            'constraint'     => 11,
            'unsigned'       => true,
            'auto_increment' => true,
        ],
        'tag'        => [
            'type'       => 'VARCHAR',
            'constraint' => '120',
            'unique'     => true, 
        ],
        'type'        => [
            'type'       => 'VARCHAR',
            'constraint' => '120',
        ],
        'description'        => [
            'type'       => 'VARCHAR',
            'constraint' => '120',
        ],
        'number_seats'       => [
            'type'           => 'INT',
            'constraint'     => 11,
        ],
        'base_fare'       => [
            'type'           => 'INT',
            'constraint'     => 11,
        ],
        'per_kilometer'       => [
            'type'           => 'INT',
            'constraint'     => 11,
        ],
        'status'        => [
            'type'       => 'VARCHAR',
            'constraint' => '120',
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
    $this->forge->createTable('vehicles');
}

public function down()
{
    $this->forge->dropTable('vehicles');
}
}
