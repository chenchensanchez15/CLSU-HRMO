<?php

namespace App\Models;

use CodeIgniter\Model;


class PlantillaItemModel extends Model
{
    protected $DBGroup = 'hrmis';
    protected $DBPrefix = '';
    protected $allowCallbacks = true;
    protected $allowValidations = true;
    protected $table = 'plantilla_items';
    protected $primaryKey = 'id_plantilla_item';
    
    protected $allowedFields = [
        'item_number',
        'xItemTitle',
        'position_id',
        'ItemTitleExt',
        'ItemSalaryGrade',
        'xxsalary_grade',
        'item_area_code',
        'item_area_type',
        'item_level',
        'item_class',
        'xxclass_name',
        'category_id',
        'item_attribution',
        'ItemStatus',
        'ItemAvailable',
        'is_available',
        'AddedBy',
        'DateAdded',
        'ModifiedBy',
        'DateModified',
        'Remarks'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Initialize the model with HRMIS database connection
     */
    public function __construct()
    {
        parent::__construct();
        // Set the database group to HRMIS
        $this->db = \Config\Database::connect('hrmis');
        if (!$this->db) {
            throw new \RuntimeException('Could not establish HRMIS database connection');
        }
    }

    /**
     * Get plantilla item with department and office details
     *
     * @param int $id
     * @return array|null
     */
    public function getPlantillaItemWithDetails($id)
    {
        $builder = $this->db->table('plantilla_items pi');
        $builder->select([
            'pi.id_plantilla_item as id',
            'pi.item_number',
            'pi.xItemTitle as position_title',
            'pi.ItemSalaryGrade as salary_grade',
            'pi.ItemStatus as status',
            'pos.position_name',
            'o.office_name',
            'd.division_name'
        ]);
        $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
        $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
        $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
        $builder->where('pi.id_plantilla_item', $id);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Get all active plantilla items with details
     *
     * @param string $status
     * @return array
     */
    public function getActivePlantillaItems($status = 'Active')
    {
        $builder = $this->db->table('plantilla_items pi');
        $builder->select([
            'pi.id_plantilla_item as id',
            'pi.item_number',
            'pi.xItemTitle as position_title',
            'pi.ItemSalaryGrade as salary_grade',
            'pi.ItemStatus as status',
            'pos.position_name',
            'o.office_name',
            'd.division_name'
        ]);
        $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
        $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
        $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
        $builder->where('pi.ItemStatus', $status);
        $builder->orderBy('pi.item_number', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Search plantilla items by position title or item number
     *
     * @param string $search
     * @return array
     */
    public function searchPlantillaItems($search)
    {
        $builder = $this->db->table('plantilla_items pi');
        $builder->select([
            'pi.id_plantilla_item as id',
            'pi.item_number',
            'pi.xItemTitle as position_title',
            'pi.ItemSalaryGrade as salary_grade',
            'pi.ItemStatus as status',
            'pos.position_name',
            'o.office_name',
            'd.division_name'
        ]);
        $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
        $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
        $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
        $builder->groupStart();
        $builder->like('pi.xItemTitle', $search);
        $builder->orLike('pi.item_number', $search);
        $builder->groupEnd();
        $builder->orderBy('pi.item_number', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}