<?php

namespace App\Models;

use CodeIgniter\Model;

class JobApplicationModel extends Model
{
   protected $table = 'job_applications';
protected $primaryKey = 'id';
protected $allowedFields = [
    'user_id', 'job_position_id', 'application_status', 'applied_at',
    'resume', 'id_front', 'id_back', 'additional_id'
];
protected $useTimestamps = true;
protected $createdField  = 'created_at';
protected $updatedField  = 'updated_at';

}
