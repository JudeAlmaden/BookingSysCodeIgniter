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
                'auto_increment' => true,
            ],
            'route_id'        => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'name'        => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'distance'        => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null' => false,
            ],
            'index'        => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null' => false,
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
        $this->forge->createTable('route_stops');
        $this->forge->addForeignKey('route_id', 'routes', 'id', 'CASCADE', 'CASCADE');
    }

public function down()
{
    $this->forge->dropTable('route_stops');
}
}

