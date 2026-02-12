<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTypeColumnsToApplicationPersonal extends Migration
{
    public function up()
    {
        $fields = [
            'clsu_employee_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'is_clsu_employee'
            ],
            'pwd_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'is_pwd'
            ]
        ];
        
        $this->forge->addColumn('application_personal', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('application_personal', ['clsu_employee_type', 'pwd_type']);
    }
}