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
            <span class="text-sm font-medium opacity-90">Human Resource Management Office</span>
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
            <span class="font-semibold text-clsuGreen w-32">Department:</span>
               <span class="text-gray-700"><?= esc($job['department'] ?? '-') ?></span>
        </div>
        <div class="flex">
            <span class="font-semibold text-clsuGreen w-32">Monthly Salary:</span>
            <span class="text-gray-700"><?= esc($job['monthly_salary'] ?? '-') ?></span>
        </div>
    </div>
</div>

<hr class="my-4">
<form id="applicationForm" method="POST" action="<?= base_url('applications/submit/' . $job['item_no']) ?>" enctype="multipart/form-data">
<input type="hidden" name="job_position_id" value="<?= esc($job['id']) ?>">

<!-- Step 1: Personal Information -->
<div class="step" id="step-1">
    <!-- Section Header -->
    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">
        Personal Information
    </div>

    <!-- Personal Info Table -->
    <div class="overflow-x-auto mb-4">
        <table class="table-auto w-full border-collapse text-xs">
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
                        <input type="text" name="first_name" value="<?= esc($profile['first_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="text" name="middle_name" value="<?= esc($profile['middle_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="text" name="last_name" value="<?= esc($profile['last_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="text" name="name_extension" value="<?= esc($profile['suffix'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Additional Info Table -->
    <div class="overflow-x-auto mb-4">
        <table class="table-auto w-full border-collapse text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 border">Sex</th>
                    <th class="px-2 py-1 border">Date of Birth</th>
                    <th class="px-2 py-1 border">Place of Birth</th>
                    <th class="px-2 py-1 border">Civil Status</th>
                    <th class="px-2 py-1 border">Citizenship</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-2 py-1 border">
                        <select name="sex" class="px-2 py-1 w-full text-xs">
                            <option value="">Select Sex</option>
                            <option value="Male" <?= ($profile['sex'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($profile['sex'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="date" name="birth_date" value="<?= esc($profile['date_of_birth'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="text" name="place_of_birth" value="<?= esc($profile['place_of_birth'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <select name="civil_status" class="px-2 py-1 w-full text-xs">
                            <option value="">Select Civil Status</option>
                            <option value="Single" <?= ($profile['civil_status'] ?? '') === 'Single' ? 'selected' : '' ?>>Single</option>
                            <option value="Married" <?= ($profile['civil_status'] ?? '') === 'Married' ? 'selected' : '' ?>>Married</option>
                            <option value="Widowed" <?= ($profile['civil_status'] ?? '') === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                            <option value="Separated" <?= ($profile['civil_status'] ?? '') === 'Separated' ? 'selected' : '' ?>>Separated</option>
                            <option value="Divorced" <?= ($profile['civil_status'] ?? '') === 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                        </select>
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="text" name="citizenship" value="<?= esc($profile['citizenship'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Height, Weight & Blood Type -->
    <div class="overflow-x-auto mb-4">
        <table class="table-auto w-full border-collapse text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 border">Height (cm)</th>
                    <th class="px-2 py-1 border">Weight (kg)</th>
                    <th class="px-2 py-1 border">Blood Type</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-2 py-1 border">
                        <input type="number" step="0.01" name="height" value="<?= esc($profile['height'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="number" step="0.1" name="weight" value="<?= esc($profile['weight'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <select name="blood_type" class="px-2 py-1 w-full text-xs">
                            <option value="" disabled <?= empty($profile['blood_type']) ? 'selected' : '' ?>>Select Blood Type</option>
                            <option value="A+" <?= ($profile['blood_type'] ?? '') === 'A+' ? 'selected' : '' ?>>A+</option>
                            <option value="A-" <?= ($profile['blood_type'] ?? '') === 'A-' ? 'selected' : '' ?>>A-</option>
                            <option value="B+" <?= ($profile['blood_type'] ?? '') === 'B+' ? 'selected' : '' ?>>B+</option>
                            <option value="B-" <?= ($profile['blood_type'] ?? '') === 'B-' ? 'selected' : '' ?>>B-</option>
                            <option value="AB+" <?= ($profile['blood_type'] ?? '') === 'AB+' ? 'selected' : '' ?>>AB+</option>
                            <option value="AB-" <?= ($profile['blood_type'] ?? '') === 'AB-' ? 'selected' : '' ?>>AB-</option>
                            <option value="O+" <?= ($profile['blood_type'] ?? '') === 'O+' ? 'selected' : '' ?>>O+</option>
                            <option value="O-" <?= ($profile['blood_type'] ?? '') === 'O-' ? 'selected' : '' ?>>O-</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Next Button -->
    <div class="text-right mt-2">
        <button type="button" onclick="nextStep(1)"
                class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">
            Next
        </button>
    </div>
</div>

<!-- Step 2: Family Background -->
<div class="step hidden" id="step-2">
    <div>
        <!-- Section Header -->
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
                                    <input type="text" name="spouse_surname" value="<?= esc($spouse['last_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_first_name" value="<?= esc($spouse['first_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_middle_name" value="<?= esc($spouse['middle_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_ext_name" value="<?= esc($spouse['extension'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
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
                                    <input type="text" name="spouse_occupation" value="<?= esc($spouse['occupation'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="spouse_contact" value="<?= esc($spouse['contact'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
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
                                    <input type="text" name="father_surname" value="<?= esc($father['last_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_first_name" value="<?= esc($father['first_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_middle_name" value="<?= esc($father['middle_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_ext_name" value="<?= esc($father['extension'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
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
                                    <input type="text" name="father_occupation" value="<?= esc($father['occupation'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="father_contact" value="<?= esc($father['contact'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
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
                                    <input type="text" name="mother_maiden_surname" value="<?= esc($mother['last_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="mother_first_name" value="<?= esc($mother['first_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="mother_middle_name" value="<?= esc($mother['middle_name'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
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
                                    <input type="text" name="mother_occupation" value="<?= esc($mother['occupation'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="mother_contact" value="<?= esc($mother['contact'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
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
        <button type="button" onclick="nextStep(2)" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold hover:bg-green-800">
            Next
        </button>
    </div>
</div>
<!-- Step 3: Educational Background -->
<div class="step hidden" id="step-3">
    <div>
        <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">Educational Background</div>
        <div class="space-y-4">

            <!-- Elementary -->
            <div>
                <p class="font-semibold text-xs mb-1">Elementary</p>
                <div class="overflow-x-auto mb-1">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">School Name</th>
                                <th class="px-1 py-1 border">Location</th>
                                <th class="px-1 py-1 border">Year Graduated</th>
                                <th class="px-1 py-1 border">Awards / Honors</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="elementary_school" value="<?= esc($education['elementary_school'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="elementary_location" value="<?= esc($education['elementary_location'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="elementary_year" value="<?= esc($education['elementary_year'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="elementary_awards" value="<?= esc($education['elementary_awards'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- High School -->
            <div>
                <p class="font-semibold text-xs mb-1">High School</p>
                <div class="overflow-x-auto mb-1">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">School Name</th>
                                <th class="px-1 py-1 border">Location</th>
                                <th class="px-1 py-1 border">Year Graduated</th>
                                <th class="px-1 py-1 border">Awards / Honors</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="highschool_school" value="<?= esc($education['highschool_school'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="highschool_location" value="<?= esc($education['highschool_location'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="highschool_year" value="<?= esc($education['highschool_year'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="highschool_awards" value="<?= esc($education['highschool_awards'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- College -->
            <div>
                <p class="font-semibold text-xs mb-1">College</p>
                <div class="overflow-x-auto mb-1">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">School Name</th>
                                <th class="px-1 py-1 border">Location</th>
                                <th class="px-1 py-1 border">Year Graduated</th>
                                <th class="px-1 py-1 border">Awards / Honors</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="college_school" value="<?= esc($education['college_school'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="college_location" value="<?= esc($education['college_location'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="college_year" value="<?= esc($education['college_year'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="college_awards" value="<?= esc($education['college_awards'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Highest Degree -->
            <div>
                <p class="font-semibold text-xs mb-1">Highest Degree</p>
                <div class="overflow-x-auto mb-1">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">Degree / Program</th>
                                <th class="px-1 py-1 border">School Name</th>
                                <th class="px-1 py-1 border">Location</th>
                                <th class="px-1 py-1 border">Year Graduated</th>
                                <th class="px-1 py-1 border">Honors / Distinctions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="highest_degree" value="<?= esc($education['highest_degree'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="highest_school" value="<?= esc($education['highest_school'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="highest_location" value="<?= esc($education['highest_location'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="highest_year" value="<?= esc($education['highest_year'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="highest_awards" value="<?= esc($education['highest_awards'] ?? '') ?>" class="px-1 py-1 w-full text-xs">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(3)" class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">Previous</button>
        <button type="button" onclick="nextStep(3)" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">Next</button>
    </div>
</div>

<!-- Step 4: Work Experience -->
<div class="step hidden" id="step-4">
    <div>
         <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">Work Experience</div>
       <!-- Current Work -->
        <div class="overflow-x-auto mb-2">
            <table class="table-auto w-full border-collapse text-xs">
                <tbody>
                    <tr>
                        <th class="px-2 py-1 border text-left w-1/3">Current Work</th>
                        <td class="px-2 py-1 border">
                            <input type="text" name="current_work" value="<?= esc($work['current'] ?? '') ?>" placeholder="Current Work" class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Previous Work -->
        <div class="overflow-x-auto mb-2">
            <table class="table-auto w-full border-collapse text-xs">
                <tbody>
                    <tr>
                        <th class="px-2 py-1 border text-left w-1/3">Previous Work</th>
                        <td class="px-2 py-1 border">
                            <input type="text" name="previous_work" value="<?= esc($work['previous'] ?? '') ?>" placeholder="Previous Work" class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Duration -->
        <div class="overflow-x-auto mb-2">
            <table class="table-auto w-full border-collapse text-xs">
                <tbody>
                    <tr>
                        <th class="px-2 py-1 border text-left w-1/3">Duration</th>
                        <td class="px-2 py-1 border">
                            <input type="text" name="work_duration" value="<?= esc($work['duration'] ?? '') ?>" placeholder="Duration in Months/Yrs" class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Awards / Achievements -->
        <div class="overflow-x-auto mb-2">
            <table class="table-auto w-full border-collapse text-xs">
                <tbody>
                    <tr>
                        <th class="px-2 py-1 border text-left w-1/3">Award</th>
                        <td class="px-2 py-1 border">
                            <input type="text" name="work_awards" value="<?= esc($work['awards'] ?? '') ?>" placeholder="Awards / Achievements" class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(4)" class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">Previous</button>
        <button type="button" onclick="nextStep(4)" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">Next</button>
    </div>
</div>

<!-- Step 5: File Attachments & Submit -->
<div class="step hidden" id="step-5">
    <div>
        <h2 class="text-sm font-semibold text-clsuGreen mb-2">File Attachments</h2>

        <!-- Resume -->
        <div class="overflow-x-auto mb-2">
            <table class="table-auto w-full border-collapse text-xs">
                <tbody>
                    <tr>
                        <th class="px-2 py-1 border text-left w-1/3">1. Resume (PDF)</th>
                        <td class="px-2 py-1 border">
                            <input type="file" name="resume" accept=".pdf" required class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Transcript of Records -->
        <div class="overflow-x-auto mb-2">
            <table class="table-auto w-full border-collapse text-xs">
                <tbody>
                    <tr>
                        <th class="px-2 py-1 border text-left w-1/3">2. Transcript of Records (TOR) (PDF)</th>
                        <td class="px-2 py-1 border">
                            <input type="file" name="tor" accept=".pdf" required class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Diploma -->
        <div class="overflow-x-auto mb-2">
            <table class="table-auto w-full border-collapse text-xs">
                <tbody>
                    <tr>
                        <th class="px-2 py-1 border text-left w-1/3">3. Diploma (PDF)</th>
                        <td class="px-2 py-1 border">
                            <input type="file" name="diploma" accept=".pdf" required class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Certificate -->
        <div class="overflow-x-auto mb-2">
            <table class="table-auto w-full border-collapse text-xs">
                <tbody>
                    <tr>
                        <th class="px-2 py-1 border text-left w-1/3">4. Certificate (optional, PDF)</th>
                        <td class="px-2 py-1 border">
                            <input type="file" name="certificate" accept=".pdf" class="px-2 py-1 w-full text-xs">
                     </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="text-xs text-gray-500">Accepted format: PDF | Maximum file size: 5MB per file</p>
    </div>

    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(5)" class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">Previous</button>
        <button type="submit" id="submitApplication" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">Submit Application</button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('applicationForm');
    const submitBtn = document.getElementById('submitApplication');

    submitBtn.addEventListener('click', function(e) {
        e.preventDefault(); // prevent default form submission

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

                // Prepare form data including files
                const formData = new FormData(form);

                // Send AJAX request
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text()) // you can return JSON from controller if you prefer
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Application Submitted!',
                        text: 'Your job application has been successfully sent.',
                        timer: 2500,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect after alert closes
                        window.location.href = '<?= base_url("dashboard") ?>';
                    });
                })
                .catch(error => {
                    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                    console.error(error);
                });
            }
        });
    });
});
</script>

</body>
</html>
