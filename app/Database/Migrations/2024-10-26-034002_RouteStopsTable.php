<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RouteStopsTable extends Migration
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
            'route'        => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'name'        => [
                'type'       => 'VARCHAR',
                'constraint' => '120',
                'null' => false,
            ],
            'distance'        => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'index'        => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
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
        $this->forge->createTable('route_stops');
        $this->forge->addForeignKey('route', 'routes', 'id', 'CASCADE', 'CASCADE');
    }

public function down()
{
    $this->forge->dropTable('route_stops');
}
}

