<?php

namespace App\Models;

use CodeIgniter\Model;

class JobPublicationModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'job_publications';
    protected $primaryKey = 'id_publication';
    protected $useAutoIncrement = true;
    
    protected $allowedFields = [
        'interview_date',
        'interview_venue',
        'publication_status',
        'request_date',
        'type',
        'hr_head',
        'hr_head_position',
        'application_deadline',
        'remarks',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}