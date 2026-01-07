<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'first_name', 'middle_name', 'last_name', 'extension', 'email', 'password', 'created_at'
    ];
    protected $useTimestamps = true; // automatically sets created_at
    protected $createdField  = 'created_at';
}
