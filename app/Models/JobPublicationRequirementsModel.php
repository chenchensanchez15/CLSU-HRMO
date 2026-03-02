<?php

namespace App\Models;

use CodeIgniter\Model;

class JobPublicationRequirementsModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'job_publication_requirements';
    protected $primaryKey = 'id_requirement';
    protected $useAutoIncrement = true;
    
    protected $allowedFields = [
        'publication_id',
        'requirement_text',
        'sort_order',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get requirements for a specific publication
     */
    public function getRequirementsByPublication($publicationId)
    {
        return $this->where('publication_id', $publicationId)
                   ->where('is_active', 1)
                   ->orderBy('sort_order', 'ASC')
                   ->findAll();
    }
    
    /**
     * Get requirements for a job vacancy by joining through the relationships
     */
    public function getRequirementsByVacancy($vacancyId)
    {
        $builder = $this->db->table('job_publication_requirements jpr');
        $builder->select('jpr.*');
        $builder->join('job_vacancies jv', 'jpr.publication_id = jv.publication_id');
        $builder->where('jv.id_vacancy', $vacancyId);
        $builder->where('jpr.is_active', 1);
        $builder->orderBy('jpr.sort_order', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}