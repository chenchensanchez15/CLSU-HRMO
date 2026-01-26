<?php

namespace App\Models;

use CodeIgniter\Model;

class JobPositionModel extends Model
{
    // Database table
    protected $table = 'job_vacancies';

    // Primary key
    protected $primaryKey = 'id';

    // Fields that are allowed to be inserted/updated
    protected $allowedFields = [
        'plantilla_item_no',          // Item number / plantilla
        'position_title',             // Job title
        'status',                     // Status
        'office',                     // Office or unit
        'salary_grade',               // Salary grade
        'monthly_salary',             // Monthly salary
        'education',                  // Required education
        'training',                   // Required training
        'experience',                 // Required experience
        'eligibility',                // Eligibility
        'competency',                 // Competency
        'duties_responsibilities',    // Duties & responsibilities
        'application_requirements',   // Application requirements
        'description',                // Short description
        'department',                 // Department
        'employee_type',              // Employment type (Permanent, Contractual, etc.)
        'application_deadline',       // Deadline for application
        'is_posted',                  // 0 = Vacant, 1 = Posted
        'posted_at',                  // When posted
        'created_at',                 // Record created
        'updated_at'                  // Record updated
    ];

    // Optional: Enable timestamps if your table has created_at and updated_at
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
