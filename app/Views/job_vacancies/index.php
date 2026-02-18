<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Job Vacancies</h1>
        <a href="<?= base_url('job-vacancies/create') ?>" 
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i> Create New Vacancy
        </a>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative">
            <input type="text" id="searchInput" placeholder="Search job vacancies..." 
                   class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
        </div>
        <div id="searchResults" class="mt-2"></div>
    </div>

    <!-- Job Vacancies Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Office</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary Grade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monthly Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Posted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($vacancies)): ?>
                        <?php foreach ($vacancies as $vacancy): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= esc($vacancy['id_vacancy']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= esc($vacancy['position_title'] ?? 'N/A') ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    Item No: <?= esc($vacancy['item_number'] ?? 'N/A') ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= esc($vacancy['department_name'] ?? 'N/A') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= esc($vacancy['office_name'] ?? 'N/A') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= esc($vacancy['salary_grade'] ?? 'N/A') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ₱<?= number_format($vacancy['monthly_salary'] ?? 0, 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('M d, Y', strtotime($vacancy['date_posted'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?= base_url('job-vacancies/' . $vacancy['id_vacancy']) ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="<?= base_url('job-vacancies/' . $vacancy['id_vacancy'] . '/edit') ?>" 
                                   class="text-green-600 hover:text-green-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" 
                                        onclick="deleteVacancy(<?= $vacancy['id_vacancy'] ?>)"
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No job vacancies found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
                location.reload();
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

// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('searchResults');
    
    if (query.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }
    
    fetch(`<?= base_url('job-vacancies/search') ?>?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                let html = '<div class="bg-white border border-gray-200 rounded-lg shadow-sm">';
                data.data.forEach(vacancy => {
                    html += `
                        <div class="p-3 border-b border-gray-100 hover:bg-gray-50">
                            <div class="font-medium">${vacancy.position_title}</div>
                            <div class="text-sm text-gray-600">
                                Item: ${vacancy.item_number} | 
                                Department: ${vacancy.department_name} | 
                                Office: ${vacancy.office_name}
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<div class="bg-white border border-gray-200 rounded-lg p-3 text-gray-500">No results found</div>';
            }
        })
        .catch(error => {
            console.error('Search error:', error);
        });
});
</script>
<?= $this->endSection() ?>