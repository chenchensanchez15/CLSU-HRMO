<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantTrainingModel extends Model
{
    protected $table = 'applicant_trainings';
    protected $primaryKey = 'id_applicant_training';
    protected $allowedFields = [
        'applicant_id',
        'training_id',
        'training_hours',
        'training_sponsor',
        'training_remarks',
        'training_certificate_file',
        'addeddate',
        'updated_by',
        'updated_date'
    ];

    // Optional helper to fetch trainings along with training name if you have a trainings table
    public function getApplicantTrainings($applicantId)
    {
        return $this->select('applicant_trainings.*, t.training_name')
                    ->join('trainings t', 't.id_training = applicant_trainings.training_id', 'left')
                    ->where('applicant_id', $applicantId)
                    ->orderBy('addeddate', 'DESC')
                    ->findAll();
    }
}
