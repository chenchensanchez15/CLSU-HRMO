<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantTrainingModel extends Model
{
    protected $table = 'applicant_trainings';
    protected $primaryKey = 'id_applicant_training';

    protected $allowedFields = [
        'user_id',
        'training_category_id',
        'training_name',
        'training_venue',           
        'date_from',
        'date_to',
        'training_facilitator',
        'training_hours',
        'training_sponsor',
        'training_remarks',
        'certificate_file',
        'added_date',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
