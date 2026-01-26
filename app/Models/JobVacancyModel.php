<?php

namespace App\Models;

use CodeIgniter\Model;

class JobVacancyModel extends Model
{
    protected $table = 'job_vacancies'; // Correct table
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'plantilla_item_no',
        'position_title',
        'status',
        'office',
        'salary_grade',
        'monthly_salary',
        'education',
        'training',
        'experience',
        'eligibility',
        'competency',
        'duties_responsibilities',
        'application_requirements',
        'description',
        'department',
        'employee_type',        // THIS column exists
        'application_deadline',
        'is_posted',
        'posted_at',
        'created_at',
        'updated_at'
    ];
}
