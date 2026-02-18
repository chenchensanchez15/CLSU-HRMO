<?php

namespace App\Controllers;

use App\Models\JobVacancyModel;
use App\Models\PlantillaItemModel;
use App\Models\JobPublicationModel;

class JobVacancies extends BaseController
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

    /**
     * Display list of job vacancies
     */
    public function index()
    {
        $builder = $this->jobVacancyModel->db->table('job_vacancies jv');
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
            'jp.remarks',
            'pi.item_number',
            'pi.xItemTitle as position_title',
            'd.division_name as department_name',
            'o.office_name',
            'pi.ItemSalaryGrade as salary_grade',
            'pos.position_name'
        ]);
        $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
        $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
        $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
        $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
        $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
        $builder->orderBy('jv.created_at', 'DESC');
        
        $vacancies = $builder->get()->getResultArray();

        return view('job_vacancies/index', [
            'vacancies' => $vacancies
        ]);
    }

    /**
     * Show form to create new job vacancy
     */
    public function create()
    {
        // Get active publications
        $publications = $this->jobPublicationModel->where('publication_status', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Get active plantilla items
        $plantillaItems = $this->plantillaItemModel->getActivePlantillaItems('Active');

        return view('job_vacancies/create', [
            'publications' => $publications,
            'plantillaItems' => $plantillaItems
        ]);
    }

    /**
     * Store new job vacancy
     */
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'publication_id' => 'required|is_not_unique[job_publications.id_publication]',
            'plantilla_item_id' => 'required|is_not_unique[`hrmis-template`.plantilla_items.id_plantilla_item]',
            'date_posted' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Check if vacancy already exists
        $existing = $this->jobVacancyModel
            ->where('publication_id', $this->request->getPost('publication_id'))
            ->where('plantilla_item_id', $this->request->getPost('plantilla_item_id'))
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This job vacancy already exists.');
        }

        $data = [
            'publication_id' => $this->request->getPost('publication_id'),
            'plantilla_item_id' => $this->request->getPost('plantilla_item_id'),
            'date_posted' => $this->request->getPost('date_posted'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->jobVacancyModel->insert($data)) {
            return redirect()->to('/job-vacancies')
                ->with('success', 'Job vacancy created successfully.');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create job vacancy.');
        }
    }

    /**
     * Show job vacancy details
     */
    public function show($id)
    {
        $vacancy = $this->getVacancyWithDetails($id);
        
        if (!$vacancy) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Job vacancy not found");
        }

        return view('job_vacancies/show', [
            'vacancy' => $vacancy
        ]);
    }

    /**
     * Show form to edit job vacancy
     */
    public function edit($id)
    {
        $vacancy = $this->jobVacancyModel->find($id);
        
        if (!$vacancy) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Job vacancy not found");
        }

        // Get active publications
        $publications = $this->jobPublicationModel->where('publication_status', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Get active plantilla items
        $plantillaItems = $this->plantillaItemModel->getActivePlantillaItems('Active');

        // Get current plantilla item details
        $currentPlantillaItem = $this->plantillaItemModel->getPlantillaItemWithDetails($vacancy['plantilla_item_id']);

        return view('job_vacancies/edit', [
            'vacancy' => $vacancy,
            'publications' => $publications,
            'plantillaItems' => $plantillaItems,
            'currentPlantillaItem' => $currentPlantillaItem
        ]);
    }

    /**
     * Update job vacancy
     */
    public function update($id)
    {
        $vacancy = $this->jobVacancyModel->find($id);
        
        if (!$vacancy) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Job vacancy not found");
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'publication_id' => 'required|is_not_unique[job_publications.id_publication]',
            'plantilla_item_id' => 'required|is_not_unique[`hrmis-template`.plantilla_items.id_plantilla_item]',
            'date_posted' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Check if another vacancy with same publication_id and plantilla_item_id exists
        $existing = $this->jobVacancyModel
            ->where('publication_id', $this->request->getPost('publication_id'))
            ->where('plantilla_item_id', $this->request->getPost('plantilla_item_id'))
            ->where('id_vacancy !=', $id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This job vacancy combination already exists.');
        }

        $data = [
            'publication_id' => $this->request->getPost('publication_id'),
            'plantilla_item_id' => $this->request->getPost('plantilla_item_id'),
            'date_posted' => $this->request->getPost('date_posted')
        ];

        if ($this->jobVacancyModel->update($id, $data)) {
            return redirect()->to('/job-vacancies')
                ->with('success', 'Job vacancy updated successfully.');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update job vacancy.');
        }
    }

    /**
     * Delete job vacancy
     */
    public function delete($id)
    {
        $vacancy = $this->jobVacancyModel->find($id);
        
        if (!$vacancy) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Job vacancy not found'
            ]);
        }

        if ($this->jobVacancyModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Job vacancy deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete job vacancy'
            ]);
        }
    }

    /**
     * Search job vacancies
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        if (empty($search)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Search query is required'
            ]);
        }

        $builder = $this->jobVacancyModel->db->table('job_vacancies jv');
        $builder->select([
            'jv.id_vacancy',
            'jv.publication_id',
            'jv.plantilla_item_id',
            'jv.date_posted',
            'jp.type as publication_type',
            'pi.item_number',
            'pi.xItemTitle as position_title',
            'd.division_name as department_name',
            'o.office_name'
        ]);
        $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
        $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
        $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
        $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
        $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
        $builder->groupStart();
        $builder->like('pi.xItemTitle', $search);
        $builder->orLike('pi.item_number', $search);
        $builder->orLike('d.division_name', $search);
        $builder->orLike('o.office_name', $search);
        $builder->groupEnd();
        $builder->orderBy('jv.created_at', 'DESC');
        
        $results = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Get vacancy with complete details
     */
    private function getVacancyWithDetails($id)
    {
        $builder = $this->jobVacancyModel->db->table('job_vacancies jv');
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
            'jp.hr_head_position',
            'jp.application_deadline',
            'jp.remarks',
            'pi.item_number',
            'pi.xItemTitle as position_title',
            'pi.ItemSalaryGrade as salary_grade',
            'pos.position_name',
            'pi.ItemStatus as plantilla_status',
            'd.division_name as department_name',
            'd.division_code as department_code',
            'o.office_name',
            'o.office_code'
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
     * Get all posted jobs for home page
     */
    public function getAllPosted()
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
            'pi.ItemStatus as status'
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
    }
}