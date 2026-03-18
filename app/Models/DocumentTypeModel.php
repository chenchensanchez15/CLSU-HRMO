<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentTypeModel extends Model
{
    protected $table = 'lib_document_types';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'document_type_name',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get all document types
     */
    public function getAllDocumentTypes()
    {
        return $this->findAll();
    }
    
    /**
     * Get document type by ID
     */
    public function getDocumentType($id)
    {
        return $this->find($id);
    }
    
    /**
     * Get document type by name
     */
    public function getDocumentTypeByName($name)
    {
        return $this->where('document_type_name', $name)->first();
    }
}