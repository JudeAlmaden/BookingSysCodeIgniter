<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScheduledStopsTable extends Migration
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
            'scheduled_route_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'stop_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'ETA' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'on_update' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('scheduled_route_id', 'scheduledRoutes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('stop_id', 'route_stops', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('scheduledStops');
    }

    public function down()
    {
        $this->forge->dropTable('scheduledStops');
    }
}
