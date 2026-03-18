<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Job Vacancy Details</h1>
        <a href="<?= base_url('job-vacancies') ?>" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Vacancy Information -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Vacancy Information</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">ID</label>
                        <p class="text-gray-900"><?= esc($vacancy['id_vacancy']) ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Date Posted</label>
                        <p class="text-gray-900"><?= date('F d, Y', strtotime($vacancy['date_posted'])) ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Created At</label>
                        <p class="text-gray-900"><?= date('F d, Y g:i A', strtotime($vacancy['created_at'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Publication Information -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Publication Information</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Publication ID</label>
                        <p class="text-gray-900"><?= esc($vacancy['publication_id']) ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Publication Type</label>
                        <p class="text-gray-900"><?= esc($vacancy['publication_type'] ?? 'N/A') ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Interview Date</label>
                        <p class="text-gray-900"><?= $vacancy['interview_date'] ? date('F d, Y', strtotime($vacancy['interview_date'])) : 'N/A' ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Interview Venue</label>
                        <p class="text-gray-900"><?= esc($vacancy['interview_venue'] ?? 'N/A') ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">HR Head</label>
                        <p class="text-gray-900"><?= esc($vacancy['hr_head'] ?? 'N/A') ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Application Deadline</label>
                        <p class="text-gray-900"><?= $vacancy['application_deadline'] ? date('F d, Y', strtotime($vacancy['application_deadline'])) : 'N/A' ?></p>
                    </div>
                </div>
            </div>

            <!-- Plantilla Item Information -->
            <div class="border border-gray-200 rounded-lg p-4 md:col-span-2">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Plantilla Item Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Plantilla ID</label>
                        <p class="text-gray-900"><?= esc($vacancy['plantilla_item_id']) ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Item Number</label>
                        <p class="text-gray-900"><?= esc($vacancy['item_number'] ?? 'N/A') ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Position Title</label>
                        <p class="text-gray-900"><?= esc($vacancy['position_title'] ?? 'N/A') ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Position Name</label>
                        <p class="text-gray-900"><?= esc($vacancy['position_name'] ?? 'N/A') ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Salary Grade</label>
                        <p class="text-gray-900"><?= esc($vacancy['salary_grade'] ?? 'N/A') ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Department</label>
                        <p class="text-gray-900"><?= esc($vacancy['department_name'] ?? 'N/A') ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Office</label>
                        <p class="text-gray-900"><?= esc($vacancy['office_name'] ?? 'N/A') ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Status</label>
                        <p class="text-gray-900">
                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                <?= ($vacancy['plantilla_status'] ?? '') === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= esc($vacancy['plantilla_status'] ?? 'N/A') ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex space-x-4">
            <a href="<?= base_url('job-vacancies/' . $vacancy['id_vacancy'] . '/edit') ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            
            <button type="button" 
                    onclick="deleteVacancy(<?= $vacancy['id_vacancy'] ?>)"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-trash mr-2"></i> Delete
            </button>
        </div>
    </div>
</div>

<script>
function deleteVacancy(id) {
    if (confirm('Are you sure you want to delete this job vacancy?')) {
        fetch(`<?= base_url('job-vacancies') ?>/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Job vacancy deleted successfully');
                window.location.href = '<?= base_url('job-vacancies') ?>';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the job vacancy');
        });
    }
}
</script>
<?= $this->endSection() ?>