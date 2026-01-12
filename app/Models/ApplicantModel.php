<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantModel extends Model
{
    protected $table = 'applicant_profiles';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id','first_name','middle_name','last_name','suffix','date_of_birth','place_of_birth',
        'sex','civil_status','citizenship','height','weight','blood_type',
        'phone','email','residential_address','permanent_address',
        'education','training','experience','eligibility','competency','photo','resume'
    ];
}
 