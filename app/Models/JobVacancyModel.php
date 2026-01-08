<?php

namespace App\Models;

use CodeIgniter\Model;

class JobVacancyModel extends Model
{
    protected $table = 'job_vacancies';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'position',
        'department',
        'description',
        'requirements'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
