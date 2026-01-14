<?php

namespace App\Models;
use CodeIgniter\Model;

class ApplicationModel extends Model
{
    protected $table = 'applications';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'vacancy_id', 'last_name', 'first_name', 'middle_name', 'name_extension',
        'birth_date', 'place_of_birth', 'sex', 'civil_status', 'citizenship'
    ];
}
