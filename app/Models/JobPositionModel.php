<?php

namespace App\Models;

use CodeIgniter\Model;

class JobPositionModel extends Model
{
    // This model is deprecated but kept for backward compatibility.
    // The new job vacancy system uses separate tables: job_vacancies, job_publications, and plantilla_items.
    
    protected $table = 'job_vacancies';
    protected $primaryKey = 'id_vacancy';
    protected $useTimestamps = false;
    protected $skipValidation = true;
    
    // This model should not be actively used anymore.
    // Use JobVacancyModel, PlantillaItemModel, and JobPublicationModel instead.
}
