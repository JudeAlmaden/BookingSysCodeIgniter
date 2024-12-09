<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Schedules extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'vehicle_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'trip_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'ETA' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'stop_index' => [  
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'reservations' => [  
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'stop_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'distance' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        $this->forge->addForeignKey('vehicle_id', 'vehicles', 'id', 'CASCADE', 'CASCADE');  // Foreign key
        $this->forge->createTable('schedules');
    }        

    public function down()
    {
        $this->forge->dropTable('schedules');
    }
}
