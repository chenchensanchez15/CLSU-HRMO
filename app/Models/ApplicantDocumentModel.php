<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantDocumentModel extends Model
{
    protected $table = 'applicant_documents';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'application_id',
        'resume',
        'tor',
        'diploma',
        'certificate',
        'uploaded_at',
    ];
    protected $useTimestamps = false; 
}
