<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantModel extends Model
{
    protected $table = 'applicants';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'contact',
        'photo',
        'address'
    ];
}
