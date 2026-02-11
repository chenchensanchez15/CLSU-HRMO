<?php

namespace App\Models;

use CodeIgniter\Model;

class DegreeLevelModel extends Model
{
    protected $table = 'lib_degree_level';  // your table name
    protected $primaryKey = 'id_degree_level';
    protected $allowedFields = [
        'degree_level_name'
    ];
}
