<?php

namespace App\Models;

use CodeIgniter\Model;

class TrainingCategoryModel extends Model
{
    protected $table = 'lib_training_category'; // ✅ matches your DB table
    protected $primaryKey = 'id_training_category';
    protected $allowedFields = ['training_category_name'];
}
