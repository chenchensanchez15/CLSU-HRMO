<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Job Vacancy</h1>
        <a href="<?= base_url('job-vacancies') ?>" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <?php if (session()->has('errors')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= esc(session('error')) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="<?= base_url('job-vacancies/' . $vacancy['id_vacancy']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Publication Selection -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Publication <span class="text-red-500">*</span>
                    </label>
                    <select name="publication_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Publication</option>
                        <?php foreach ($publications as $publication): ?>
                            <option value="<?= $publication['id_publication'] ?>" 
                                    <?= $vacancy['publication_id'] == $publication['id_publication'] ? 'selected' : '' ?>>
                                Publication #<?= $publication['id_publication'] ?> - 
                                <?= date('M d, Y', strtotime($publication['request_date'])) ?> - 
                                <?= $publication['type'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Plantilla Item Selection -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Plantilla Item <span class="text-red-500">*</span>
                    </label>
                    <select name="plantilla_item_id" id="plantillaSelect" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Plantilla Item</option>
                        <?php foreach ($plantillaItems as $item): ?>
                            <option value="<?= $item['id'] ?>" 
                                    data-details='<?= json_encode($item) ?>'
                                    <?= $vacancy['plantilla_item_id'] == $item['id'] ? 'selected' : '' ?>>
                                <?= $item['item_number'] ?> - <?= $item['position_title'] ?> 
                                (<?= $item['department_name'] ?? 'N/A' ?> - <?= $item['office_name'] ?? 'N/A' ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="plantillaDetails" class="mt-2 text-sm text-gray-600">
                        <?php if ($currentPlantillaItem): ?>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="font-medium"><?= $currentPlantillaItem['position_title'] ?></div>
                            <div class="grid grid-cols-2 gap-2 mt-2 text-sm">
                                <div><span class="font-medium">Item Number:</span> <?= $currentPlantillaItem['item_number'] ?></div>
                                <div><span class="font-medium">Department:</span> <?= $currentPlantillaItem['department_name'] ?? 'N/A' ?></div>
                                <div><span class="font-medium">Office:</span> <?= $currentPlantillaItem['office_name'] ?? 'N/A' ?></div>
                                <div><span class="font-medium">Salary Grade:</span> <?= $currentPlantillaItem['salary_grade'] ?? 'N/A' ?></div>
                                <div><span class="font-medium">Monthly Salary:</span> ₱<?= number_format($currentPlantillaItem['monthly_salary'] ?? 0, 2) ?></div>
                                <div><span class="font-medium">Status:</span> <?= $currentPlantillaItem['status'] ?? 'N/A' ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Date Posted -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Date Posted <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_posted" value="<?= $vacancy['date_posted'] ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                    <i class="fas fa-save mr-2"></i> Update Job Vacancy
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Display plantilla item details when selected
document.getElementById('plantillaSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const detailsDiv = document.getElementById('plantillaDetails');
    
    if (selectedOption.value) {
        const details = JSON.parse(selectedOption.getAttribute('data-details'));
        detailsDiv.innerHTML = `
            <div class="bg-gray-50 p-3 rounded-lg">
                <div class="font-medium">${details.position_title}</div>
                <div class="grid grid-cols-2 gap-2 mt-2 text-sm">
                    <div><span class="font-medium">Item Number:</span> ${details.item_number}</div>
                    <div><span class="font-medium">Department:</span> ${details.department_name || 'N/A'}</div>
                    <div><span class="font-medium">Office:</span> ${details.office_name || 'N/A'}</div>
                    <div><span class="font-medium">Salary Grade:</span> ${details.salary_grade || 'N/A'}</div>
                    <div><span class="font-medium">Monthly Salary:</span> ₱${parseFloat(details.monthly_salary || 0).toFixed(2)}</div>
                    <div><span class="font-medium">Status:</span> ${details.status || 'N/A'}</div>
                </div>
            </div>
        `;
    } else {
        detailsDiv.innerHTML = '';
    }
});
</script>
<?= $this->endSection() ?>