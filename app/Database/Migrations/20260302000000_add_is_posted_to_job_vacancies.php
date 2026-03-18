<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class AddIsPostedToJobVacancies extends Migration
{
    public function up()
    {
        $this->forge->addColumn('job_vacancies', [
            'is_posted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '0 = Draft, 1 = Posted'
            ]
        ]);
        
        // Update existing records to be posted
        $this->db->table('job_vacancies')->update(['is_posted' => 1]);
    }

    public function down()
    {
        $this->forge->dropColumn('job_vacancies', 'is_posted');
    }
}
