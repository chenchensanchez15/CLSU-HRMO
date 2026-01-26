<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationDocumentsModel extends Model
{
    protected $table = 'application_documents';
    protected $primaryKey = 'id_application_document';

    protected $allowedFields = [
        'job_application_id',
        'resume',
        'tor',
        'diploma',
        'certificate',
        'uploaded_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
