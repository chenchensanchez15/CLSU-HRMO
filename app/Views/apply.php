<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<head>
    <title>Application Information | CLSU Online Job Application System</title>
    <link rel="icon" type="image/x-icon" href="/HRMO/public/assets/images/favicon.ico">
</head>

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
            <a href="<?= site_url('dashboard') ?>" class="text-xl font-bold no-underline hover:no-underline" style="text-decoration: none;">CLSU Online Job Application</a>
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
    <div class="mb-4">
        <h3 class="text-clsuGreen font-bold text-sm mb-3">Additional Personal Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
            <!-- CLSU Employee -->
            <div class="relative">
                <label class="block text-xs font-medium text-clsuGreen mb-1">Are you CLSU Employee?</label>
                <select name="is_clsu_employee"
                        id="clsu_employee_select"
                        required
                        class="w-full text-xs px-2 py-1 border border-clsuGreen rounded"
                        onchange="handleClsuMainSelection(this)">
                    <option value="">Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
                <select name="clsu_employee_type"
                        id="clsu_employee_detail"
                        required
                        class="w-full mt-1 text-xs px-2 py-1 border border-clsuGreen rounded hidden"
                        onchange="handleClsuDetailSelection(this)">
                    <option value="">Select employment type</option>
                    <option value="Permanent">Permanent</option>
                    <option value="Temporary">Temporary</option>
                    <option value="COS/JO">COS/JO</option>
                </select>
                <input type="text"
                       id="clsu_specify"
                       name="clsu_employee_specify"
                       placeholder="Specify position"
                       class="w-full mt-1 text-xs px-2 py-1 border border-clsuGreen rounded hidden">
                <input type="hidden" name="is_clsu_employee" id="clsu_employee_hidden">
            </div>
            
            <!-- Person with Disability -->
            <div class="relative">
                <label class="block text-xs font-medium text-clsuGreen mb-1">Person with Disability</label>
                <select name="is_pwd"
                        id="pwd_select"
                        required
                        class="w-full text-xs px-2 py-1 border border-clsuGreen rounded"
                        onchange="handlePwdMainSelection(this)">
                    <option value="">Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
                <select name="pwd_type"
                        id="pwd_detail"
                        required
                        class="w-full mt-1 text-xs px-2 py-1 border border-clsuGreen rounded hidden"
                        onchange="handlePwdDetailSelection(this)">
                    <option value="">Select disability type</option>
                    <option value="visual impairment">Visual Impairment</option>
                    <option value="hearing loss">Hearing Loss</option>
                    <option value="orthopedic disability">Orthopedic Disability</option>
                    <option value="learning disability">Learning Disability</option>
                    <option value="psychosocial disability">Psychosocial Disability</option>
                    <option value="chronic illness">Chronic Illness</option>
                    <option value="mental disability">Mental Disability</option>
                    <option value="others">Others</option>
                </select>
                <input type="text"
                       id="pwd_specify"
                       name="pwd_specify"
                       placeholder="Specify disability"
                       class="w-full mt-1 text-xs px-2 py-1 border border-clsuGreen rounded hidden">
                <input type="hidden" name="pwd_type" id="pwd_type_hidden">
                <input type="hidden" name="is_pwd" id="pwd_hidden">
            </div>
            
            <!-- Indigenous Person -->
            <div class="relative">
                <label class="block text-xs font-medium text-clsuGreen mb-1">Indigenous Person</label>
                <select name="is_indigenous"
                        id="indigenous_select"
                        required
                        class="w-full text-xs px-2 py-1 border border-clsuGreen rounded"
                        onchange="handleIndigenousMainSelection(this)">
                    <option value="">Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
                <input type="text"
                       id="indigenous_specify"
                       name="indigenous_specify"
                       placeholder="Specify indigenous group"
                       class="w-full mt-1 text-xs px-2 py-1 border border-clsuGreen rounded hidden">
                <input type="hidden" name="is_indigenous" id="indigenous_hidden">
            </div>
        </div>
        
        <!-- Row 2: Religion and Solo Parent -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <!-- Religion -->
            <div>
                <label class="block text-xs font-medium text-clsuGreen mb-1">Religion</label>
                <input type="text"
                       name="religion"
                       placeholder="Please enter your religion"
                       required
                       class="w-full text-xs px-2 py-1 border border-clsuGreen rounded">
            </div>
            
            <!-- Solo Parent -->
            <div>
                <label class="block text-xs font-medium text-clsuGreen mb-1">Solo Parent</label>
                <select name="is_solo_parent"
                        required
                        class="w-full text-xs px-2 py-1 border border-clsuGreen rounded">
                    <option value="">Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>
    </div>

    <div class="text-right mt-2">
        <button type="button" id="step1Next" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">
            Next
        </button>
    </div>
</div>

<div class="step hidden" id="step-2">
<?php if(empty($createdBy)): ?>
<!-- Verification & Edit Prompt (Yellow) -->
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 rounded">
    <div class="flex items-start">
        <!-- Icon -->
        <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"></path>
        </svg>

        <!-- Content -->
        <div class="flex-1">
            <h4 class="text-sm font-semibold text-yellow-800 mb-1">
                Please verify that all information is correct
            </h4>
            <p class="text-xs text-yellow-700 mb-2">
                Review the details below carefully. Ensure accuracy for further processing.
                If you need to make changes to your personal information, you can edit it before submission.
            </p>

            <!-- Smaller Edit Button -->
            <button id="editFromAlert" 
                    type="button"
                    class="inline-flex items-center px-2 py-1 bg-clsuGreen text-white text-[10px] font-medium rounded hover:bg-green-800 transition-colors">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Personal Info
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

    <!-- Personal Information Details (2-Row Format) -->
    <div class="space-y-3 text-xs">
        <h3 class="text-xs font-semibold text-gray-700 mb-2 flex items-center">
            <svg class="w-3.5 h-3.5 mr-1 text-clsuGreen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Personal Information
        </h3>
        
<!-- ROW 1 -->
<div class="grid grid-cols-6 gap-4 mb-3 text-sm">

    <!-- FULL NAME -->
    <div>
        <p class="text-[10px] font-semibold text-gray-600">Full Name</p>
        <p class="font-medium text-gray-800">
            <?= esc(trim(($profile['first_name'] ?? '') . ' ' . ($profile['middle_name'] ?? '') . ' ' . ($profile['last_name'] ?? '') . (($profile['suffix'] ?? '') ? ' ' . ($profile['suffix'] ?? '') : '')) ?: '-') ?>
        </p>
    </div>

    <!-- SEX -->
    <div>
        <p class="text-[10px] font-semibold text-gray-600">Sex</p>
        <p class="font-medium text-gray-800"><?= esc($profile['sex'] ?? '-') ?></p>
    </div>

    <!-- DATE OF BIRTH -->
    <div>
        <p class="text-[10px] font-semibold text-gray-600">Date of Birth</p>
        <p class="font-medium text-gray-800">
            <?= !empty($profile['date_of_birth']) ? date('F j, Y', strtotime($profile['date_of_birth'])) : '-' ?>
        </p>
    </div>

    <!-- CIVIL STATUS -->
    <div>
        <p class="text-[10px] font-semibold text-gray-600">Civil Status</p>
        <p class="font-medium text-gray-800"><?= esc($profile['civil_status'] ?? '-') ?></p>
    </div>

    <!-- PHONE -->
    <div>
        <p class="text-[10px] font-semibold text-gray-600">Phone</p>
        <p class="font-medium text-gray-800"><?= esc($profile['phone'] ?? '-') ?></p>
    </div>

    <!-- CITIZENSHIP -->
    <div>
        <p class="text-[10px] font-semibold text-gray-600">Citizenship</p>
        <p class="font-medium text-gray-800"><?= esc($profile['citizenship'] ?? '-') ?></p>
    </div>

</div>


<!-- ROW 2 -->
<div class="grid grid-cols-3 gap-4 pb-2 border-b border-gray-200 text-sm">

    <!-- EMAIL -->
    <div>
        <p class="text-[10px] font-semibold text-gray-600">Email</p>
        <p class="font-medium text-gray-800 break-words">
            <?= esc($profile['email'] ?? '-') ?>
        </p>
    </div>

    <!-- RESIDENTIAL ADDRESS -->
    <div>
        <p class="text-[10px] font-semibold text-gray-600">Residential Address</p>
        <p class="font-medium text-gray-800">
            <?= esc($profile['residential_address'] ?? '-') ?>
        </p>
    </div>

    <!-- PERMANENT ADDRESS -->
    <div>
        <p class="text-[10px] font-semibold text-gray-600">Permanent Address</p>
        <p class="font-medium text-gray-800">
            <?= esc($profile['permanent_address'] ?? '-') ?>
        </p>
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
            'course' => '-',
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
                'course' => $edu['course'] ?? '-',
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
<?php if(empty($createdBy)): ?>
    <!-- Verification & Edit Prompt for Educational Background -->
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 rounded">
    <div class="flex items-start">
        <!-- Icon -->
        <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"></path>
        </svg>

        <!-- Content -->
        <div class="flex-1">
            <h4 class="text-sm font-semibold text-yellow-800 mb-1">
                Please verify that all information is correct
            </h4>
            <p class="text-xs text-yellow-700 mb-2">
                Review the details below carefully. Ensure accuracy for further processing. 
                If you need to make changes to your educational background, you can edit it before submission.
            </p>

            <!-- Smaller Edit Button -->
            <button id="editEducationBtn" 
                    type="button"
                    class="inline-flex items-center px-2 py-1 bg-clsuGreen text-white text-[10px] font-medium rounded hover:bg-green-800 transition-colors">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Education Info
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

    <!-- Section Header with Icon -->
    <div class="px-3 py-2 mb-4 flex items-center">
        <svg class="w-4 h-4 text-clsuGreen mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
        <h3 class="text-clsuGreen font-semibold text-sm">Educational Background</h3>
    </div>
    
    <div class="overflow-x-auto mb-5">
        <table class="table-auto w-full text-left border-collapse text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Level</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Name of School</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Degree</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Course</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">From</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">To</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Highest Level / Units Earned</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Year Graduated</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Scholarship / Academic Honors</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <?php foreach($finalEducation as $index => $edu): ?>
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-3 py-2 border-b border-gray-200 font-semibold text-gray-800"><?= esc($edu['level_name']) ?></td>
                    <td class="px-3 py-2 border-b border-gray-200 text-gray-700"><?= esc($edu['school_name']) ?></td>
                    <td class="px-3 py-2 border-b border-gray-200 text-gray-700"><?= esc($edu['degree_course']) ?></td>
                    <td class="px-3 py-2 border-b border-gray-200 text-gray-700"><?= esc($edu['course'] ?? '-') ?></td>
                    <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($edu['period_from']) ?></td>
                    <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($edu['period_to']) ?></td>
                    <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($edu['highest_level_units']) ?></td>
                    <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($edu['year_graduated']) ?></td>
                    <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($edu['awards']) ?></td>
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

<!-- Verification & Edit Prompt for Work Experience -->
<?php if(empty($createdBy)): ?>
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 rounded">
    <div class="flex items-start">
        <!-- Icon -->
        <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"></path>
        </svg>

        <!-- Content -->
        <div class="flex-1">
            <h4 class="text-sm font-semibold text-yellow-800 mb-1">
                Please verify that all information is correct
            </h4>
            <p class="text-xs text-yellow-700 mb-2">
                Review the details below carefully. Ensure accuracy for further processing. 
                If you need to make changes to your work experience, you can edit it before submission.
            </p>

            <!-- Smaller Edit Button -->
            <button id="editWorkBtn" 
                    type="button"
                    class="inline-flex items-center px-2 py-1 bg-clsuGreen text-white text-[10px] font-medium rounded hover:bg-green-800 transition-colors">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Work Experience
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

    
    <!-- Section Header with Icon -->
    <div class="px-3 py-2 mb-4 flex items-center">
        <svg class="w-4 h-4 text-clsuGreen mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
        <h3 class="text-clsuGreen font-semibold text-sm">Work Experience</h3>
    </div>
    

    <?php
    $user_id = session()->get('user_id');
    $work_experiences = $db->table('applicant_work_experience')
        ->where('user_id', $user_id)
        ->orderBy('date_from', 'DESC')
        ->get()
        ->getResultArray();
    ?>

    <div class="overflow-x-auto mb-5">
        <table id="work-table" class="table-auto w-full border-collapse text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Position Title</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Office / Company</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Inclusive Dates</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Status of Appointment</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Government Service</th>
                </tr>
            </thead>

            <tbody class="bg-white">
                <?php if (!empty($work_experiences)): ?>
                    <?php foreach ($work_experiences as $work): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-3 py-2 border-b border-gray-200 text-gray-800 font-medium"><?= esc($work['position_title'] ?? '-') ?></td>
                            <td class="px-3 py-2 border-b border-gray-200 text-gray-700"><?= esc($work['office'] ?? '-') ?></td>
                            <td class="px-3 py-2 border-b border-gray-200 text-gray-600">
                                <?= !empty($work['date_from']) ? date('F d, Y', strtotime($work['date_from'])) : '-' ?> 
                                - 
                                <?= !empty($work['date_to']) ? date('F d, Y', strtotime($work['date_to'])) : '-' ?>
                                <input type="hidden" name="date_from[]" value="<?= !empty($work['date_from']) ? date('Y-m-d', strtotime($work['date_from'])) : '' ?>">
                                <input type="hidden" name="date_to[]" value="<?= !empty($work['date_to']) ? date('Y-m-d', strtotime($work['date_to'])) : '' ?>">
                            </td>
                            <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($work['status_of_appointment'] ?? '-') ?></td>
                            <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($work['govt_service'] ?? '-') ?></td>
                            <!-- Hidden inputs for form submission -->
                            <input type="hidden" name="position_title[]" value="<?= esc($work['position_title'] ?? 'N/A') ?>">
                            <input type="hidden" name="office[]" value="<?= esc($work['office'] ?? 'N/A') ?>">
                            <input type="hidden" name="status_of_appointment[]" value="<?= esc($work['status_of_appointment'] ?? 'N/A') ?>">
                            <input type="hidden" name="govt_service[]" value="<?= esc($work['govt_service'] ?? 'No') ?>">
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="px-3 py-2 border-b border-gray-200 text-center text-gray-500" colspan="5">
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
    
<?php if(empty($createdBy)): ?>
<!-- Verification & Edit Prompt for Civil Service -->
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 rounded">
    <div class="flex items-start">
        <!-- Icon -->
        <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"></path>
        </svg>

        <!-- Content -->
        <div class="flex-1">
            <h4 class="text-sm font-semibold text-yellow-800 mb-1">
                Please verify that all information is correct
            </h4>
            <p class="text-xs text-yellow-700 mb-2">
                Review the details below carefully. Ensure accuracy for further processing. 
                If you need to make changes to your civil service information, you can edit it before submission.
            </p>

            <!-- Smaller Edit Button -->
            <button id="editCivilServiceBtn" 
                    type="button"
                    class="inline-flex items-center px-2 py-1 bg-clsuGreen text-white text-[10px] font-medium rounded hover:bg-green-800 transition-colors">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Civil Service Info
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

    
    <!-- Section Header with Icon -->
    <div class="px-3 py-2 mb-4 flex items-center">
        <svg class="w-4 h-4 text-clsuGreen mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>
        <h3 class="text-clsuGreen font-semibold text-sm">Civil Service Eligibility</h3>
    </div>
    
    <div class="overflow-x-auto mb-5 relative">
        <table id="cs-table" class="table-auto w-full border-collapse text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Eligibility</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Rating</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Date of Exam</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Place of Exam</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">License No.</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">License Valid Until</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Certificate</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <?php if (!empty($civil_services)): ?>
                    <?php foreach($civil_services as $cs): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-3 py-2 border-b border-gray-200 text-gray-800 font-medium"><?= esc($cs['eligibility'] ?? '-') ?></td>
                            <td class="px-3 py-2 border-b border-gray-200 text-gray-700"><?= esc($cs['rating'] ?? '-') ?></td>
                            <td class="px-3 py-2 border-b border-gray-200 text-gray-600">
                                <?= !empty($cs['date_of_exam']) ? date('F d, Y', strtotime($cs['date_of_exam'])) : '-' ?>
                                <input type="hidden" name="date_of_exam[]" value="<?= !empty($cs['date_of_exam']) ? date('Y-m-d', strtotime($cs['date_of_exam'])) : '' ?>">
                            </td>
                            <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($cs['place_of_exam'] ?? '-') ?></td>
                            <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($cs['license_no'] ?? '-') ?></td>
                            <td class="px-3 py-2 border-b border-gray-200 text-gray-600">
                                <?= !empty($cs['license_valid_until']) ? date('F d, Y', strtotime($cs['license_valid_until'])) : '-' ?>
                                <input type="hidden" name="license_valid_until[]" value="<?= !empty($cs['license_valid_until']) ? date('Y-m-d', strtotime($cs['license_valid_until'])) : '' ?>">
                            </td>
                            <td class="px-3 py-2 border-b border-gray-200 text-center">
                                <?php if (!empty($cs['certificate'])): ?>
                                    <button type="button"
                                        class="view-certificate-btn inline-flex items-center px-2 py-1 text-xs font-medium rounded text-blue-600 hover:bg-blue-50"
                                        data-file="<?= base_url('account/viewCivilCertificate/' . urlencode($cs['certificate'])) ?>">
                                        <i class="fa-regular fa-eye mr-1"></i> View
                                    </button>
                                <?php else: ?>-<?php endif; ?>
                            </td>
                            <td class="px-3 py-2 border-b border-gray-200 text-center">
                                <button type="button" class="inline-flex px-2 py-1 text-xs font-medium rounded text-red-600 hover:bg-red-50 remove-cs-row">
                                    <i class="fa-solid fa-trash mr-1"></i> Delete
                                </button>
                            </td>
                            <!-- Hidden inputs for form submission -->
                            <input type="hidden" name="civil_service_ids[]" value="<?= esc($cs['id'] ?? '') ?>">
                            <input type="hidden" name="eligibility[]" value="<?= esc($cs['eligibility'] ?? '-') ?>">
                            <input type="hidden" name="rating[]" value="<?= esc($cs['rating'] ?? '-') ?>">
                            <input type="hidden" name="place_of_exam[]" value="<?= esc($cs['place_of_exam'] ?? '-') ?>">
                            <input type="hidden" name="license_no[]" value="<?= esc($cs['license_no'] ?? '-') ?>">
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="px-3 py-2 border-b border-gray-200 text-center text-gray-500" colspan="8">
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

    // ---------------- VIEW CERTIFICATE ----------------
    async function openCertificate(fileUrl) {

        // Show loading first
        Swal.fire({
            title: 'Loading...',
            text: 'Please wait while the certificate loads.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            // Short delay to simulate loading
            await new Promise(resolve => setTimeout(resolve, 1000));

            // If no file URL
            if (!fileUrl || fileUrl.trim() === '') {
                Swal.close();
                Swal.fire({
                    icon: 'warning',
                    title: 'No Certificate Available',
                    text: 'No civil service certificate has been uploaded for this record.',
                    showConfirmButton: false,
                    timer: 1000
                });
                return;
            }

            const response = await fetch(fileUrl);
            const contentType = response.headers.get('content-type') || '';

            // If JSON returned → file missing or error
            if (contentType.includes('application/json')) {
                const data = await response.json();
                Swal.close();
                Swal.fire({
                    icon: 'warning',
                    title: 'No Certificate Available',
                    text: data.message || 'No civil service certificate has been uploaded for this record.',
                    showConfirmButton: false,
                    timer: 1000
                });
                return;
            }

            // File exists → open modal
            Swal.close();
            iframe.src = fileUrl;
            certModal.classList.remove('hidden');

        } catch (error) {
            Swal.close();
            Swal.fire({
                icon: 'warning',
                title: 'No Certificate Available',
                text: 'No civil service certificate has been uploaded for this record.',
                showConfirmButton: false,
                timer: 1000
            });
            console.error(error);
        }
    }

    function closeCertificate() {
        iframe.src = '';
        certModal.classList.add('hidden');
    }

    document.querySelectorAll('.view-certificate-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            openCertificate(this.dataset.file);
        });
    });

    certModal.addEventListener('click', function (e) {
        if (e.target === certModal) closeCertificate();
    });

    // ---------------- DELETE RECORD ----------------
    document.querySelectorAll('.remove-cs-row').forEach(button => {

        button.addEventListener('click', function () {

            const row = this.closest('tr');
            const recordName = row.children[1]?.textContent.trim() || 'This record';

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will remove the record from the table!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it.',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280'
            }).then(result => {

                if (result.isConfirmed) {
                    // Add hidden input to track deleted record
                    const civilServiceId = row.querySelector('input[name="civil_service_ids[]"]')?.value;
                    if (civilServiceId) {
                        const deletedInput = document.createElement('input');
                        deletedInput.type = 'hidden';
                        deletedInput.name = 'deleted_civil_service[]';
                        deletedInput.value = civilServiceId;
                        document.querySelector('form').appendChild(deletedInput);
                    }

                    row.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: `This civil service record has been deleted.`,
                        timer: 1200,
                        showConfirmButton: false
                    });

                    if (csTableBody.querySelectorAll('tr').length === 0) {
                        csTableBody.innerHTML = `
                            <tr>
                                <td class="px-2 py-1 border text-center" colspan="8">
                                    No civil service record added.
                                </td>
                            </tr>`;
                    }
                }
            });

        });

    });

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
<?php if(empty($createdBy)): ?>
<!-- Verification & Edit Prompt for Trainings -->
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 rounded">
    <div class="flex items-start">
        <!-- Icon -->
        <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"></path>
        </svg>

        <!-- Content -->
        <div class="flex-1">
            <h4 class="text-sm font-semibold text-yellow-800 mb-1">
                Please verify that all information is correct
            </h4>
            <p class="text-xs text-yellow-700 mb-2">
                Review the details below carefully. Ensure accuracy for further processing. 
                If you need to make changes to your trainings, you can edit them before submission.
            </p>

            <!-- Smaller Edit Button -->
            <button id="editTrainingsBtn" 
                    type="button"
                    class="inline-flex items-center px-2 py-1 bg-clsuGreen text-white text-[10px] font-medium rounded hover:bg-green-800 transition-colors">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Trainings Info
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

    <!-- Section Header with Icon -->
    <div class="px-3 py-2 mb-4 flex items-center">
        <svg class="w-4 h-4 text-clsuGreen mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <h3 class="text-clsuGreen font-semibold text-sm">Trainings</h3>
    </div>


    <div class="overflow-x-auto mb-5 relative">
        <table id="training-table" class="table-auto w-full border-collapse text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Category</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Training Name</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Venue</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Date From</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Date To</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Facilitator</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Hours</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Sponsor</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Remarks</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700">Certificate</th>
                    <th class="px-3 py-2 border-t border-b border-gray-300 font-medium text-gray-700 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white">
            <?php if (!empty($trainings)): ?>
                <?php foreach ($trainings as $tr): ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-3 py-2 border-b border-gray-200 text-gray-800 font-medium"><?= esc($tr['training_category_name']) ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-gray-700"><?= esc($tr['training_name']) ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($tr['training_venue']) ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-gray-600">
                            <?= !empty($tr['date_from']) ? date('F d, Y', strtotime($tr['date_from'])) : '-' ?>
                            <input type="hidden" name="training_date_from[]" value="<?= !empty($tr['date_from']) ? date('Y-m-d', strtotime($tr['date_from'])) : '' ?>">
                        </td>
                        <td class="px-3 py-2 border-b border-gray-200 text-gray-600">
                            <?= !empty($tr['date_to']) ? date('F d, Y', strtotime($tr['date_to'])) : '-' ?>
                            <input type="hidden" name="training_date_to[]" value="<?= !empty($tr['date_to']) ? date('Y-m-d', strtotime($tr['date_to'])) : '' ?>">
                        </td>
                        <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($tr['training_facilitator']) ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($tr['training_hours']) ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($tr['training_sponsor']) ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-gray-600"><?= esc($tr['training_remarks']) ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-center">
                            <?php if (!empty($tr['certificate_file'])): ?>
                                <button type="button"
                                    class="viewCertificateBtn inline-flex items-center px-2 py-1 text-xs font-medium rounded text-blue-600 hover:bg-blue-50"
                                    data-file="<?= base_url('file/view-training/' . $tr['id_applicant_training'] . '/' . $tr['certificate_file']) ?>">
                                    <i class="fa-regular fa-eye mr-1"></i> View
                                </button>
                                <input type="hidden" name="existing_certificate_file[]" value="<?= esc($tr['certificate_file']) ?>">
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">No certificate</span>
                                <input type="hidden" name="existing_certificate_file[]" value="">
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-2 border-b border-gray-200 text-center">
                            <button type="button" class="deleteTrainingBtn inline-flex px-2 py-1 text-xs font-medium rounded text-red-600 hover:bg-red-50">
                                <i class="fa-solid fa-trash mr-1"></i> Delete
                            </button>
                        </td>
                        <!-- Hidden inputs for form submission -->
                        <input type="hidden" name="training_category_id[]" value="<?= esc($tr['training_category_id']) ?>">
                        <input type="hidden" name="training_name[]" value="<?= esc($tr['training_name']) ?>">
                        <input type="hidden" name="training_venue[]" value="<?= esc($tr['training_venue']) ?>">
                        <input type="hidden" name="training_facilitator[]" value="<?= esc($tr['training_facilitator']) ?>">
                        <input type="hidden" name="training_hours[]" value="<?= esc($tr['training_hours']) ?>">
                        <input type="hidden" name="training_sponsor[]" value="<?= esc($tr['training_sponsor']) ?>">
                        <input type="hidden" name="training_remarks[]" value="<?= esc($tr['training_remarks']) ?>">
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="px-3 py-2 border-b border-gray-200 text-center text-gray-500">
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

    async function openTrainingCertificate(fileUrl) {

        // Show loading first
        Swal.fire({
            title: 'Loading...',
            text: 'Please wait while the certificate loads.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            // Short delay to simulate loading
            await new Promise(resolve => setTimeout(resolve, 1000));

            // If no file URL
            if (!fileUrl || fileUrl.trim() === '' || fileUrl === '#') {
                Swal.close();
                return Swal.fire({
                    icon: 'warning',
                    title: 'No Certificate Available',
                    text: 'No training certificate has been uploaded for this record.',
                    showConfirmButton: false,
                    timer: 1000
                });
            }

            const response = await fetch(fileUrl);
            const contentType = response.headers.get('content-type') || '';

            // If JSON returned → file missing or error
            if (contentType.includes('application/json')) {
                const data = await response.json();
                Swal.close();
                return Swal.fire({
                    icon: 'warning',
                    title: 'No Certificate Available',
                    text: data.message || 'No training certificate has been uploaded for this record.',
                    showConfirmButton: false,
                    timer: 1000
                });
            }

            if (!response.ok) {
                throw new Error('No training certificate has been uploaded for this record.');
            }

            // File exists → open modal
            Swal.close();
            frame.src = fileUrl;
            modal.classList.remove('hidden');
            modal.classList.add('flex');

        } catch (error) {
            Swal.close();
            Swal.fire({
                icon: 'warning',
                title: 'No Certificate Available',
                text: error.message || 'No training certificate has been uploaded for this record.',
                showConfirmButton: false,
                timer: 1000
            });
            console.error(error);
        }
    }

    tbody.addEventListener('click', function(e) {

        const viewBtn = e.target.closest('.viewCertificateBtn');
        const deleteBtn = e.target.closest('.deleteTrainingBtn');

        if (viewBtn) {
            e.preventDefault();
            e.stopPropagation();
            openTrainingCertificate(viewBtn.dataset.file);
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
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280'
            }).then(result => {
                if (result.isConfirmed) {
                    row.remove();
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: `This training record has been deleted.`,
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
<!-- Step 7: File Attachments (VIEW / EDIT) -->
<div class="step hidden" id="step-7">
<!-- Verification Message (Yellow) -->
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 rounded">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
        </svg>
        <div>
            <h4 class="text-sm font-semibold text-yellow-800 mb-1">Please verify your file attachments</h4>
            <p class="text-xs text-yellow-700">You can view or update your uploaded files before submitting the application.</p>
        </div>
    </div>
</div>

    <?php
    // Use position-specific requirements if available, otherwise fallback to default
    if (!empty($requirements)) {
        $docLabels = [];
        $displayIndex = 1;
        
        foreach ($requirements as $req) {
            $requirementText = $req['requirement_text'];
            
            // Check if this is a combined requirement that needs to be split
            if (strpos($requirementText, 'Transcript of Records, Diploma, Certificate of Employment and Certificate of Trainings and Seminars') !== false) {
                // Split into individual requirements
                $docLabels['requirement_' . $req['id_requirement'] . '_tor'] = $displayIndex . '. Official Transcript of Records (TOR) Issued by the School';
                $displayIndex++;
                $docLabels['requirement_' . $req['id_requirement'] . '_diploma'] = $displayIndex . '. Copy of Diploma or Proof of Graduation';
                $displayIndex++;
                $docLabels['requirement_' . $req['id_requirement'] . '_employment'] = $displayIndex . '. Certificate of Employment';
                $displayIndex++;
                $docLabels['requirement_' . $req['id_requirement'] . '_trainings'] = $displayIndex . '. Certificate of Trainings and Seminars';
                $displayIndex++;
            } else {
                // Regular requirement
                $docLabels['requirement_' . $req['id_requirement']] = $displayIndex . '. ' . $requirementText;
                $displayIndex++;
            }
        }
    } else {
        // Fallback to default requirements
        $docLabels = [
            'pds'               => '1. Fully accomplished Personal Data Sheet (PDS) with recent passport-sized picture (CS Form No. 212, Revised 2017)',
            'performance_rating' => '2. Latest Performance Rating in the Present Position (Most Recent Rating Period)',
            'resume'            => '3. Updated Resume / Curriculum Vitae',
            'tor'               => '4. Official Transcript of Records (TOR) Issued by the School',
            'diploma'           => '5. Copy of Diploma or Proof of Graduation'
        ];
    }
    ?>

    <div class="overflow-x-auto mb-5">
        <table class="table-auto w-full border-collapse text-xs">
            <tbody class="bg-white">
            <?php foreach ($docLabels as $key => $label): ?>
          <tr class="hover:bg-gray-50 transition-colors duration-150">
    <th class="px-3 py-2 border-b border-gray-200 text-left font-medium text-gray-700 w-1/3">
        <?= esc($label) ?>
    </th>
    <td class="px-3 py-2 border-b border-gray-200 flex items-center gap-2">
        <?php 
        // For dynamic requirements, we don't have existing documents to show by default
        // For default requirements, show existing documents
        // For split requirements, handle special cases
        $hasExistingDoc = false;
        $fileValue = '';
        
        if (strpos($key, 'requirement_') === false && !empty($documents[$key])) {
            // Default requirements with existing documents
            $hasExistingDoc = true;
            $fileValue = $documents[$key];
        } elseif (strpos($key, '_tor') !== false) {
            // Split TOR requirement
            $fileValue = $documents['tor'] ?? '';
            $hasExistingDoc = !empty($fileValue);
        } elseif (strpos($key, '_diploma') !== false) {
            // Split Diploma requirement
            $fileValue = $documents['diploma'] ?? '';
            $hasExistingDoc = !empty($fileValue);
        } elseif (strpos($key, '_employment') !== false) {
            // Employment certificate - would need to check work experience certificates
            $hasExistingDoc = false; // No existing employment certificates in current structure
        } elseif (strpos($key, '_trainings') !== false) {
            // Training certificates - would need to check training certificates
            $hasExistingDoc = false; // No existing training certificates in current structure
        }
        ?>
        <?php if ($hasExistingDoc): ?>
            <button 
                type="button"
                class="viewFileBtn inline-flex items-center px-2 py-1 text-xs font-medium rounded text-blue-600 hover:bg-blue-50"
                data-file="<?= base_url('file/viewFile/' . $fileValue) ?>">
                <i class="fa-regular fa-eye mr-1"></i> View Document
            </button>
        <?php else: ?>
            <span class="text-red-600 text-xs">No file available</span>
        <?php endif; ?>

        <!-- NEW: Edit / Upload -->
        <input type="file" 
               name="<?= $key ?>" 
               accept="application/pdf" 
               class="fileUpload border px-2 py-1 text-xs rounded text-gray-700" />

        <!-- Hidden existing file -->
        <input type="hidden" name="existing_<?= $key ?>" value="<?= esc($fileValue ?? '') ?>">
    </td>
</tr>

            <?php endforeach; ?>
            </tbody>
        </table>
    </div>


    <!-- Data Privacy Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
                <h4 class="text-sm font-semibold text-blue-800 mb-2">Data Privacy Notice</h4>
                <p class="text-xs text-blue-700 mb-3">
                    We collect and process your personal information in accordance with the Data Privacy Act of 2012 (Republic Act No. 10173) and its Implementing Rules and Regulations. Your personal data will be used solely for employment purposes, recruitment processes, and human resource management. We ensure the confidentiality, integrity, and availability of your personal information through appropriate organizational, physical, and technical security measures.
                </p>
                <div class="flex items-start mt-3">
                    <input type="checkbox" 
                           id="privacy_consent" 
                           name="privacy_consent" 
                           required 
                           class="mt-1 mr-2 h-4 w-4 text-clsuGreen border-gray-300 rounded focus:ring-clsuGreen">
                    <label for="privacy_consent" class="text-xs text-gray-700">
                        I have read and understood the Data Privacy Notice. I consent to the collection, use, and processing of my personal information for employment and recruitment purposes in accordance with applicable data privacy laws.
                        <span class="text-red-500">*</span>
                    </label>
                </div>
                <div id="privacy-error" class="hidden mt-2 text-red-600 text-xs flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Please read and accept the Data Privacy Notice by checking the consent checkbox before submitting your application.
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(7)"
                class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">
            Previous
        </button>

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
document.addEventListener('DOMContentLoaded', function(){

    const modal = document.getElementById('fileModal');
    const frame = document.getElementById('fileFrame');
    let isOpening = false;

    // View button click
    document.addEventListener('click', function(e){
        const btn = e.target.closest('.viewFileBtn');
        if(!btn) return;

        e.preventDefault();
        const fileUrl = btn.dataset.file;

        // No file → show SweetAlert2 warning
        if(!fileUrl || fileUrl.trim() === ''){
            Swal.fire({
                icon: 'warning',
                title: 'No File Available',
                text: 'No file has been uploaded for this document.',
                showConfirmButton: false,
                timer: 1000
            });
            return;
        }

        // Check if this is a Google Drive file ID (28-33 characters, no timestamp prefix)
        const fileName = fileUrl.split('/').pop();
        const isGoogleDriveFile = /^[a-zA-Z0-9_-]{28,33}$/.test(fileName) && !/^\d{10}_/.test(fileName);

        if(isGoogleDriveFile) {
            // For Google Drive files, show in modal with iframe
            const googleDriveUrl = `https://drive.google.com/file/d/${fileName}/preview`;
            Swal.close();
            frame.src = googleDriveUrl;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            return;
        }

        if(isOpening) return;
        isOpening = true;

        Swal.fire({
            title: 'Loading...',
            text: 'Please wait while the file loads.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        setTimeout(async () => {
            try{
                const response = await fetch(fileUrl);

                // Check if the response is OK
                if(!response.ok){
                    let msg = 'File not available';
                    const contentType = response.headers.get('content-type') || '';
                    if(contentType.includes('application/json')){
                        const data = await response.json();
                        msg = data.message || msg;
                    }
                    throw new Error(msg);
                }

                // File exists → show modal
                Swal.close();
                frame.src = fileUrl;
                modal.classList.remove('hidden');
                modal.classList.add('flex');

            }catch(err){
                // Show SweetAlert2 warning for errors
                Swal.fire({
                    icon: 'warning',
                    title: 'No File Available',
                    text: err.message || 'No file has been uploaded for this document.',
                    showConfirmButton: false,
                    timer: 1000
                });
            }finally{
                isOpening = false;
            }
        }, 500);
    });

    // Close modal when clicking outside the frame
    modal.addEventListener('click', e => {
        if(e.target === modal){
            frame.src = '';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });

    // Live update View button when a new file is selected
    const fileInputs = document.querySelectorAll('.fileUpload');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(){
            const file = this.files[0];
            if(!file) return;

            if(file.type !== 'application/pdf'){
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File',
                    text: 'Only PDF files are allowed.'
                });
                this.value = '';
                return;
            }

            // Create object URL for preview
            const url = URL.createObjectURL(file);

            // Update the corresponding View button
            const row = this.closest('tr');
            const viewBtn = row.querySelector('.viewFileBtn');

            if(viewBtn){
                viewBtn.dataset.file = url;
                // Blue "View Uploaded Document" with eye icon
                viewBtn.innerHTML = `
                    <span class="text-blue-600 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View Uploaded Document
                    </span>
                `;
            }
        });
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
// Handle CLSU Employee main selection
function handleClsuMainSelection(selectElement) {
    const detailSelect = document.getElementById('clsu_employee_detail');
    const specifyInput = document.getElementById('clsu_specify');
    const hiddenInput = document.getElementById('clsu_employee_hidden');
    
    if (selectElement.value === 'Yes') {
        detailSelect.classList.remove('hidden');
        detailSelect.required = true;
        specifyInput.classList.add('hidden');
        specifyInput.value = '';
        hiddenInput.value = 'Yes';
    } else if (selectElement.value === 'No') {
        detailSelect.classList.add('hidden');
        detailSelect.required = false;
        specifyInput.classList.add('hidden');
        detailSelect.value = '';
        specifyInput.value = '';
        hiddenInput.value = 'No';
    } else {
        detailSelect.classList.add('hidden');
        detailSelect.required = false;
        specifyInput.classList.add('hidden');
        detailSelect.value = '';
        specifyInput.value = '';
        hiddenInput.value = '';
    }
}

// Handle CLSU Employee detail selection
function handleClsuDetailSelection(selectElement) {
    console.log('CLSU detail changed to:', selectElement.value);
    const specifyInput = document.getElementById('clsu_specify');
    const hiddenInput = document.getElementById('clsu_employee_hidden');
    const typeHidden = document.getElementById('clsu_employee_type_hidden');
    
    console.log('Elements found:', {specifyInput, hiddenInput, typeHidden});
    
    if (selectElement.value) {
        console.log('Value exists, showing specify textbox');
        if (typeHidden) typeHidden.value = selectElement.value;
        if (specifyInput) specifyInput.classList.remove('hidden');
        if (hiddenInput) hiddenInput.value = 'Yes';
        console.log('Specify textbox should now be visible');
    } else {
        console.log('No value, hiding specify textbox');
        if (specifyInput) {
            specifyInput.classList.add('hidden');
            specifyInput.value = '';
        }
        if (typeHidden) typeHidden.value = '';
        if (hiddenInput) hiddenInput.value = 'Yes';
    }
}

// Handle PWD main selection
function handlePwdMainSelection(selectElement) {
    const detailSelect = document.getElementById('pwd_detail');
    const specifyInput = document.getElementById('pwd_specify');
    const hiddenInput = document.getElementById('pwd_hidden');
    
    if (selectElement.value === 'Yes') {
        detailSelect.classList.remove('hidden');
        detailSelect.required = true;
        specifyInput.classList.add('hidden');
        specifyInput.value = '';
        hiddenInput.value = 'Yes';
    } else if (selectElement.value === 'No') {
        detailSelect.classList.add('hidden');
        detailSelect.required = false;
        specifyInput.classList.add('hidden');
        detailSelect.value = '';
        specifyInput.value = '';
        hiddenInput.value = 'No';
    } else {
        detailSelect.classList.add('hidden');
        detailSelect.required = false;
        specifyInput.classList.add('hidden');
        detailSelect.value = '';
        specifyInput.value = '';
        hiddenInput.value = '';
    }
}

// Handle PWD detail selection
function handlePwdDetailSelection(selectElement) {
    console.log('PWD detail changed to:', selectElement.value);
    const specifyInput = document.getElementById('pwd_specify');
    const hiddenInput = document.getElementById('pwd_hidden');
    const typeHidden = document.getElementById('pwd_type_hidden');
    
    if (selectElement.value) {
        console.log('Showing PWD specify textbox');
        specifyInput.classList.remove('hidden');
        if (typeHidden) typeHidden.value = selectElement.value;
        if (hiddenInput) hiddenInput.value = 'Yes';
    } else {
        console.log('Hiding PWD specify textbox');
        specifyInput.classList.add('hidden');
        specifyInput.value = '';
        if (typeHidden) typeHidden.value = '';
        if (hiddenInput) hiddenInput.value = 'Yes';
    }
}

// Handle Indigenous main selection
function handleIndigenousMainSelection(selectElement) {
    const specifyInput = document.getElementById('indigenous_specify');
    const hiddenInput = document.getElementById('indigenous_hidden');
    
    if (selectElement.value === 'Yes') {
        specifyInput.classList.remove('hidden');
        hiddenInput.value = 'Yes';
    } else if (selectElement.value === 'No') {
        specifyInput.classList.add('hidden');
        specifyInput.value = '';
        hiddenInput.value = 'No';
    } else {
        specifyInput.classList.add('hidden');
        specifyInput.value = '';
        hiddenInput.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Initialize required states for conditional fields
    const clsuSelect = document.getElementById('clsu_employee_select');
    const pwdSelect = document.getElementById('pwd_select');
    
    if (clsuSelect && clsuSelect.value === 'Yes') {
        const clsuDetail = document.getElementById('clsu_employee_detail');
        if (clsuDetail) {
            clsuDetail.required = true;
        }
    }
    
    if (pwdSelect && pwdSelect.value === 'Yes') {
        const pwdDetail = document.getElementById('pwd_detail');
        if (pwdDetail) {
            pwdDetail.required = true;
        }
    }

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
                    // Skip validation for hidden required fields
                    if (field.offsetParent === null && field.classList.contains('hidden')) {
                        return;
                    }
                    // Also skip if the field is not visible (display: none)
                    if (window.getComputedStyle(field).display === 'none') {
                        return;
                    }
                    if (!field.value.trim()) {
                        allFilled = false;
                        field.classList.add('border-red-500');
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });
                if (!allFilled) {
                    // Show inline error message instead of popup
                    const errorMessage = document.createElement('div');
                    errorMessage.id = 'validation-error-' + item.step;
                    errorMessage.className = 'text-red-600 text-xs mt-2';
                    errorMessage.textContent = 'Please fill out all required fields before proceeding.';
                    
                    // Remove existing error message if present
                    const existingError = document.getElementById('validation-error-' + item.step);
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    // Add error message to the step container
                    const stepContainer = document.getElementById('step-' + item.step);
                    if (stepContainer) {
                        stepContainer.appendChild(errorMessage);
                        // Scroll to error message
                        errorMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                    return;
                } else {
                    // Remove error message if validation passes
                    const existingError = document.getElementById('validation-error-' + item.step);
                    if (existingError) {
                        existingError.remove();
                    }
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

        // First validate privacy consent
        const privacyCheckbox = document.getElementById('privacy_consent');
        const privacyError = document.getElementById('privacy-error');
        
        if (!privacyCheckbox.checked) {
            // Show inline error message
            privacyError.classList.remove('hidden');
            privacyCheckbox.focus();
            privacyCheckbox.scrollIntoView({behavior: 'smooth', block: 'center'});
            return;
        } else {
            // Hide error if previously shown
            privacyError.classList.add('hidden');
        }

        // If privacy consent is given, show confirmation dialog
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
                            title: 'Application Submitted Successfully!',
                            html: '<div class="text-left"><p class="mb-2"><strong></strong></div>',
                            timer: 3000,
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
