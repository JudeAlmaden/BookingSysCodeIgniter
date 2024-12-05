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
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'trip_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'distance' => [  
                'type'       => 'INT',
                'constraint' => 11,

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
                'null' => false,
            ],
            'updated_at'  => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
    
        $this->forge->addKey('id', true);  // Primary key
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bookings');
    }        

    public function down()
    {
        $this->forge->dropTable('bookings');
    }
}
