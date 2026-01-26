<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationFamModel extends Model
{
    protected $table = 'application_fam';
    protected $primaryKey = 'id_application_fam';

    protected $allowedFields = [
        'job_application_id',
        'first_name',
        'last_name',
        'middle_name',
        'extension',
        'relationship',
        'occupation',
        'contact_no'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
