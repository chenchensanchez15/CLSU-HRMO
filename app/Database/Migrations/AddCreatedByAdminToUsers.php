<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedByAdminToUsers extends Migration
{
    public function up()
    {
        // Add created_by_admin column to users table
        $this->forge->addColumn('users', [
            'created_by_admin' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '0=Self-registered, 1=Created by admin',
                'after' => 'first_login'
            ]
        ]);
        
        // Add index for better query performance
        $this->forge->addKey('created_by_admin');
        $this->forge->processIndexes('users');
    }

    public function down()
    {
        // Remove the column
        $this->forge->dropColumn('users', 'created_by_admin');
    }
}
