<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Job Application | Step 1 - Personal Information</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

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



    <!-- Additional Personal Details -->
    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">
        Additional Personal Details
    </div>
    <table class="table-auto w-full border-collapse text-xs mb-4">
        <tbody>

            <!-- ROW 1 -->
            <tr>
                <td class="border p-0" colspan="3">
                    <table class="w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-2 py-1 border w-1/2">Are you CLSU Permanent Employee?</th>
                                <th class="px-2 py-1 border w-1/2">Religion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-2 py-1 border">
                                    <select name="is_clsu_employee"
                                            required
                                            class="w-full text-xs px-2 py-1"
                                            onchange="toggleSpecify(this, 'clsu_specify')">
                                        <option value="">Select</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    <input type="text"
                                           id="clsu_specify"
                                           name="clsu_employee_specify"
                                           placeholder="Specify"
                                           class="w-full text-xs px-2 py-1 mt-1 hidden">
                                </td>
                                <td class="px-2 py-1 border">
                                    <input type="text"
                                           name="religion"
                                           placeholder="Enter Religion"
                                           required
                                           class="w-full text-xs px-2 py-1">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>

            <!-- ROW 2 -->
            <tr>
                <td class="border p-0" colspan="3">
                    <table class="w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-2 py-1 border w-1/3">Indigenous Person</th>
                                <th class="px-2 py-1 border w-1/3">Person with Disability</th>
                                <th class="px-2 py-1 border w-1/3">Solo Parent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-2 py-1 border">
                                    <select name="is_indigenous"
                                            required
                                            class="w-full text-xs px-2 py-1"
                                            onchange="toggleSpecify(this, 'indigenous_specify')">
                                        <option value="">Select</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    <input type="text"
                                           id="indigenous_specify"
                                           name="indigenous_specify"
                                           placeholder="Specify"
                                           class="w-full text-xs px-2 py-1 mt-1 hidden">
                                </td>

                                <td class="px-2 py-1 border">
                                    <select name="is_pwd"
                                            required
                                            class="w-full text-xs px-2 py-1"
                                            onchange="toggleSpecify(this, 'pwd_specify')">
                                        <option value="">Select</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    <input type="text"
                                           id="pwd_specify"
                                           name="pwd_specify"
                                           placeholder="Specify"
                                           class="w-full text-xs px-2 py-1 mt-1 hidden">
                                </td>

                                <td class="px-2 py-1 border">
                                    <select name="is_solo_parent"
                                            required
                                            class="w-full text-xs px-2 py-1">
                                        <option value="">Select</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>

        </tbody>
    </table>

    <!-- Script for toggling specify input -->
    <script>
    function toggleSpecify(select, inputId) {
        const input = document.getElementById(inputId);
        if (select.value === 'Yes') {
            input.classList.remove('hidden');
            input.setAttribute('required', 'required');
        } else {
            input.classList.add('hidden');
            input.value = '';
            input.removeAttribute('required');
        }
    }
    </script>

    <!-- Personal Information -->
    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">Personal Information</div>

    <!-- Hidden inputs to store data in DB -->
    <input type="hidden" name="first_name" value="<?= esc($profile['first_name'] ?? '') ?>">
    <input type="hidden" name="middle_name" value="<?= esc($profile['middle_name'] ?? '') ?>">
    <input type="hidden" name="last_name" value="<?= esc($profile['last_name'] ?? '') ?>">
    <input type="hidden" name="extension" value="<?= esc($profile['suffix'] ?? '') ?>">
    <input type="hidden" name="sex" value="<?= esc($profile['sex'] ?? '') ?>">
    <input type="hidden" name="date_of_birth" value="<?= esc($profile['date_of_birth'] ?? '') ?>">
    <input type="hidden" name="civil_status" value="<?= esc($profile['civil_status'] ?? '') ?>">
    <input type="hidden" name="email" value="<?= esc($profile['email'] ?? '') ?>">
    <input type="hidden" name="phone" value="<?= esc($profile['phone'] ?? '') ?>">
    <input type="hidden" name="citizenship" value="<?= esc($profile['citizenship'] ?? '') ?>">
    <input type="hidden" name="residential_address" value="<?= esc($profile['residential_address'] ?? '') ?>">
    <input type="hidden" name="permanent_address" value="<?= esc($profile['permanent_address'] ?? '') ?>">

    <!-- Visible UI table -->
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
                <td class="px-2 py-1 border"><?= esc($profile['first_name'] ?? '-') ?></td>
                <td class="px-2 py-1 border"><?= esc($profile['middle_name'] ?? '-') ?></td>
                <td class="px-2 py-1 border"><?= esc($profile['last_name'] ?? '-') ?></td>
                <td class="px-2 py-1 border"><?= esc($profile['suffix'] ?? '-') ?></td>
            </tr>
        </tbody>
    </table>

    <table class="table-auto w-full border-collapse text-xs mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 border">Sex</th>
                <th class="px-2 py-1 border">Date of Birth</th>
                <th class="px-2 py-1 border">Civil Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-2 py-1 border"><?= esc($profile['sex'] ?? '-') ?></td>
                <td class="px-2 py-1 border"><?= !empty($profile['date_of_birth']) ? date('F j, Y', strtotime($profile['date_of_birth'])) : '-' ?></td>
                <td class="px-2 py-1 border"><?= esc($profile['civil_status'] ?? '-') ?></td>
            </tr>
        </tbody>
    </table>

    <table class="table-auto w-full border-collapse text-xs mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 border">Email</th>
                <th class="px-2 py-1 border">Phone</th>
                <th class="px-2 py-1 border">Citizenship</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-2 py-1 border"><?= esc($profile['email'] ?? '-') ?></td>
                <td class="px-2 py-1 border"><?= esc($profile['phone'] ?? '-') ?></td>
                <td class="px-2 py-1 border"><?= esc($profile['citizenship'] ?? '-') ?></td>
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
                <td class="px-2 py-1 border"><?= esc($profile['residential_address'] ?? '-') ?></td>
                <td class="px-2 py-1 border"><?= esc($profile['permanent_address'] ?? '-') ?></td>
            </tr>
        </tbody>
    </table>

    <div class="text-right mt-2">
        <button type="button" id="step1Next" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">
            Next
        </button>
    </div>

</div>

<!-- Step 2: Family Background -->
<div class="step hidden" id="step-2">
    <div>
        <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">
            Family Background
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
                                <td class="px-1 py-1 border"><?= esc($spouse['last_name'] ?? 'N/A') ?></td>
                                <td class="px-1 py-1 border"><?= esc($spouse['first_name'] ?? 'N/A') ?></td>
                                <td class="px-1 py-1 border"><?= esc($spouse['middle_name'] ?? 'N/A') ?></td>
                                <td class="px-1 py-1 border"><?= esc($spouse['extension'] ?? 'N/A') ?></td>
                            </tr>
                        </tbody>
                    </table>
            
                    <!-- Hidden Inputs -->
                    <input type="hidden" name="spouse_surname" value="<?= esc($spouse['last_name'] ?? 'N/A') ?>">
                    <input type="hidden" name="spouse_first_name" value="<?= esc($spouse['first_name'] ?? 'N/A') ?>">
                    <input type="hidden" name="spouse_middle_name" value="<?= esc($spouse['middle_name'] ?? 'N/A') ?>">
                    <input type="hidden" name="spouse_ext_name" value="<?= esc($spouse['extension'] ?? 'N/A') ?>">
                </div>
            
                <div class="overflow-x-auto mb-2">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">Contact Number</th>
                                <th class="px-1 py-1 border">Occupation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border"><?= esc($spouse['contact_no'] ?? 'N/A') ?></td>
                                <td class="px-1 py-1 border"><?= esc($spouse['occupation'] ?? 'N/A') ?></td>
                            </tr>
                        </tbody>
                    </table>
            
                    <!-- Hidden Inputs -->
                    <input type="hidden" name="spouse_contact" value="<?= esc($spouse['contact_no'] ?? 'N/A') ?>">
                    <input type="hidden" name="spouse_occupation" value="<?= esc($spouse['occupation'] ?? 'N/A') ?>">
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
                                <td class="px-1 py-1 border"><?= esc($father['last_name'] ?? 'N/A') ?></td>
                                <td class="px-1 py-1 border"><?= esc($father['first_name'] ?? 'N/A') ?></td>
                                <td class="px-1 py-1 border"><?= esc($father['middle_name'] ?? 'N/A') ?></td>
                                <td class="px-1 py-1 border"><?= esc($father['extension'] ?? 'N/A') ?></td>
                            </tr>
                        </tbody>
                    </table>
            
                    <!-- Hidden Inputs -->
                    <input type="hidden" name="father_surname" value="<?= esc($father['last_name'] ?? 'N/A') ?>">
                    <input type="hidden" name="father_first_name" value="<?= esc($father['first_name'] ?? 'N/A') ?>">
                    <input type="hidden" name="father_middle_name" value="<?= esc($father['middle_name'] ?? 'N/A') ?>">
                    <input type="hidden" name="father_ext_name" value="<?= esc($father['extension'] ?? 'N/A') ?>">
                </div>
            
                <div class="overflow-x-auto mb-2">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">Contact Number</th>
                                <th class="px-1 py-1 border">Occupation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border"><?= esc($father['contact_no'] ?? 'N/A') ?></td>
                                <td class="px-1 py-1 border"><?= esc($father['occupation'] ?? 'N/A') ?></td>
                            </tr>
                        </tbody>
                    </table>
            
                    <!-- Hidden Inputs -->
                    <input type="hidden" name="father_contact" value="<?= esc($father['contact_no'] ?? 'N/A') ?>">
                    <input type="hidden" name="father_occupation" value="<?= esc($father['occupation'] ?? 'N/A') ?>">
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
                                <td class="px-1 py-1 border"><?= esc($mother['last_name'] ?? 'N/A') ?></td>
                                <td class="px-1 py-1 border"><?= esc($mother['first_name'] ?? 'N/A') ?></td>
                                <td class="px-1 py-1 border"><?= esc($mother['middle_name'] ?? 'N/A') ?></td>
                            </tr>
                        </tbody>
                    </table>
            
                    <!-- Hidden Inputs -->
                    <input type="hidden" name="mother_maiden_surname" value="<?= esc($mother['last_name'] ?? 'N/A') ?>">
                    <input type="hidden" name="mother_first_name" value="<?= esc($mother['first_name'] ?? 'N/A') ?>">
                    <input type="hidden" name="mother_middle_name" value="<?= esc($mother['middle_name'] ?? 'N/A') ?>">
                </div>
            
                <div class="overflow-x-auto mb-2">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">Contact Number</th>
                                <th class="px-1 py-1 border">Occupation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border"><?= esc($mother['contact_no'] ?? 'N/A') ?></td>
                                <td class="px-1 py-1 border"><?= esc($mother['occupation'] ?? 'N/A') ?></td>
                            </tr>
                        </tbody>
                    </table>
            
                    <!-- Hidden Inputs -->
                    <input type="hidden" name="mother_contact" value="<?= esc($mother['contact_no'] ?? 'N/A') ?>">
                    <input type="hidden" name="mother_occupation" value="<?= esc($mother['occupation'] ?? 'N/A') ?>">
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


<?php
$db = \Config\Database::connect();
$user_id = session()->get('user_id');

// Fetch all applicant education for this user
$educationRecords = $db->table('applicant_education')
                       ->where('user_id', $user_id)
                       ->orderBy('degree_level_id', 'ASC')
                       ->get()
                       ->getResultArray();

// Fetch all degree levels
$libDegreeLevels = $db->table('lib_degree_level')->get()->getResultArray();

// Prepare final rows grouped by level
$finalEducation = [];

foreach($libDegreeLevels as $levelObj){
    $levelId = $levelObj['id_degree_level'];
    $levelName = $levelObj['degree_level_name'];

    // Filter records for this level
    $levelRecords = array_filter($educationRecords, fn($r) => $r['degree_level_id'] == $levelId);

    // If no records exist, just add a placeholder
    if(empty($levelRecords)){
        $finalEducation[] = [
            'level_name' => $levelName,
            'school_name' => '-',
            'degree_course' => '-',
            'period_from' => '-',
            'period_to' => '-',
            'highest_level_units' => '-',
            'year_graduated' => '-',
            'awards' => '-',
        ];
    } else {
        // Add all records for this level
        $firstRow = true;
        foreach($levelRecords as $edu){
            $finalEducation[] = [
                'level_name' => $firstRow ? $levelName : '', // only show level on first row
                'school_name' => $edu['school_name'] ?? '-',
                'degree_course' => $edu['degree_course'] ?? '-',
                'period_from' => $edu['period_from'] ?? '-',
                'period_to' => $edu['period_to'] ?? '-',
                'highest_level_units' => $edu['highest_level_units'] ?? '-',
                'year_graduated' => $edu['year_graduated'] ?? '-',
                'awards' => $edu['awards'] ?? '-',
            ];
            $firstRow = false;
        }
    }
}
?>

<!-- Step 3: Educational Background -->
<div class="step hidden" id="step-3">
    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">Educational Background</div>

    <div class="overflow-x-auto mb-5">
        <table class="table-auto w-full text-left border-collapse text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 border">Level</th>
                    <th class="px-2 py-1 border">Name of School</th>
                    <th class="px-2 py-1 border">Degree / Course</th>
                    <th class="px-2 py-1 border">From</th>
                    <th class="px-2 py-1 border">To</th>
                    <th class="px-2 py-1 border">Highest Level / Units Earned</th>
                    <th class="px-2 py-1 border">Year Graduated</th>
                    <th class="px-2 py-1 border">Scholarship / Academic Honors</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($finalEducation as $edu): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-2 py-1 border font-semibold"><?= esc($edu['level_name']) ?></td>
                    <td class="px-2 py-1 border"><?= esc($edu['school_name']) ?></td>
                    <td class="px-2 py-1 border"><?= esc($edu['degree_course']) ?></td>
                    <td class="px-2 py-1 border"><?= esc($edu['period_from']) ?></td>
                    <td class="px-2 py-1 border"><?= esc($edu['period_to']) ?></td>
                    <td class="px-2 py-1 border"><?= esc($edu['highest_level_units']) ?></td>
                    <td class="px-2 py-1 border"><?= esc($edu['year_graduated']) ?></td>
                    <td class="px-2 py-1 border"><?= esc($edu['awards']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="text-right mt-2">
        <button type="button" onclick="prevStep(3)" class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">
            Previous
        </button>
        <button type="button" id="step3Next" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">
            Next
        </button>
    </div>
</div>
<!-- Step 4: Work Experience (Read-Only) -->
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

    <div class="overflow-x-auto mb-2">
        <table id="work-table" class="table-auto w-full border-collapse text-xs mt-2">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 border">Position Title</th>
                    <th class="px-2 py-1 border">Office / Company</th>
                    <th class="px-2 py-1 border">Inclusive Dates</th>
                    <th class="px-2 py-1 border">Status of Appointment</th>
                    <th class="px-2 py-1 border">Government Service</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($work_experiences)): ?>
                    <?php foreach ($work_experiences as $work): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-1 border">
                                <?= esc($work['position_title'] ?? '-') ?>
                                <input type="hidden" name="position_title[]" value="<?= esc($work['position_title'] ?? 'N/A') ?>">
                            </td>
                            <td class="px-2 py-1 border">
                                <?= esc($work['office'] ?? '-') ?>
                                <input type="hidden" name="office[]" value="<?= esc($work['office'] ?? 'N/A') ?>">
                            </td>
                            <td class="px-2 py-1 border">
                                <?= !empty($work['date_from']) ? date('F d, Y', strtotime($work['date_from'])) : '-' ?> 
                                - 
                                <?= !empty($work['date_to']) ? date('F d, Y', strtotime($work['date_to'])) : '-' ?>
                                <input type="hidden" name="date_from[]" value="<?= !empty($work['date_from']) ? date('Y-m-d', strtotime($work['date_from'])) : '' ?>">
                                <input type="hidden" name="date_to[]" value="<?= !empty($work['date_to']) ? date('Y-m-d', strtotime($work['date_to'])) : '' ?>">
                            </td>
                            <td class="px-2 py-1 border">
                                <?= esc($work['status_of_appointment'] ?? '-') ?>
                                <input type="hidden" name="status_of_appointment[]" value="<?= esc($work['status_of_appointment'] ?? 'N/A') ?>">
                            </td>
                            <td class="px-2 py-1 border">
                                <?= esc($work['govt_service'] ?? '-') ?>
                                <input type="hidden" name="govt_service[]" value="<?= esc($work['govt_service'] ?? 'No') ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="px-2 py-1 border text-center" colspan="5">
                            No work experience added.
                            <!-- Add empty hidden inputs to avoid undefined index errors -->
                            <input type="hidden" name="position_title[]" value="N/A">
                            <input type="hidden" name="office[]" value="N/A">
                            <input type="hidden" name="date_from[]" value="">
                            <input type="hidden" name="date_to[]" value="">
                            <input type="hidden" name="status_of_appointment[]" value="N/A">
                            <input type="hidden" name="govt_service[]" value="No">
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
<?php
$user_id = session()->get('user_id');
$app = $db->table('job_applications')
    ->where('user_id', $user_id)
    ->orderBy('applied_at', 'DESC')
    ->get()
    ->getRowArray();

$civil_services = $db->table('applicant_civil_service')
    ->where('user_id', $user_id)
    ->orderBy('date_of_exam', 'DESC')
    ->get()
    ->getResultArray();
?>

<div class="step hidden" id="step-5">

    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">
        Civil Service Eligibility
    </div>

    <div class="overflow-x-auto mb-2 relative">
        <table id="cs-table" class="table-auto w-full border-collapse text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 border">Eligibility</th>
                    <th class="px-2 py-1 border">Rating</th>
                    <th class="px-2 py-1 border">Date of Exam</th>
                    <th class="px-2 py-1 border">Place of Exam</th>
                    <th class="px-2 py-1 border">License No.</th>
                    <th class="px-2 py-1 border">License Valid Until</th>
                    <th class="px-2 py-1 border">Certificate</th>
                    <th class="px-2 py-1 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($civil_services)): ?>
                    <?php foreach($civil_services as $cs): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-1 border">
                                <?= esc($cs['eligibility'] ?? '-') ?>
                                <input type="hidden" name="eligibility[]" value="<?= esc($cs['eligibility'] ?? '-') ?>">
                            </td>
                            <td class="px-2 py-1 border">
                                <?= esc($cs['rating'] ?? '-') ?>
                                <input type="hidden" name="rating[]" value="<?= esc($cs['rating'] ?? '-') ?>">
                            </td>
                            <td class="px-2 py-1 border">
                                <?= !empty($cs['date_of_exam']) ? date('F d, Y', strtotime($cs['date_of_exam'])) : '-' ?>
                                <input type="hidden" name="date_of_exam[]" value="<?= !empty($cs['date_of_exam']) ? date('Y-m-d', strtotime($cs['date_of_exam'])) : '' ?>">
                            </td>
                            <td class="px-2 py-1 border">
                                <?= esc($cs['place_of_exam'] ?? '-') ?>
                                <input type="hidden" name="place_of_exam[]" value="<?= esc($cs['place_of_exam'] ?? '-') ?>">
                            </td>
                            <td class="px-2 py-1 border">
                                <?= esc($cs['license_no'] ?? '-') ?>
                                <input type="hidden" name="license_no[]" value="<?= esc($cs['license_no'] ?? '-') ?>">
                            </td>
                            <td class="px-2 py-1 border">
                                <?= !empty($cs['license_valid_until']) ? date('F d, Y', strtotime($cs['license_valid_until'])) : '-' ?>
                                <input type="hidden" name="license_valid_until[]" value="<?= !empty($cs['license_valid_until']) ? date('Y-m-d', strtotime($cs['license_valid_until'])) : '' ?>">
                            </td>
                            <td class="px-2 py-1 border text-center">
                                <?php if (!empty($cs['certificate'])): ?>
                                    <button type="button"
                                        class="view-certificate-btn text-blue-600 text-xs font-medium hover:text-blue-800"
                                        data-file="<?= base_url('account/viewCivilCertificate/' . urlencode($cs['certificate'])) ?>">
                                        View
                                    </button>
                                <?php else: ?>-<?php endif; ?>
                            </td>
                            <td class="px-2 py-1 border text-center">
                                <button type="button" class="text-red-500 hover:underline remove-cs-row">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="px-2 py-1 border text-center" colspan="8">
                            No civil service record added.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(5)" class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">
            Previous
        </button>
        <button type="button" onclick="nextStep(5)" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">
            Next
        </button>
    </div>
</div>

<div id="certificate-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-6xl h-full flex flex-col shadow-lg relative">
        <iframe id="certificate-frame" src="" class="flex-1 w-full h-full border-none rounded-b-2xl"></iframe>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const csTableBody = document.querySelector('#cs-table tbody');
    const certModal = document.getElementById('certificate-modal');
    const iframe = document.getElementById('certificate-frame');

    function openCertificate(file) {
        if (!file || file.trim() === '') {
            Swal.fire({icon:'error',title:'File not found',text:'No certificate file is available.'});
            return;
        }
        iframe.src = file;
        certModal.classList.remove('hidden');
    }

    function closeCertificate() {
        iframe.src = '';
        certModal.classList.add('hidden');
    }

    function attachViewCertificate(button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            openCertificate(this.getAttribute('data-file'));
        });
    }

    certModal.addEventListener('click', function (e) {
        if (e.target === certModal) closeCertificate();
    });

    function attachDeleteEvent(button) {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            Swal.fire({
                title:'Are you sure?',
                text:'This will remove the record from the table!',
                icon:'warning',
                showCancelButton:true
            }).then(result=>{
                if(result.isConfirmed){
                    row.remove();
                    if(csTableBody.querySelectorAll('tr').length===0){
                        csTableBody.innerHTML=`<tr><td class="px-2 py-1 border text-center" colspan="8">No civil service record added.</td></tr>`;
                    }
                }
            });
        });
    }

    document.querySelectorAll('.remove-cs-row').forEach(attachDeleteEvent);
    document.querySelectorAll('.view-certificate-btn').forEach(attachViewCertificate);

});
</script>
<?php
$user_id = session()->get('user_id');
$trainings = $db->table('applicant_trainings at')
    ->select('at.*, tc.training_category_name')
    ->join('lib_training_category tc', 'tc.id_training_category = at.training_category_id', 'left')
    ->where('at.user_id', $user_id)
    ->orderBy('at.date_from', 'DESC')
    ->get()
    ->getResultArray();
?>

<div class="step hidden" id="step-6">

    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">
        Trainings
    </div>

    <div class="overflow-x-auto mb-2 relative">
        <table id="training-table" class="table-auto w-full border-collapse text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 border">Category</th>
                    <th class="px-2 py-1 border">Training Name</th>
                    <th class="px-2 py-1 border">Venue</th>
                    <th class="px-2 py-1 border">Date From</th>
                    <th class="px-2 py-1 border">Date To</th>
                    <th class="px-2 py-1 border">Facilitator</th>
                    <th class="px-2 py-1 border">Hours</th>
                    <th class="px-2 py-1 border">Sponsor</th>
                    <th class="px-2 py-1 border">Remarks</th>
                    <th class="px-2 py-1 border">Certificate</th>
                    <th class="px-2 py-1 border text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($trainings)): ?>
                <?php foreach ($trainings as $tr): ?>
                    <tr>
                        <td class="px-2 py-1 border">
                            <?= esc($tr['training_category_name']) ?>
                            <input type="hidden" name="training_category_id[]" value="<?= esc($tr['training_category_id']) ?>">
                        </td>
                        <td class="px-2 py-1 border">
                            <?= esc($tr['training_name']) ?>
                            <input type="hidden" name="training_name[]" value="<?= esc($tr['training_name']) ?>">
                        </td>
                        <td class="px-2 py-1 border">
                            <?= esc($tr['training_venue']) ?>
                            <input type="hidden" name="training_venue[]" value="<?= esc($tr['training_venue']) ?>">
                        </td>
                        <td class="px-2 py-1 border">
                            <?= !empty($tr['date_from']) ? date('F d, Y', strtotime($tr['date_from'])) : '-' ?>
                            <input type="hidden" name="training_date_from[]" value="<?= !empty($tr['date_from']) ? date('Y-m-d', strtotime($tr['date_from'])) : '' ?>">
                        </td>
                        <td class="px-2 py-1 border">
                            <?= !empty($tr['date_to']) ? date('F d, Y', strtotime($tr['date_to'])) : '-' ?>
                            <input type="hidden" name="training_date_to[]" value="<?= !empty($tr['date_to']) ? date('Y-m-d', strtotime($tr['date_to'])) : '' ?>">
                        </td>
                        <td class="px-2 py-1 border">
                            <?= esc($tr['training_facilitator']) ?>
                            <input type="hidden" name="training_facilitator[]" value="<?= esc($tr['training_facilitator']) ?>">
                        </td>
                        <td class="px-2 py-1 border">
                            <?= esc($tr['training_hours']) ?>
                            <input type="hidden" name="training_hours[]" value="<?= esc($tr['training_hours']) ?>">
                        </td>
                        <td class="px-2 py-1 border">
                            <?= esc($tr['training_sponsor']) ?>
                            <input type="hidden" name="training_sponsor[]" value="<?= esc($tr['training_sponsor']) ?>">
                        </td>
                        <td class="px-2 py-1 border">
                            <?= esc($tr['training_remarks']) ?>
                            <input type="hidden" name="training_remarks[]" value="<?= esc($tr['training_remarks']) ?>">
                        </td>
                        <td class="px-2 py-1 border text-center">
                            <?php if (!empty($tr['certificate_file'])): ?>
                                <button type="button"
                                    class="viewCertificateBtn text-blue-600 text-xs font-medium hover:text-blue-800"
                                    data-file="<?= base_url('file/view-training/' . $tr['id_applicant_training'] . '/' . $tr['certificate_file']) ?>">
                                    View
                                </button>
                                <input type="hidden" name="existing_certificate_file[]" value="<?= esc($tr['certificate_file']) ?>">
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">No certificate</span>
                                <input type="hidden" name="existing_certificate_file[]" value="">
                            <?php endif; ?>
                        </td>
                        <td class="px-2 py-1 border text-center">
                            <button type="button" class="deleteTrainingBtn text-red-600 px-1">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="px-2 py-1 border text-center">
                        No training record added.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(6)" class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">
            Previous
        </button>
        <button type="button" onclick="nextStep(6)" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">
            Next
        </button>
    </div>
</div>

<div id="certificateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-6xl h-[90vh] shadow-lg">
        <iframe id="certificateFrame" src="" class="w-full h-full border-none"></iframe>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const tbody = document.querySelector('#training-table tbody');
    const modal = document.getElementById('certificateModal');
    const frame = document.getElementById('certificateFrame');

    function checkEmptyTable() {
        if (!tbody.querySelector('tr')) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="11" class="px-2 py-1 border text-center text-gray-500">
                        No training record added.
                    </td>
                </tr>`;
        }
    }

    tbody.addEventListener('click', function(e) {

        const viewBtn = e.target.closest('.viewCertificateBtn');
        const deleteBtn = e.target.closest('.deleteTrainingBtn');

        if (viewBtn) {
            e.preventDefault();
            e.stopPropagation();
            frame.src = viewBtn.dataset.file;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            return;
        }

        if (deleteBtn) {
            const row = deleteBtn.closest('tr');
            const trainingName = row.children[1].textContent.trim();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will remove the record from the table!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(result => {
                if (result.isConfirmed) {
                    row.remove();
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: `Training "${trainingName}" has been deleted.`,
                        timer: 1200,
                        showConfirmButton: false
                    });
                    checkEmptyTable();
                }
            });
        }

    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            frame.src = '';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });

});
</script>

<?php
$user_id = session()->get('user_id');

/*
 | Fetch USER documents (source of truth)
 */
$documents = $db->table('applicant_documents')
    ->where('user_id', $user_id)
    ->get()
    ->getRowArray();

$documents = $documents ?? [
    'pds'         => '',
    'performance' => '',
    'resume'      => '',
    'tor'         => '',
    'diploma'     => ''
];
?>

<!-- Step 7: File Attachments (VIEW ONLY) -->
<div class="step hidden" id="step-7">
    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">
        File Attachments
    </div>

    <?php
    $docLabels = [
        'pds'         => '1. Fully accomplished Personal Data Sheet (PDS)',
        'performance' => '2. Performance rating (last rating period)',
        'resume'      => '3. Resume (PDF)',
        'tor'         => '4. Transcript of Records (TOR)',
        'diploma'     => '5. Diploma'
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
                            <button 
                                type="button"
                                class="viewFileBtn text-blue-600 text-xs font-medium hover:text-blue-800"
                                data-file="<?= base_url('file/viewFile/' . $documents[$key]) ?>">
                                View
                            </button>
                            <input type="hidden" name="existing_<?= $key ?>" value="<?= esc($documents[$key]) ?>">
                        <?php else: ?>
                            <span class="text-red-600 text-xs">
                                No file available
                            </span>
                            <input type="hidden" name="existing_<?= $key ?>" value="">
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Navigation Buttons -->
    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(7)"
                class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">
            Previous
        </button>

        <!-- Only this submits -->
        <button type="submit"
                class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800"
                id="submitApplication">
            Submit Application
        </button>
    </div>
</div>

<!-- File Preview Modal -->
<div id="fileModal" 
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white w-full max-w-6xl h-[90vh] shadow-lg">
        <iframe id="fileFrame" src="" class="w-full h-full" frameborder="0"></iframe>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const modal = document.getElementById('fileModal');
    const frame = document.getElementById('fileFrame');

    // Open file preview
    document.addEventListener('click', function(e) {
        const viewBtn = e.target.closest('.viewFileBtn');

        if (viewBtn) {
            e.preventDefault();
            e.stopPropagation();

            const file = viewBtn.dataset.file;

            if (!file) return;

            frame.src = file;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    });

    // Close modal when clicking outside iframe
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            frame.src = '';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });

});
</script>


<script>
// Navigate to next step
function nextStep(currentStep){
    document.getElementById('step-'+currentStep).classList.add('hidden');
    document.getElementById('step-'+(currentStep+1)).classList.remove('hidden');
}

// Navigate to previous step
function prevStep(currentStep){
    document.getElementById('step-'+currentStep).classList.add('hidden');
    document.getElementById('step-'+(currentStep-1)).classList.remove('hidden');
}
</script>
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
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ---------- Navigation Steps ----------
    function nextStep(current){
        document.getElementById('step-'+current).classList.add('hidden');
        document.getElementById('step-'+(current+1)).classList.remove('hidden');
    }

    function prevStep(current){
        document.getElementById('step-'+current).classList.add('hidden');
        document.getElementById('step-'+(current-1)).classList.remove('hidden');
    }

    // ---------- Step Validations ----------
    const stepValidations = [
        {btnId: 'step1Next', step: 1},
        {btnId: 'step2Next', step: 2},
        {btnId: 'step3Next', step: 3}
    ];

    stepValidations.forEach(item => {
        const btn = document.getElementById(item.btnId);
        if (btn) {
            btn.addEventListener('click', function() {
                const requiredFields = document.querySelectorAll('#step-' + item.step + ' [required]');
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
                        text: 'Please fill out all required fields before proceeding.'
                    });
                    return;
                }
                nextStep(item.step);
            });
        }
    });

    // ---------- Submit Form ----------
    const form = document.getElementById('applicationForm');
    const submitBtn = document.getElementById('submitApplication');

    submitBtn.addEventListener('click', function(e){
        e.preventDefault();

        // Confirm submission
        Swal.fire({
            title: 'Are you sure?',
            text: 'Please confirm that all your data are correct.',
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
                .then(res => res.text())
                .then(text => {
                    let data;
                    try { data = JSON.parse(text); } 
                    catch { data = { success: false, message: 'Invalid server response' }; }

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Application Submitted!',
                            text: 'Your application has been successfully saved.',
                            timer: 2500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '<?= base_url("dashboard") ?>';
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Submission failed. Try again.', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Submission failed. Try again.', 'error');
                });
            }
        });
    });

});
</script>

</body>
</html>
