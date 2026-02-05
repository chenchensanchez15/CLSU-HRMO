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
           <!-- Instruction Note -->
        <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs mb-4">
            *Do not leave blank entries. Put N/A for not applicable.
        </div>
    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">Personal Information</div>

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
                <th class="px-2 py-1 border">Sex</th>
                <th class="px-2 py-1 border">Date of Birth</th>
                <th class="px-2 py-1 border">Civil Status</th>
                
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
                <td class="px-2 py-1 border">
                    <input type="email" name="email" value="<?= esc($profile['email'] ?? '') ?>" placeholder="Enter Email" required class="px-2 py-1 w-full text-xs">
                </td>
               <td class="px-2 py-1 border">
    <input 
        type="text" 
        name="phone" 
        value="<?= esc($profile['phone'] ?? '') ?>" 
        placeholder="Enter Phone Number" 
        required 
        class="px-2 py-1 w-full text-xs" 
        pattern="\d{11}" 
        title="Phone number must be exactly 11 digits"
        maxlength="11"
        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
    >
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
</div>
<!-- Step 2: Family Background -->
<div class="step hidden" id="step-2">
        <!-- Instruction Note -->
        <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs mb-4">
            *Do not leave blank entries. Put N/A for not applicable.
        </div>

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
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_surname" placeholder="Enter Surname" value="<?= esc($spouse['last_name'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_first_name" placeholder="Enter First Name" value="<?= esc($spouse['first_name'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_middle_name" placeholder="Enter Middle Name" value="<?= esc($spouse['middle_name'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_ext_name" placeholder="Enter Extension" value="<?= esc($spouse['extension'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                                <td class="px-1 py-1 border">
                                    <input 
                                        type="text" 
                                        name="spouse_contact" 
                                        placeholder="Enter Contact Number" 
                                        value="<?= esc($spouse['contact_no'] ?? '') ?>" 
                                        required 
                                        class="px-1 py-1 w-full text-xs" 
                                        maxlength="11"
                                        pattern="\d{11}" 
                                        title="Contact number must be exactly 11 digits"
                                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    >
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_occupation" placeholder="Enter Occupation" value="<?= esc($spouse['occupation'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs" required>
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
                                    <input type="text" name="father_surname" placeholder="Enter Surname" value="<?= esc($father['last_name'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_first_name" placeholder="Enter First Name" value="<?= esc($father['first_name'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_middle_name" placeholder="Enter Middle Name" value="<?= esc($father['middle_name'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_ext_name" placeholder="Enter Extension" value="<?= esc($father['extension'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                                <td class="px-1 py-1 border">
                                    <input 
                                        type="text" 
                                        name="father_contact" 
                                        placeholder="Enter Contact Number" 
                                        value="<?= esc($father['contact_no'] ?? '') ?>" 
                                        required 
                                        class="px-1 py-1 w-full text-xs" 
                                        maxlength="11"
                                        pattern="\d{11}" 
                                        title="Contact number must be exactly 11 digits"
                                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    >
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_occupation" placeholder="Enter Occupation" value="<?= esc($father['occupation'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs" required>
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
                                    <input type="text" name="mother_maiden_surname" placeholder="Enter Surname" value="<?= esc($mother['last_name'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="mother_first_name" placeholder="Enter First Name" value="<?= esc($mother['first_name'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="mother_middle_name" placeholder="Enter Middle Name" value="<?= esc($mother['middle_name'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                                       <td class="px-1 py-1 border">
                                    <input 
                                        type="text" 
                                        name="mother_contact" 
                                        placeholder="Enter Contact Number" 
                                        value="<?= esc($mother['contact_no'] ?? '') ?>" 
                                        required 
                                        class="px-1 py-1 w-full text-xs" 
                                        maxlength="11"
                                        pattern="\d{11}" 
                                        title="Contact number must be exactly 11 digits"
                                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    >
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="mother_occupation" placeholder="Enter Occupation" value="<?= esc($mother['occupation'] ?? 'N/A') ?>" class="px-1 py-1 w-full text-xs" required>
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
        <!-- Instruction Note -->
    <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs mb-4">
        *Do not leave blank entries. Put N/A for not applicable.
    </div>

    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">Educational Background</div>

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

 <!-- First row: School Name, Degree / Course, Highest Level / Units -->
<div class="overflow-x-auto mb-1">
    <table class="table-auto w-full border-collapse text-xs">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-1 py-1 border">School Name</th>
                <th class="px-1 py-1 border">Degree / Course</th>
                <th class="px-1 py-1 border">Highest Level / Units</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-1 py-1 border">
                    <input type="text" name="<?= $key ?>_school" value="<?= esc($education_data[$key]['school_name'] ?? '') ?>" placeholder="Enter School Name" class="px-1 py-1 w-full text-xs" required>
                </td>
                <td class="px-1 py-1 border">
                    <input type="text" name="<?= $key ?>_degree" value="<?= esc($education_data[$key]['degree_course'] ?? '') ?>" placeholder="Enter Degree / Course" class="px-1 py-1 w-full text-xs" required>
                </td>
                <td class="px-1 py-1 border">
                    <input type="text" name="<?= $key ?>_units" value="<?= esc($education_data[$key]['highest_level_units'] ?? '') ?>" placeholder="Enter Highest Level / Units" class="px-1 py-1 w-full text-xs" required>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="overflow-x-auto mb-2">
    <table class="table-auto w-full border-collapse text-xs">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-1 py-1 border">Period From</th>
                <th class="px-1 py-1 border">Period To</th>
                <th class="px-1 py-1 border">Year Graduated</th>
                <th class="px-1 py-1 border">Awards / Honors</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-1 py-1 border">
                    <input 
                        type="text" 
                        name="<?= $key ?>_period_from" 
                        value="<?= esc($education_data[$key]['period_from'] ?? '') ?>" 
                        placeholder="From" 
                        class="px-1 py-1 w-full text-xs" 
                        maxlength="4"
                        oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,4);" 
                        required
                    >
                </td>
                <td class="px-1 py-1 border">
                    <input 
                        type="text" 
                        name="<?= $key ?>_period_to" 
                        value="<?= esc($education_data[$key]['period_to'] ?? '') ?>" 
                        placeholder="To" 
                        class="px-1 py-1 w-full text-xs" 
                        maxlength="4"
                        oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,4);" 
                        required
                    >
                </td>
                <td class="px-1 py-1 border">
                    <input 
                        type="text" 
                        name="<?= $key ?>_year" 
                        value="<?= esc($education_data[$key]['year_graduated'] ?? '') ?>" 
                        placeholder="Year Graduated" 
                        class="px-1 py-1 w-full text-xs" 
                        maxlength="4"
                        oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,4);" 
                        required
                    >
                </td>
                <td class="px-1 py-1 border">
                    <input type="text" name="<?= $key ?>_awards" value="<?= esc($education_data[$key]['awards'] ?? '') ?>" placeholder="Awards / Honors" class="px-1 py-1 w-full text-xs" required>
                </td>
            </tr>
        </tbody>
    </table>
</div>
    <?php endforeach; ?>

    <div class="text-right mt-2">
        <button type="button" onclick="prevStep(3)" class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">Previous</button>
        <button type="button" id="step3Next" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">Next</button>
    </div>
</div>

<!-- Step 4: Work Experience -->
<div class="step hidden" id="step-4">
         <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs mb-4">
            *Do not leave blank entries. Put N/A for not applicable.
        </div>
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
    <select name="govt_service[]" class="px-1 py-1 w-full text-xs">
        <option value="">Select</option>
        <option value="Yes" <?= esc($work['govt_service']) === 'Yes' ? 'selected' : '' ?>>Yes</option>
        <option value="No" <?= esc($work['govt_service']) === 'No' ? 'selected' : '' ?>>No</option>
    </select>
</td>
                            <td class="px-2 py-1 border text-center">
                                <button type="button" class="text-red-500 hover:underline remove-row">
                                 <i class="fa-solid fa-trash"></i>
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

<?php
$user_id = session()->get('user_id');
$app = $db->table('job_applications')
    ->where('user_id', $user_id)
    ->orderBy('applied_at', 'DESC')
    ->get()
    ->getRowArray();
?>

<!-- Step 5: Civil Service -->
<div class="step hidden" id="step-5">
        <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs mb-4">
        *Do not leave blank entries. Put N/A for not applicable.
    </div>
    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">
        Civil Service Eligibility
    </div>

    <?php
    $user_id = session()->get('user_id');
    $civil_services = $db->table('applicant_civil_service')
        ->where('user_id', $user_id)
        ->orderBy('date_of_exam', 'DESC')
        ->get()
        ->getResultArray();
    ?>

    <div class="overflow-x-auto mb-2 relative">
        <!-- Add button -->
        <button
            type="button"
            id="add-cs-btn"
            class="bg-clsuGreen text-white px-4 py-1 rounded text-xs font-semibold hover:bg-green-800 absolute right-0 top-0"
        >
            Add Civil Service
        </button>

        <table id="cs-table" class="table-auto w-full border-collapse text-xs mt-10">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 border">Eligibility</th>
                    <th class="px-2 py-1 border">Rating</th>
                    <th class="px-2 py-1 border">Date of Exam</th>
                    <th class="px-2 py-1 border">Place of Exam</th>
                    <th class="px-2 py-1 border">License No.</th>
                    <th class="px-2 py-1 border">License Valid Until</th>
                    <th class="px-2 py-1 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($civil_services)): ?>
                    <?php foreach($civil_services as $cs): ?>
                        <tr>
                            <td class="px-2 py-1 border">
                                <input type="text" name="eligibility[]" value="<?= esc($cs['eligibility']) ?>" class="px-2 py-1 w-full text-xs" required>
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="text" name="rating[]" value="<?= esc($cs['rating']) ?>" class="px-2 py-1 w-full text-xs" required>
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="date" name="date_of_exam[]" value="<?= esc($cs['date_of_exam']) ?>" class="px-2 py-1 w-full text-xs">
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="text" name="place_of_exam[]" value="<?= esc($cs['place_of_exam']) ?>" class="px-2 py-1 w-full text-xs">
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="text" name="license_no[]" value="<?= esc($cs['license_no']) ?>" class="px-2 py-1 w-full text-xs">
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="date" name="license_valid_until[]" value="<?= esc($cs['license_valid_until']) ?>" class="px-2 py-1 w-full text-xs">
                            </td>
                            <td class="px-2 py-1 border text-center">
                                <button type="button" class="text-red-500 hover:underline remove-cs-row"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="px-2 py-1 border text-center" colspan="7">
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

<?php
$user_id = session()->get('user_id');

/* ===============================
   Prefill applicant trainings
================================ */
$trainings = $db->table('applicant_trainings at')
    ->select('at.*, tc.training_category_name')
    ->join('lib_training_category tc', 'tc.id_training_category = at.training_category_id', 'left')
    ->where('at.user_id', $user_id)
    ->orderBy('at.date_from', 'DESC')
    ->get()
    ->getResultArray();

/* ===============================
   Training categories
================================ */
$categories = $db->table('lib_training_category')->get()->getResultArray();
?>

<!-- ===============================
     STEP 6: TRAININGS
================================ -->
<div class="step hidden" id="step-6">

    <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs mb-4">
        * Do not leave blank entries. Put N/A for not applicable.
    </div>

    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">
        Trainings
    </div>

    <div class="overflow-x-auto mb-2 relative">
        <button type="button"
                id="add-training-btn"
                class="bg-clsuGreen text-white px-4 py-1 rounded text-xs font-semibold hover:bg-green-800 absolute right-0 top-0">
            Add Training
        </button>

        <table id="training-table" class="table-auto w-full border-collapse text-xs mt-6">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 border">Category</th>
                    <th class="px-2 py-1 border">Training Name</th>
                    <th class="px-2 py-1 border">Date From</th>
                    <th class="px-2 py-1 border">Date To</th>
                    <th class="px-2 py-1 border">Facilitator</th>
                    <th class="px-2 py-1 border">Hours</th>
                    <th class="px-2 py-1 border">Sponsor</th>
                    <th class="px-2 py-1 border">Remarks</th>
                    <th class="px-2 py-1 border">Certificate</th>
                    <th class="px-2 py-1 border">Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($trainings)): ?>
                <?php foreach ($trainings as $tr): ?>
                    <tr>
                        <td class="px-2 py-1 border">
                            <select name="training_category_id[]" class="px-2 py-1 w-full text-xs">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= esc($cat['id_training_category']) ?>"
                                        <?= $cat['id_training_category'] == $tr['training_category_id'] ? 'selected' : '' ?>>
                                        <?= esc($cat['training_category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>

                        <td class="px-2 py-1 border">
                            <input type="text" name="training_name[]" value="<?= esc($tr['training_name']) ?>" class="px-2 py-1 w-full text-xs">
                        </td>

                        <td class="px-2 py-1 border">
                            <input type="date" name="training_date_from[]" value="<?= esc($tr['date_from']) ?>" class="px-2 py-1 w-full text-xs">
                        </td>

                        <td class="px-2 py-1 border">
                            <input type="date" name="training_date_to[]" value="<?= esc($tr['date_to']) ?>" class="px-2 py-1 w-full text-xs">
                        </td>

                        <td class="px-2 py-1 border">
                            <input type="text" name="training_facilitator[]" value="<?= esc($tr['training_facilitator']) ?>" class="px-2 py-1 w-full text-xs">
                        </td>

                        <td class="px-2 py-1 border">
                            <input type="number" name="training_hours[]" value="<?= esc($tr['training_hours']) ?>" class="px-2 py-1 w-full text-xs">
                        </td>

                        <td class="px-2 py-1 border">
                            <input type="text" name="training_sponsor[]" value="<?= esc($tr['training_sponsor']) ?>" class="px-2 py-1 w-full text-xs">
                        </td>

                        <td class="px-2 py-1 border">
                            <input type="text" name="training_remarks[]" value="<?= esc($tr['training_remarks']) ?>" class="px-2 py-1 w-full text-xs">
                        </td>

 <!-- ===============================
     CERTIFICATE (FIXED)
=============================== -->
<td class="px-2 py-1 border">
    <?php if (!empty($tr['certificate_file'])): ?>
 <p class="text-xs text-green-700 mb-1">
            Uploaded: 
            <a href="<?= base_url('uploads/' . $tr['certificate_file']) ?>" 
               target="_blank" 
               class="underline text-blue-600 text-xs">
                View
            </a>
        </p>

        <!-- Keep old file in hidden input for update -->
        <input type="hidden"
               name="existing_certificate_file[]"
               value="<?= esc($tr['certificate_file']) ?>">

        <!-- Option to upload new certificate -->
        <input type="file"
               name="training_certificate[]"
               class="text-xs mt-1">
    <?php else: ?>
        <!-- No existing file, just upload -->
        <input type="file"
               name="training_certificate[]"
               class="text-xs">

        <input type="hidden"
               name="existing_certificate_file[]"
               value="">
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
                    <td colspan="10" class="px-2 py-1 border text-center">
                        No training record added.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ===============================
         NAV BUTTONS
    ================================ -->
    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(6)"
                class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">
            Previous
        </button>

        <button type="button" onclick="nextStep(6)"
                class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">
            Next
        </button>
    </div>
</div>

<!-- ===============================
     JS: ADD TRAINING ROW
================================ -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const categoryOptions = `<?php foreach ($categories as $cat): ?>
        <option value="<?= esc($cat['id_training_category']) ?>">
            <?= esc($cat['training_category_name']) ?>
        </option>
    <?php endforeach; ?>`;

    document.getElementById('add-training-btn').addEventListener('click', function () {

        const tbody = document.querySelector('#training-table tbody');

        const emptyRow = tbody.querySelector('td[colspan="10"]');
        if (emptyRow) emptyRow.closest('tr').remove();

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-2 py-1 border">
                <select name="training_category_id[]" class="px-2 py-1 w-full text-xs">
                    <option value="">Select Category</option>
                    ${categoryOptions}
                </select>
            </td>
            <td class="px-2 py-1 border"><input type="text" name="training_name[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border"><input type="date" name="training_date_from[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border"><input type="date" name="training_date_to[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border"><input type="text" name="training_facilitator[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border"><input type="number" name="training_hours[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border"><input type="text" name="training_sponsor[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border"><input type="text" name="training_remarks[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border">
                <input type="file" name="training_certificate[]" class="text-xs">
                <input type="hidden" name="existing_certificate_file[]" value="">
            </td>
            <td class="px-2 py-1 border text-center">
                <button type="button" class="deleteTrainingBtn text-red-600 px-1">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.querySelector('#training-table tbody');

    // Delete any training row
    tbody.addEventListener('click', function(e) {
        if (e.target.closest('.deleteTrainingBtn')) {
            e.target.closest('tr').remove();

            // Show "No training record added" if table is empty
            if (tbody.querySelectorAll('tr').length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = `
                    <td colspan="10" class="px-2 py-1 border text-center">
                        No training record added.
                    </td>
                `;
                tbody.appendChild(emptyRow);
            }
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
    'resume'      => '',
    'tor'         => '',
    'diploma'     => '',
    'certificate' => ''
];
?>

<!-- Step 7: File Attachments -->
<div class="step hidden" id="step-7">

    <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs mb-4">
        * Do not leave blank entries. Put N/A for not applicable.
    </div>

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
                        <p class="text-xs text-green-700 mb-1">
        Uploaded: 
        <a href="<?= base_url('uploads/' . $documents[$key]) ?>" 
           target="_blank" 
           class="underline text-blue-600 text-xs">
            View
        </a>
    </p>

                                <!-- 🔑 KEEP OLD FILE -->
                                <input type="hidden"
                                       name="existing_<?= $key ?>"
                                       value="<?= esc($documents[$key]) ?>">

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

    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(7)"
                class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold">
            Previous
        </button>

       <button type="submit"
        class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold" id="submitApplication">
    Submit Application
</button>

    </div>
</div>


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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    // ---------- Add Rows Helper ----------
    function addRow(buttonId, tableSelector, rowHTML, emptyColspan){
        document.getElementById(buttonId).addEventListener('click', function () {
            const tableBody = document.querySelector(tableSelector + ' tbody');
            const emptyRow = tableBody.querySelector('td[colspan="' + emptyColspan + '"]');
            if (emptyRow) emptyRow.closest('tr').remove();
            const newRow = document.createElement('tr');
            newRow.innerHTML = rowHTML;
            tableBody.appendChild(newRow);
        });
    }

    // ---------- Civil Service Row ----------
    addRow('add-cs-btn', '#cs-table', `
        <td class="px-2 py-1 border"><input type="text" name="eligibility[]" placeholder="Eligibility" class="px-2 py-1 w-full text-xs" required></td>
        <td class="px-2 py-1 border"><input type="text" name="rating[]" placeholder="Rating" class="px-2 py-1 w-full text-xs" required></td>
        <td class="px-2 py-1 border"><input type="date" name="date_of_exam[]" class="px-2 py-1 w-full text-xs"></td>
        <td class="px-2 py-1 border"><input type="text" name="place_of_exam[]" placeholder="Place of Exam" class="px-2 py-1 w-full text-xs"></td>
        <td class="px-2 py-1 border"><input type="text" name="license_no[]" placeholder="License No." class="px-2 py-1 w-full text-xs"></td>
        <td class="px-2 py-1 border"><input type="date" name="license_valid_until[]" class="px-2 py-1 w-full text-xs"></td>
        <td class="px-1 py-1 border text-center"><button type="button" class="deleteCSBtn text-red-600 px-1"><i class="fa-solid fa-trash"></i></button></td>
    `, 7);

    // ---------- Work Experience Row ----------
    addRow('add-work-btn', '#work-table', `
        <td class="px-2 py-1 border"><input type="text" name="position_title[]" placeholder="Position Title" class="px-2 py-1 w-full text-xs"></td>
        <td class="px-2 py-1 border"><input type="text" name="office[]" placeholder="Office / Company" class="px-2 py-1 w-full text-xs"></td>
        <td class="px-2 py-1 border flex space-x-1"><input type="date" name="date_from[]" class="px-2 py-1 w-1/2 text-xs"><input type="date" name="date_to[]" class="px-2 py-1 w-1/2 text-xs"></td>
        <td class="px-2 py-1 border"><input type="text" name="status_of_appointment[]" placeholder="Status of Appointment" class="px-2 py-1 w-full text-xs"></td>
        <td class="px-2 py-1 border"><select name="govt_service[]" class="px-2 py-1 w-full text-xs"><option value="">Select</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
        <td class="px-2 py-1 border text-center"><button type="button" class="remove-row text-red-500"><i class="fa-solid fa-trash"></i></button></td>
    `, 6);

    // ---------- Delete Rows ----------
    document.addEventListener('click', function(e){
        // Civil Service
        if (e.target.closest('.deleteCSBtn') || e.target.closest('.remove-cs-row')) {
            const tableBody = document.querySelector('#cs-table tbody');
            e.target.closest('tr').remove();
            if (tableBody.querySelectorAll('tr').length === 0) {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center border px-2 py-1">No civil service record added.</td></tr>';
            }
        }

        // Work
        if (e.target.closest('.remove-row')) {
            const tableBody = document.querySelector('#work-table tbody');
            e.target.closest('tr').remove();
            if (tableBody.querySelectorAll('tr').length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center border px-2 py-1">No work experience added.</td></tr>';
            }
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
                        // ✅ SweetAlert2 success after saving
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
