<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Job Application | Step 1 - Personal Information</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clsuGreen: '#0B6B3A',
                        clsuGold: '#F2C94C'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

<header class="bg-clsuGreen text-white py-3 px-6 shadow">
    <div class="flex items-center gap-3">
        <img src="/HRMO/public/assets/images/clsu-logo2.png" alt="CLSU Logo" class="w-12 h-auto">
        <div class="flex flex-col leading-tight">
            <span class="text-xl font-bold">CLSU Online Job Application</span>
        </div>
    </div>
</header>


<div class="min-h-screen bg-gray-100 px-4 py-6 flex justify-center">
    <!-- Main Form Container -->
    <div class="w-full max-w-7xl bg-white shadow rounded-lg p-6 text-sm">

           <!-- Application Details Header -->
<div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-3">
    <h2 class="text-xl md:text-2xl font-bold text-clsuGreen">Application Details</h2>
    <a href="<?= site_url('dashboard') ?>" class="bg-red-500 text-white px-4 py-1.5 rounded hover:bg-red-600 text-sm font-medium">
        ✕
    </a>
</div>

<!-- Application Details Info (2 Columns, aligned) -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Column 1 -->
    <div class="space-y-2">
        <div class="flex">
            <span class="font-semibold text-clsuGreen w-32">Position:</span>
               <span class="text-gray-700"><?= esc($job['position_title'] ?? '-') ?></span>
        </div>
        <div class="flex">
            <span class="font-semibold text-clsuGreen w-32">Office:</span>
               <span class="text-gray-700"><?= esc($job['office'] ?? '-') ?></span>
        </div>
    </div>

    <!-- Column 2 -->
    <div class="space-y-2">
        <div class="flex">
            <span class="font-semibold text-clsuGreen w-32">Salary Grade:</span>
            <span class="text-gray-700"><?= esc($job['salary_grade'] ?? '-') ?></span>
        </div>
        <div class="flex">
            <span class="font-semibold text-clsuGreen w-32">Monthly Salary:</span>
            <span class="text-gray-700"><?= esc($job['monthly_salary'] ?? '-') ?></span>
        </div>
    </div>
</div>

<hr class="my-4">
<form id="applicationForm"
      method="POST"
      enctype="multipart/form-data"
      action="<?= base_url('applications/submit/' . $job['id']) ?>">
<input type="hidden" name="job_position_id" value="<?= esc($job['id']) ?>">
<!-- Step 1: Personal Information -->
<div class="step" id="step-1">
    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">Personal Information</div>
       <!-- Instruction Note -->
        <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs mb-4">
            *Do not leave blank entries. Put N/A for not applicable.
        </div>
    <!-- Hidden job_application_id -->
    <input type="hidden" name="job_application_id" value="<?= esc($job_application_id ?? '') ?>">

    <table class="table-auto w-full border-collapse text-xs mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 border">First Name</th>
                <th class="px-2 py-1 border">Middle Name</th>
                <th class="px-2 py-1 border">Last Name</th>
                <th class="px-2 py-1 border">Suffix</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-2 py-1 border">
                    <input type="text" name="first_name" value="<?= esc($profile['first_name'] ?? '') ?>" placeholder="Enter First Name" required class="px-2 py-1 w-full text-xs">
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="middle_name" value="<?= esc($profile['middle_name'] ?? '') ?>" placeholder="Enter Middle Name" class="px-2 py-1 w-full text-xs">
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="last_name" value="<?= esc($profile['last_name'] ?? '') ?>" placeholder="Enter Last Name" required class="px-2 py-1 w-full text-xs">
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="extension" value="<?= esc($profile['suffix'] ?? '') ?>" placeholder="Enter Suffix" class="px-2 py-1 w-full text-xs">
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table-auto w-full border-collapse text-xs mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 border">Email</th>
                <th class="px-2 py-1 border">Phone</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-2 py-1 border">
                    <input type="email" name="email" value="<?= esc($profile['email'] ?? '') ?>" placeholder="Enter Email" required class="px-2 py-1 w-full text-xs">
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="phone" value="<?= esc($profile['phone'] ?? '') ?>" placeholder="Enter Phone Number" required class="px-2 py-1 w-full text-xs">
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table-auto w-full border-collapse text-xs mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 border">Sex</th>
                <th class="px-2 py-1 border">Date of Birth</th>
                <th class="px-2 py-1 border">Civil Status</th>
                <th class="px-2 py-1 border">Citizenship</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-2 py-1 border">
                    <select name="sex" required class="px-2 py-1 w-full text-xs">
                        <option value="">Select Sex</option>
                        <option value="Male" <?= ($profile['sex'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= ($profile['sex'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </td>
                <td class="px-2 py-1 border">
                    <input type="date" name="date_of_birth" value="<?= esc($profile['date_of_birth'] ?? '') ?>" required class="px-2 py-1 w-full text-xs">
                </td>
                <td class="px-2 py-1 border">
                    <select name="civil_status" required class="px-2 py-1 w-full text-xs">
                        <option value="">Select Civil Status</option>
                        <option value="Single" <?= ($profile['civil_status'] ?? '') === 'Single' ? 'selected' : '' ?>>Single</option>
                        <option value="Married" <?= ($profile['civil_status'] ?? '') === 'Married' ? 'selected' : '' ?>>Married</option>
                        <option value="Widowed" <?= ($profile['civil_status'] ?? '') === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                        <option value="Separated" <?= ($profile['civil_status'] ?? '') === 'Separated' ? 'selected' : '' ?>>Separated</option>
                        <option value="Divorced" <?= ($profile['civil_status'] ?? '') === 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                    </select>
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="citizenship" value="<?= esc($profile['citizenship'] ?? '') ?>" placeholder="Enter Citizenship" required class="px-2 py-1 w-full text-xs">
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table-auto w-full border-collapse text-xs mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 border">Residential Address</th>
                <th class="px-2 py-1 border">Permanent Address</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-2 py-1 border">
                    <input type="text" name="residential_address" value="<?= esc($profile['residential_address'] ?? '') ?>" placeholder="Enter Residential Address" required class="px-2 py-1 w-full text-xs">
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="permanent_address" value="<?= esc($profile['permanent_address'] ?? '') ?>" placeholder="Enter Permanent Address" required class="px-2 py-1 w-full text-xs">
                </td>
            </tr>
        </tbody>
    </table>

<div class="text-right mt-2">
    <button type="button" id="step1Next" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">
        Next
    </button>
</div>

<script>
document.getElementById('step1Next').addEventListener('click', function() {
    // Select all required inputs and selects inside step-1
    const requiredFields = document.querySelectorAll('#step-1 [required]');
    let allFilled = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            allFilled = false;
            // Optional: highlight empty fields
            field.classList.add('border-red-500');
        } else {
            field.classList.remove('border-red-500');
        }
    });

    if (!allFilled) {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete Information',
            text: 'Please fill out all required fields before proceeding.',
        });
        return; // stop nextStep
    }

    // All required fields are filled → proceed
    nextStep(1);
});
</script>
</div>

<!-- Step 2: Family Background -->
<div class="step hidden" id="step-2">
    <div>
        <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">
            Family Background
        </div>

        <!-- Instruction Note -->
        <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs mb-4">
            *Do not leave blank entries. Put N/A for not applicable.
        </div>

        <div class="space-y-4">

            <!-- Spouse -->
            <div>
                <p class="font-semibold text-xs mb-1 text-text-black">Spouse</p>
                <div class="overflow-x-auto mb-2">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">Surname</th>
                                <th class="px-1 py-1 border">First Name</th>
                                <th class="px-1 py-1 border">Middle Name</th>
                                <th class="px-1 py-1 border">Extension</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_surname" placeholder="Enter Surname" value="<?= esc($spouse['last_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_first_name" placeholder="Enter First Name" value="<?= esc($spouse['first_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_middle_name" placeholder="Enter Middle Name" value="<?= esc($spouse['middle_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_ext_name" placeholder="Enter Extension" value="<?= esc($spouse['extension'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="overflow-x-auto mb-2">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">Occupation</th>
                                <th class="px-1 py-1 border">Contact Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_occupation" placeholder="Enter Occupation" value="<?= esc($spouse['occupation'] ?? '') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_contact" placeholder="Enter Contact Number" value="<?= esc($spouse['contact'] ?? '') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Father -->
            <div>
                <p class="font-semibold text-xs mb-1 text-text-black">Father</p>
                <div class="overflow-x-auto mb-2">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">Surname</th>
                                <th class="px-1 py-1 border">First Name</th>
                                <th class="px-1 py-1 border">Middle Name</th>
                                <th class="px-1 py-1 border">Extension</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_surname" placeholder="Enter Surname" value="<?= esc($father['last_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_first_name" placeholder="Enter First Name" value="<?= esc($father['first_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_middle_name" placeholder="Enter Middle Name" value="<?= esc($father['middle_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_ext_name" placeholder="Enter Extension" value="<?= esc($father['extension'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="overflow-x-auto mb-2">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">Occupation</th>
                                <th class="px-1 py-1 border">Contact Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_occupation" placeholder="Enter Occupation" value="<?= esc($father['occupation'] ?? '') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_contact" placeholder="Enter Contact Number" value="<?= esc($father['contact'] ?? '') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mother -->
            <div>
                <p class="font-semibold text-xs mb-1 text-text-black">Mother (Maiden Name)</p>
                <div class="overflow-x-auto mb-2">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">Surname</th>
                                <th class="px-1 py-1 border">First Name</th>
                                <th class="px-1 py-1 border">Middle Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="mother_maiden_surname" placeholder="Enter Surname" value="<?= esc($mother['last_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="mother_first_name" placeholder="Enter First Name" value="<?= esc($mother['first_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="mother_middle_name" placeholder="Enter Middle Name" value="<?= esc($mother['middle_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="overflow-x-auto mb-2">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">Occupation</th>
                                <th class="px-1 py-1 border">Contact Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="mother_occupation" placeholder="Enter Occupation" value="<?= esc($mother['occupation'] ?? '') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="mother_contact" placeholder="Enter Contact Number" value="<?= esc($mother['contact'] ?? '') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="text-right mt-4">
        <button type="button" onclick="prevStep(2)" class="bg-gray-400 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-gray-500">
            Previous
        </button>
        <button type="button" id="step2Next" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold hover:bg-green-800">
            Next
        </button>
    </div>
</div>

<script>
// Step 2 validation
document.getElementById('step2Next').addEventListener('click', function() {
    const requiredFields = document.querySelectorAll('#step-2 [required]');
    let allFilled = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            allFilled = false;
            field.classList.add('border-red-500');
        } else {
            field.classList.remove('border-red-500');
        }
    });

    if (!allFilled) {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete Information',
            text: 'Please fill out all required fields before proceeding.',
        });
        return;
    }

    nextStep(2);
});
</script>


<?php
$db = \Config\Database::connect(); // ← Add this line
$user_id = session()->get('user_id'); // adjust according to your session

$builder = $db->table('applicant_education');
$applicant_education = $builder->where('user_id', $user_id)->get()->getResultArray();

// Prepare education_data array for prefilling the form
$education_data = [];
if (!empty($applicant_education)) {
    foreach ($applicant_education as $edu) {
        switch ($edu['level']) {
            case 'Elementary':
                $education_data['elementary'] = $edu;
                break;
            case 'Secondary':
                $education_data['secondary'] = $edu;
                break;
            case 'Vocational/Trade':
                $education_data['vocational'] = $edu;
                break;
            case 'College':
                $education_data['college'] = $edu;
                break;
            case 'Graduate Studies':
                $education_data['graduate'] = $edu;
                break;
        }
    }
}

// Ensure every level has a default array to avoid undefined index errors
$levels = ['elementary', 'secondary', 'vocational', 'college', 'graduate'];
foreach ($levels as $level) {
    if (!isset($education_data[$level])) {
        $education_data[$level] = [
            'school_name' => '',
            'degree_course' => '',
            'period_from' => '',
            'period_to' => '',
            'highest_level_units' => '',
            'year_graduated' => '',
            'awards' => ''
        ];
    }
}
?>
<!-- Step 3: Educational Background -->
<div class="step hidden" id="step-3">
    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">Educational Background</div>

    <!-- Instruction Note -->
    <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs mb-4">
        *Do not leave blank entries. Put N/A for not applicable.
    </div>

    <?php
    $levels = [
        'Elementary' => 'elementary',
        'Secondary' => 'secondary',
        'Vocational / Trade' => 'vocational',
        'College' => 'college',
        'Graduate Studies' => 'graduate'
    ];

    function format_period($from, $to) {
        if (!$from && !$to) return '';
        return trim("$from – $to");
    }
    ?>

    <?php foreach($levels as $label => $key): ?>
    <!-- Level Label -->
    <p class="font-semibold text-xs mb-1"><?= $label ?></p>

    <table class="table-auto w-full border-collapse text-xs mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 border">School Name</th>
                <th class="px-2 py-1 border">Degree / Course</th>
                <th class="px-2 py-1 border">Period (From – To)</th>
                <th class="px-2 py-1 border">Year Graduated</th>
                <th class="px-2 py-1 border">Highest Level / Units</th>
                <th class="px-2 py-1 border">Awards / Honors</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-2 py-1 border">
                    <input type="text" name="<?= $key ?>_school" value="<?= esc($education_data[$key]['school_name'] ?? '') ?>" placeholder="Enter School Name" class="px-2 py-1 w-full text-xs" required>
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="<?= $key ?>_degree" value="<?= esc($education_data[$key]['degree_course'] ?? '') ?>" placeholder="Enter Degree / Course" class="px-2 py-1 w-full text-xs" required>
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="<?= $key ?>_period" value="<?= esc(format_period($education_data[$key]['period_from'] ?? '', $education_data[$key]['period_to'] ?? '')) ?>" placeholder="Enter Period From – To" class="px-2 py-1 w-full text-xs" required>
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="<?= $key ?>_year" value="<?= esc($education_data[$key]['year_graduated'] ?? '') ?>" placeholder="Enter Year Graduated" class="px-2 py-1 w-full text-xs" required>
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="<?= $key ?>_units" value="<?= esc($education_data[$key]['highest_level_units'] ?? '') ?>" placeholder="Enter Highest Level / Units" class="px-2 py-1 w-full text-xs" required>
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="<?= $key ?>_awards" value="<?= esc($education_data[$key]['awards'] ?? '') ?>" placeholder="Enter Awards / Honors" class="px-2 py-1 w-full text-xs" required>
                </td>
            </tr>
        </tbody>
    </table>
    <?php endforeach; ?>

    <div class="text-right mt-2">
        <button type="button" onclick="prevStep(3)" class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">Previous</button>
        <button type="button" id="step3Next" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">Next</button>
    </div>
</div>

<script>
// Step 3 validation
document.getElementById('step3Next').addEventListener('click', function() {
    const requiredFields = document.querySelectorAll('#step-3 [required]');
    let allFilled = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            allFilled = false;
            field.classList.add('border-red-500');
        } else {
            field.classList.remove('border-red-500');
        }
    });

    if (!allFilled) {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete Information',
            text: 'Please fill out all required fields before proceeding.',
        });
        return;
    }

    nextStep(3);
});
</script>

<!-- Step 4: Work Experience -->
<div class="step hidden" id="step-4">
    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">
        Work Experience
    </div>

    <?php
    $user_id = session()->get('user_id');
    $work_experiences = $db->table('applicant_work_experience')
        ->where('user_id', $user_id)
        ->orderBy('date_from', 'DESC')
        ->get()
        ->getResultArray();
    ?>

    <div class="overflow-x-auto mb-2 relative">
        <!-- Add button -->
        <button
            type="button"
            id="add-work-btn"
            class="bg-clsuGreen text-white px-4 py-1 rounded text-xs font-semibold hover:bg-green-800 absolute right-0 top-0"
        >
            Add Work Exp
        </button>

        <table id="work-table" class="table-auto w-full border-collapse text-xs mt-10">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 border">Position Title</th>
                    <th class="px-2 py-1 border">Office / Company</th>
                    <th class="px-2 py-1 border">Inclusive Dates</th>
                    <th class="px-2 py-1 border">Status of Appointment</th>
                    <th class="px-2 py-1 border">Government Service (Yes / No)</th>
                    <th class="px-2 py-1 border">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($work_experiences)): ?>
                    <?php foreach ($work_experiences as $work): ?>
                        <tr>
                            <td class="px-2 py-1 border">
                                <input
                                    type="text"
                                    name="position_title[]"
                                    value="<?= esc($work['position_title']) ?>"
                                    placeholder="Position Title"
                                    class="px-2 py-1 w-full text-xs"
                                >
                            </td>

                            <td class="px-2 py-1 border">
                                <input
                                    type="text"
                                    name="office[]"
                                    value="<?= esc($work['office']) ?>"
                                    placeholder="Office / Company"
                                    class="px-2 py-1 w-full text-xs"
                                >
                            </td>

                            <td class="px-2 py-1 border flex space-x-1">
                                <input
                                    type="date"
                                    name="date_from[]"
                                    value="<?= esc($work['date_from']) ?>"
                                    class="px-2 py-1 w-1/2 text-xs"
                                >
                                <input
                                    type="date"
                                    name="date_to[]"
                                    value="<?= esc($work['date_to']) ?>"
                                    class="px-2 py-1 w-1/2 text-xs"
                                >
                            </td>

                            <td class="px-2 py-1 border">
                                <input
                                    type="text"
                                    name="status_of_appointment[]"
                                    value="<?= esc($work['status_of_appointment']) ?>"
                                    placeholder="e.g. Contractual, Permanent"
                                    class="px-2 py-1 w-full text-xs"
                                >
                            </td>

                            <td class="px-2 py-1 border">
                                <input
                                    type="text"
                                    name="govt_service[]"
                                    value="<?= esc($work['govt_service']) ?>"
                                    placeholder="Yes / No"
                                    class="px-2 py-1 w-full text-xs"
                                >
                            </td>

                            <td class="px-2 py-1 border text-center">
                                <button type="button" class="text-red-500 hover:underline remove-row">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="px-2 py-1 border text-center" colspan="6">
                            No work experience added.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(4)"
            class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">
            Previous
        </button>
        <button type="button" onclick="nextStep(4)"
            class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">
            Next
        </button>
    </div>
</div>
<script>
document.getElementById('add-work-btn').addEventListener('click', function () {
    const tableBody = document.querySelector('#work-table tbody');

    const emptyRow = tableBody.querySelector('td[colspan="6"]');
    if (emptyRow) emptyRow.closest('tr').remove();

    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td class="px-2 py-1 border">
            <input type="text" name="position_title[]" placeholder="Position Title" class="px-2 py-1 w-full text-xs">
        </td>
        <td class="px-2 py-1 border">
            <input type="text" name="office[]" placeholder="Office / Company" class="px-2 py-1 w-full text-xs">
        </td>
        <td class="px-2 py-1 border flex space-x-1">
            <input type="date" name="date_from[]" class="px-2 py-1 w-1/2 text-xs">
            <input type="date" name="date_to[]" class="px-2 py-1 w-1/2 text-xs">
        </td>
        <td class="px-2 py-1 border">
            <input type="text" name="status_of_appointment[]" placeholder="Status of Appointment" class="px-2 py-1 w-full text-xs">
        </td>
        <td class="px-2 py-1 border">
            <input type="text" name="govt_service[]" placeholder="Yes / No" class="px-2 py-1 w-full text-xs">
        </td>
        <td class="px-2 py-1 border text-center">
            <button type="button" class="text-red-500 hover:underline remove-row">Delete</button>
        </td>
    `;
    tableBody.appendChild(newRow);
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        const tableBody = document.querySelector('#work-table tbody');
        e.target.closest('tr').remove();

        if (tableBody.querySelectorAll('tr').length === 0) {
            tableBody.innerHTML =
                '<tr><td class="px-2 py-1 border text-center" colspan="6">No work experience added.</td></tr>';
        }
    }
});
</script>

<!-- Step 5: File Attachments & Submit -->
<div class="step hidden" id="step-5">
    <h2 class="text-sm font-semibold text-clsuGreen mb-2">File Attachments</h2>

    <?php
    $docLabels = [
        'resume'      => '1. Resume (PDF)',
        'tor'         => '2. Transcript of Records (TOR) (PDF)',
        'diploma'     => '3. Diploma (PDF)',
        'certificate' => '4. Certificate (optional, PDF)'
    ];
    ?>

    <div class="overflow-x-auto">
        <table class="table-auto w-full border-collapse text-xs">
            <tbody>
            <?php foreach ($docLabels as $key => $label): ?>
                <tr>
                    <th class="px-2 py-2 border text-left w-1/3">
                        <?= esc($label) ?>
                    </th>
                    <td class="px-2 py-2 border">
                        <?php if (!empty($documents[$key])): ?>
                            <!-- VIEW + REPLACE -->
                            <div class="flex items-center gap-2">
                                <a href="<?= site_url('applications/viewDocument/'.$app['id'].'/'.$key) ?>"
                                   target="_blank"
                                   class="bg-blue-600 text-white px-2 py-1 rounded text-xs">
                                    View
                                </a>

                                <input type="file"
                                       name="<?= $key ?>"
                                       accept=".pdf"
                                       class="text-xs">
                            </div>
                        <?php else: ?>
                            <!-- UPLOAD -->
                            <input type="file"
                                   name="<?= $key ?>"
                                   accept=".pdf"
                                   class="w-full text-xs"
                                   <?= $key !== 'certificate' ? 'required' : '' ?>>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <p class="text-xs text-gray-500 mt-2">
        Accepted format: PDF | Maximum file size: 5MB per file
    </p>

    <div class="text-right mt-3">
        <button type="button"
                onclick="prevStep(5)"
                class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">
            Previous
        </button>

       <!-- Submit Button -->
<button type="button" id="submitApplication" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">
    Submit Application
</button>

    </div>
</div>

    </form>
</main>
</div>
</div>
<footer class="flex-shrink-0 w-full bg-gray-100 py-4 border-t mt-auto">
    <div class="flex justify-end px-6 text-xs text-gray-600">
        <div class="text-right">
            &copy; <?= date('Y') ?> CLSU-HRMO. All rights reserved.<br>
            Powered by <span class="text-green-700">Management Information System Office (CLSU-MISO)</span>
        </div>
    </div>
</footer>



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function nextStep(current) {
    document.getElementById('step-' + current).classList.add('hidden');
    document.getElementById('step-' + (current + 1)).classList.remove('hidden');
}
function prevStep(current) {
    document.getElementById('step-' + current).classList.add('hidden');
    document.getElementById('step-' + (current - 1)).classList.remove('hidden');
}

// SweetAlert2 confirmation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('applicationForm');
    const submitBtn = document.getElementById('submitApplication');

    submitBtn.addEventListener('click', function(e) {
        e.preventDefault(); // prevent immediate form submission
        Swal.fire({
            title: 'Are you sure?',
            text: "Please confirm that all your data and information are correct.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0B6B3A',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('applicationForm');
    const submitBtn = document.getElementById('submitApplication');

    submitBtn.addEventListener('click', function () {

        Swal.fire({
            title: 'Are you sure?',
            text: 'Please confirm that all your data and information are correct.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0B6B3A',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Application Submitted!',
                        text: 'Your job application has been successfully sent.',
                        timer: 2500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '<?= base_url("dashboard") ?>';
                    });
                })
                .catch(err => {
                    Swal.fire('Error', 'Submission failed. Try again.', 'error');
                    console.error(err);
                });
            }
        });
    });
});
</script>

</body>
</html>
