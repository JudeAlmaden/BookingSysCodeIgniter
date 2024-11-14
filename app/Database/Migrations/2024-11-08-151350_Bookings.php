<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Bookings extends Migration
{
    public function up()
    {
        
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'trip_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'distance' => [  
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'num_seats' => [  
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'price' => [  
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Pending', 
            ],
            'from'        => [
                'type'       => 'VARCHAR',
                'constraint' => '120',
                'null' => false,
            ],
            'to'        => [
                'type'       => 'VARCHAR',
                'constraint' => '120',
                'null' => false,
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
        $this->forge->createTable('bookings');
    }        

    public function down()
    {
        $this->forge->dropTable('bookings');
    }
}
