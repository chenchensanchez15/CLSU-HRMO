<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantFamModel extends Model
{
    protected $table = 'applicant_fam';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'job_application_id',
        'spouse_surname', 'spouse_first_name', 'spouse_middle_name', 'spouse_ext_name',
        'spouse_occupation', 'spouse_contact',
        'father_surname', 'father_first_name', 'father_middle_name', 'father_ext_name',
        'mother_maiden_surname', 'mother_first_name', 'mother_middle_name'
    ];
}
