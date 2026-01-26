<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantDocumentsModel extends Model
{
    protected $table      = 'applicant_documents';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'resume', 'tor', 'diploma', 'certificate', 'uploaded_at'];
    protected $useTimestamps = false; // we're using 'uploaded_at' manually
}
