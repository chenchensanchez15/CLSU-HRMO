<?php

namespace App\Controllers;

use App\Models\JobVacancyModel;
use App\Models\PlantillaItemModel;
use App\Models\JobPublicationModel;

class Jobs extends BaseController
{
    protected $jobVacancyModel;
    protected $plantillaItemModel;
    protected $jobPublicationModel;

    public function __construct()
    {
        $this->jobVacancyModel = new JobVacancyModel();
        $this->plantillaItemModel = new PlantillaItemModel();
        $this->jobPublicationModel = new JobPublicationModel();
    }

    public function index()
    {
        $builder = $this->jobVacancyModel->db->table('job_vacancies jv');
        $builder->select([
            'jv.id_vacancy as id',
            'jv.plantilla_item_id',
            'jv.date_posted',
            'jv.created_at',
            'jp.interview_date',
            'jp.interview_venue',
            'jp.publication_status',
            'jp.type as publication_type',
            'jp.hr_head',
            'jp.application_deadline',
            'jp.remarks',
            'pi.item_number as plantilla_item_no',
            'pi.xItemTitle as position_title',
            'pi.ItemSalaryGrade as salary_grade',
            'pos.position_name',
            'o.office_name',
            'd.division_name as department',
            'pi.ItemStatus as status',

        ]);
        $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
        $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
        $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
        $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
        $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
        $builder->where('jp.publication_status', 1); // Assuming 1 means active/public
        $builder->orderBy('jv.created_at', 'DESC');
        
        $data['jobs'] = $builder->get()->getResultArray();
        return view('home', $data); // This is your job listing page
    }

    public function view($id = null)
    {
        $builder = $this->jobVacancyModel->db->table('job_vacancies jv');
        $builder->select([
            'jv.id_vacancy as id',
            'jv.plantilla_item_id',
            'jv.date_posted',
            'jv.created_at',
            'jp.interview_date',
            'jp.interview_venue',
            'jp.publication_status',
            'jp.type as publication_type',
            'jp.hr_head',
            'jp.application_deadline',
            'jp.remarks',
            'pi.item_number as plantilla_item_no',
            'pi.xItemTitle as position_title',
            'pi.ItemSalaryGrade as salary_grade',
            'pos.position_name',
            'o.office_name',
            'd.division_name as department',
            'pi.ItemStatus as status',

        ]);
        $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
        $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
        $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
        $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
        $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
        $builder->where('jv.id_vacancy', $id);
        $builder->where('jp.publication_status', 1); // Only show published jobs
        
        $job = $builder->get()->getRowArray();

        if (!$job) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Job not found');
        }

        return view('job_view', ['job' => $job]);
    }

    /**
     * Get all posted jobs for modal use
     */
    public function getAllPosted()
    {
        try {
            $builder = $this->jobVacancyModel->db->table('job_vacancies jv');
            $builder->select([
                'jv.id_vacancy as id',
                'jv.plantilla_item_id',
                'jv.date_posted',
                'jv.created_at',
                'jp.interview_date',
                'jp.interview_venue',
                'jp.publication_status',
                'jp.type as publication_type',
                'jp.hr_head',
                'jp.application_deadline',
                'jp.remarks',
                'pi.item_number as plantilla_item_no',
                'pi.xItemTitle as position_title',
                'pi.ItemSalaryGrade as salary_grade',
                'pos.position_name',
                'o.office_name',
                'd.division_name as department',
                'pi.ItemStatus as status',
                'jp.description',
                
                'jp.education',
                'jp.training',
                'jp.experience',
                'jp.eligibility',
                'jp.competency',
                'jp.duties_responsibilities',
                'jp.application_requirements'
            ]);
            $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
            $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
            $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
            $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
            $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
            $builder->where('jp.publication_status', 1); // Assuming 1 means active/public
            $builder->orderBy('jv.created_at', 'DESC');
            
            $jobs = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'jobs' => $jobs
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while fetching jobs'
            ]);
        }
    }

    /**
     * Get job details via AJAX for modal
     */
    public function getDetails($id = null)
    {
        try {
            $builder = $this->jobVacancyModel->db->table('job_vacancies jv');
            $builder->select([
                'jv.id_vacancy as id',
                'jv.plantilla_item_id',
                'jv.date_posted',
                'jv.created_at',
                'jp.interview_date',
                'jp.interview_venue',
                'jp.publication_status',
                'jp.type as publication_type',
                'jp.hr_head',
                'jp.application_deadline',
                'jp.remarks',
                'pi.item_number as plantilla_item_no',
                'pi.xItemTitle as position_title',
                'pi.ItemSalaryGrade as salary_grade',
                'pos.position_name',
                'o.office_name',
                'd.division_name as department',
                'pi.ItemStatus as status',
                'jp.description',
                
                'jp.education',
                'jp.training',
                'jp.experience',
                'jp.eligibility',
                'jp.competency',
                'jp.duties_responsibilities',
                'jp.application_requirements'
            ]);
            $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
            $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
            $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
            $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
            $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
            $builder->where('jv.id_vacancy', $id);
            $builder->where('jp.publication_status', 1); // Only show published jobs
            
            $job = $builder->get()->getRowArray();
            
            if (!$job) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Job not found'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'job' => $job
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while fetching job details'
            ]);
        }
    }

}
