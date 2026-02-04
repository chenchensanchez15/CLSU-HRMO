<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantFamModel extends Model
{
    protected $table = 'applicant_fam';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'extension',
        'relationship',
        'occupation',
        'contact_no',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true; // auto-manage created_at and updated_at
}
