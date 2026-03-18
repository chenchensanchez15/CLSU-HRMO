<?php

namespace App\Models;

use CodeIgniter\Model;

class JobVacancyModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'job_vacancies';
    protected $primaryKey = 'id_vacancy';
    protected $allowedFields = [
        'publication_id',
        'plantilla_item_id',
        'date_posted',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get job vacancy with complete details including plantilla item information
     *
     * @param int $id
     * @return array|null
     */
    public function getVacancyWithDetails($id)
    {
        $builder = $this->db->table('job_vacancies jv');
        $builder->select([
            'jv.id_vacancy',
            'jv.publication_id',
            'jv.plantilla_item_id',
            'jv.date_posted',
            'jv.created_at',
            'jp.interview_date',
            'jp.interview_venue',
            'jp.publication_status',
            'jp.type as publication_type',
            'jp.hr_head',
            'jp.application_deadline',
            'pi.item_number',
            'pi.xItemTitle as position_title',
            'pi.ItemSalaryGrade as salary_grade',
            'pos.position_name',
            'o.office_name',
            'd.division_name as department_name'
        ]);
        $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
        $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
        $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
        $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
        $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
        $builder->where('jv.id_vacancy', $id);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Get all vacancies with details
     *
     * @return array
     */
    public function getAllVacanciesWithDetails()
    {
        $builder = $this->db->table('job_vacancies jv');
        $builder->select([
            'jv.id_vacancy',
            'jv.publication_id',
            'jv.plantilla_item_id',
            'jv.date_posted',
            'jv.created_at',
            'jp.interview_date',
            'jp.interview_venue',
            'jp.publication_status',
            'jp.type as publication_type',
            'jp.hr_head',
            'jp.application_deadline',
            'pi.item_number',
            'pi.xItemTitle as position_title',
            'pi.ItemSalaryGrade as salary_grade',
            'pos.position_name',
            'o.office_name',
            'd.division_name as department_name'
        ]);
        $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
        $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
        $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
        $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
        $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
        $builder->orderBy('jv.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}
