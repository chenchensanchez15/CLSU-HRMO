<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantDocumentsModel extends Model
{
    protected $table = 'applicant_documents';
    protected $primaryKey = 'id';
    
    // Allowed fields must match your DB columns for insert/update
    protected $allowedFields = [
        'user_id',
        'document_type_id',
        'filename',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get all documents for a user
     */
    public function getDocumentsByUser($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }
    
    /**
     * Get document by user and document type
     */
    public function getDocumentByType($userId, $documentTypeId)
    {
        return $this->where('user_id', $userId)
                   ->where('document_type_id', $documentTypeId)
                   ->first();
    }
    
    /**
     * Save or update document
     */
    public function saveDocument($userId, $documentTypeId, $filename)
    {
        $existing = $this->getDocumentByType($userId, $documentTypeId);
        
        if ($existing) {
            // Update existing document
            return $this->update($existing['id'], [
                'filename' => $filename,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Insert new document
            return $this->insert([
                'user_id' => $userId,
                'document_type_id' => $documentTypeId,
                'filename' => $filename,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    /**
     * Delete document by user and document type
     */
    public function deleteDocument($userId, $documentTypeId)
    {
        $document = $this->getDocumentByType($userId, $documentTypeId);
        if ($document) {
            return $this->delete($document['id']);
        }
        return false;
    }
}