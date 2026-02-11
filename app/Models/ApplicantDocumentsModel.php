<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantDocumentsModel extends Model
{
    protected $table      = 'applicant_documents';
    protected $primaryKey = 'id';

    // Allowed fields must match your DB columns for insert/update
    protected $allowedFields = [
        'user_id',
        'pds',
        'performance_rating',
        'resume',
        'tor',
        'diploma',
        'uploaded_at',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = false; // using manual timestamps
}
