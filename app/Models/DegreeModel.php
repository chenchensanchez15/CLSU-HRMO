<?php

namespace App\Models;

use CodeIgniter\Model;

class DegreeModel extends Model
{
    protected $table = 'lib_degrees';      // your table name
    protected $primaryKey = 'id_degree';   // primary key
    protected $allowedFields = [
        'degree_name',
        'degree_level_id'
    ];
}
