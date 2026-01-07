<?php

namespace App\Models;

use CodeIgniter\Model;

class JobPositionModel extends Model
{
    protected $table = 'job_positions'; // your database table
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'position_title', 'office', 'item_no', 'salary_grade', 'monthly_salary',
        'education', 'training', 'experience', 'eligibility', 'competency',
        'duties_responsibilities', 'application_requirements', 'application_deadline',
        'description', 'department', 'employment_type', 'status', 'created_at'
    ];
}
