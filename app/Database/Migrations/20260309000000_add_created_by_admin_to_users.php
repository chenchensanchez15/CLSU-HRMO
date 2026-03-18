<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedByToUsers extends Migration
{
    public function up()
    {
        // Add created_by column to users table
        $this->forge->addColumn('users', [
            'created_by' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '0=User registered (login page), 1=Created by admin',
                'after' => 'first_login'
            ]
        ]);
        
        // Add index for better query performance
        $this->forge->addKey('created_by');
        $this->forge->processIndexes('users');
    }

    public function down()
    {
        // Remove the column
        $this->forge->dropColumn('users', 'created_by');
    }
}
