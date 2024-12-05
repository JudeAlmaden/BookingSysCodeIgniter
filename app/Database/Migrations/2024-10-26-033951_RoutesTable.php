<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RoutesTable extends Migration
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
                'unique'     => true, 
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
        $this->forge->createTable('routes');

    }

    public function down()
    {
        $this->forge->dropTable('routes');
    }
}
