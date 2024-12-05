<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Payments extends Migration
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
            'booking_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',  
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'Pending', 
            ],
            'transaction_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,  
            ],
            'payment_proof' => [
                'type' => 'MEDIUMBLOB',  // or 'LONGBLOB'
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        // Primary Key and Foreign Key
        $this->forge->addKey('id', true);         
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('booking_id', 'bookings', 'id', 'CASCADE', 'CASCADE');

        // Create the table
        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}
