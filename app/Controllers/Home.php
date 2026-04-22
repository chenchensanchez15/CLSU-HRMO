<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $jobVacancyModel = new \App\Models\JobVacancyModel();

        $builder = $jobVacancyModel->db->table('job_vacancies jv');
        $builder->select([
            'jv.id_vacancy as id',
            'jv.status as vacancy_status',
            'jv.plantilla_item_id',
            'jv.publication_id',
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
        ]);

        $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
        $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
        $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
        $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
        $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
        $builder->where('jv.status', 'Active'); // Only Active vacancies
        $builder->orderBy('jv.created_at', 'DESC');

        $jobs = $builder->get()->getResultArray();

        // ✅ COMPUTE MONTHLY SALARY and FETCH REQUIREMENTS
        foreach ($jobs as &$job) {
            $job['monthly_salary'] = $this->get_monthly_salary($job['plantilla_item_id']) ?? 0;
            
            // ✅ FETCH REQUIREMENTS from job_publication_requirements
            $job['application_requirements'] = $this->get_job_requirements($job['publication_id']);
        }

        $data['jobs'] = $jobs;

        return view('home', $data);
    }


private function get_monthly_salary($plantilla_item_id)
{
    $db = \Config\Database::connect();

    // ✅ Use hrmis-template database explicitly
    $schedule = $db->query("
        SELECT *
        FROM `hrmis-template`.lib_salary_schedules
        WHERE schedule_forpermanent = 1
        AND schedule_effectivity <= CURDATE()
        ORDER BY schedule_effectivity DESC
        LIMIT 1
    ")->getRow();

    if (!$schedule) return null;

    $item = $db->query("
        SELECT pi.id_plantilla_item, lp.salary_grade
        FROM `hrmis-template`.plantilla_items pi
        LEFT JOIN `hrmis-template`.lib_positions lp 
            ON pi.position_id = lp.id_position
        WHERE pi.id_plantilla_item = ?
    ", [$plantilla_item_id])->getRow();

    if (!$item || !$item->salary_grade) return null;

    $salary = $db->query("
        SELECT sg_sin1
        FROM `hrmis-template`.lib_salaries
        WHERE salary_grade = ?
        AND salary_schedule_id = ?
        LIMIT 1
    ", [$item->salary_grade, $schedule->id_salary_schedule])->getRow();

    return $salary ? $salary->sg_sin1 : null;
}

/**
 * Get job requirements from job_publication_requirements table
 * Matches publication_id from job_vacancies
 */
private function get_job_requirements($publication_id)
{
    if (empty($publication_id)) {
        return 'N/A';
    }
    
    $db = \Config\Database::connect();
    
    // Fetch all requirements for this publication_id
    $requirements = $db->table('job_publication_requirements')
                       ->select('requirement_text')
                       ->where('publication_id', $publication_id)
                       ->orderBy('id_requirement', 'ASC')
                       ->get()
                       ->getResultArray();
    
    if (empty($requirements)) {
        return 'No specific requirements listed.';
    }
    
    // Format requirements as a bulleted list
    $formattedRequirements = [];
    foreach ($requirements as $req) {
        if (!empty($req['requirement_text'])) {
            $formattedRequirements[] = '• ' . $req['requirement_text'];
        }
    }
    
    return !empty($formattedRequirements) ? implode("\n", $formattedRequirements) : 'No specific requirements listed.';
}

}
