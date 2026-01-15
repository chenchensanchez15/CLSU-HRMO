<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Applicant Dashboard | CLSU HRMO</title>
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
<style>
body { margin:0; font-family: Arial, sans-serif; background:#f4f6f9; }

/* NAVBAR */
.navbar { background: #0B6B3A; padding: 10px 30px; color: #fff; display:flex; justify-content:space-between; align-items:center; }
.nav-links a { color:#fff; margin-right:20px; font-weight:bold; text-decoration:none; }
.account-menu { position: relative; display:inline-block; }
.account-dropdown { display:none; position:absolute; right:0; background:#fff; color:#000; min-width:160px; border-radius:5px; box-shadow:0 2px 5px rgba(0,0,0,0.2); z-index:100; }
.account-dropdown a { display:block; padding:10px; text-decoration:none; color:#0B6B3A; }
.account-dropdown a:hover { background:#f2f2f2; }

/* LAYOUT */
.container { display:flex; padding:30px; gap:30px; flex-wrap:wrap; }
.left { flex:0 0 30%; background:#fff; padding:20px; border-radius:10px; text-align:center; position:sticky; top:20px; align-self:flex-start; }
.right { flex:1; background:transparent; }
.card { background:#fff; padding:20px; border-radius:10px; margin-bottom:20px; }
.card h3 { color:#0B6B3A; margin-bottom:15px; }
table { width:100%; border-collapse:collapse; }
th, td { padding:10px; border-bottom:1px solid #ddd; text-align:left; }
.status { padding:5px 10px; border-radius:5px; color:#fff; font-size:13px; }
.Pending { background:orange; }
.Approved { background:green; }
.Rejected { background:red; }

@media (max-width:1024px) {
    .container { flex-direction:column; }
    .left, .right { width:100%; flex:none; }
}
</style>
<script>
function toggleDropdown() {
    const dropdown = document.getElementById('accountDropdown');
    dropdown.style.display = dropdown.style.display==='block'?'none':'block';
}
window.onclick = function(event) {
    if(!event.target.closest('.account-menu')) {
        const dropdown = document.getElementById('accountDropdown');
        if(dropdown) dropdown.style.display='none';
    }
}
</script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<header class="bg-clsuGreen text-white py-3 px-6 shadow">
    <div class="flex items-center justify-between max-w-7xl mx-auto">
        <div class="flex items-center gap-4">
            <img src="/HRMO/public/assets/images/clsu-logo2.png" alt="CLSU Logo" class="w-12 h-auto">
            <div class="flex flex-col leading-tight">
                <span class="text-xl font-bold">CLSU Online Job Application</span>
                <span class="text-sm font-medium opacity-90">Human Resource Management Office</span>
            </div>
        </div>
        <div class="flex items-center gap-12">
            <nav class="hidden md:flex gap-6 font-semibold mt-7">
                <a href="<?= site_url('dashboard') ?>" class="hover:underline">Home</a>
                <a href="<?= site_url('account/personal') ?>" class="hover:underline">Personal</a>
                <a href="#" class="hover:underline">Trainings</a>
            </nav>
            <div class="account-menu relative mt-1">
                <button onclick="toggleDropdown()" class="flex items-center gap-1 leading-none focus:outline-none">
                    <?php 
                    $photoPath = FCPATH . 'uploads/' . ($profile['photo'] ?? '');
                    if(!empty($profile['photo']) && file_exists($photoPath)): ?>
                        <img src="<?= base_url('uploads/' . $profile['photo']) ?>" class="w-8 h-8 rounded-full border-2 border-white object-cover">
                    <?php else: ?>
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center border-2 border-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12 2a5 5 0 100 10 5 5 0 000-10zm-7 18a7 7 0 0114 0H5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="accountDropdown" class="account-dropdown absolute right-0 mt-2 hidden bg-white text-black min-w-[200px] rounded shadow-lg z-50">
                    <a href="<?= site_url('account/changePassword') ?>" class="block px-4 py-2 hover:bg-gray-100">Account</a>
                    <a href="<?= site_url('logout') ?>" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="min-h-screen bg-gray-100 px-4 py-6 flex justify-center">
    <!-- Main Form Container -->
    <div class="w-full max-w-7xl bg-white shadow rounded-lg p-6 text-sm">

        <!-- Application Details Header -->
        <div class="flex justify-between items-center mb-4">
            <span class="font-semibold text-gray-700 text-base">Application Details</span>
            <a href="<?= site_url('dashboard') ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm font-medium">
                ✕
            </a>
        </div>

        <!-- Application Details Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="space-y-1">
                <p><span class="font-semibold">Position:</span> <?= esc($job['position_title'] ?? '-') ?></p>
                <p><span class="font-semibold">Office:</span> <?= esc($job['office'] ?? '-') ?></p>
                <p><span class="font-semibold">Department:</span> <?= esc($job['department'] ?? '-') ?></p>
                <p><span class="font-semibold">Monthly Salary:</span> <span class="text-clsuGreen font-semibold"><?= esc($job['monthly_salary'] ?? '-') ?></span></p>
            </div>
            <div class="space-y-1">
                <p><span class="font-semibold">Applied At:</span> <?= date('F d, Y') ?></p>
                <p><span class="font-semibold">Application Deadline:</span> <?= date('F d, Y', strtotime($job['deadline'] ?? '')) ?></p>
                <p><span class="font-semibold">Status:</span> <?= esc($application_status ?? 'Submitted. For Evaluation') ?></p>
            </div>
        </div>

        <hr class="my-4">

        <!-- Application Form -->
        <form id="applicationForm" method="POST" action="<?= base_url('applications/update/' . ($app['id'] ?? '')) ?>" enctype="multipart/form-data">
            <input type="hidden" name="job_position_id" value="<?= esc($job['id'] ?? '') ?>">

            <!-- Steps Container -->
            <div class="space-y-6">

                <!-- Step 1: Personal Information -->
                <div class="step" id="step-1">
                    <div class="bg-gray-100 px-3 py-2 rounded font-semibold text-sm mb-4">Personal Information</div>

                    <!-- Name Table -->
                    <div class="overflow-x-auto mb-4">
                        <table class="table-auto w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-2 py-1 border">First Name *</th>
                                    <th class="px-2 py-1 border">Middle Name</th>
                                    <th class="px-2 py-1 border">Last Name *</th>
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
                                        <input type="text" name="last_name" value="<?= esc($profile['last_name'] ?? '') ?>" class=" px-2 py-1 w-full text-xs">
                                    </td>
                                    <td class="px-2 py-1 border">
                                        <input type="text" name="name_extension" value="<?= esc($profile['suffix'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Birth & Sex Table -->
                    <div class="overflow-x-auto mb-4">
                        <table class="table-auto w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-2 py-1 border">Sex *</th>
                                    <th class="px-2 py-1 border">Date of Birth *</th>
                                    <th class="px-2 py-1 border">Place of Birth</th>
                                    <th class="px-2 py-1 border">Civil Status</th>
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
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Contact & Citizenship Table -->
                    <div class="overflow-x-auto mb-4">
                        <table class="table-auto w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-2 py-1 border">Email</th>
                                    <th class="px-2 py-1 border">Phone Number</th>
                                    <th class="px-2 py-1 border">Citizenship</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-2 py-1 border">
                                        <input type="email" name="email" value="<?= esc($app['email'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                                    </td>
                                    <td class="px-2 py-1 border">
                                        <input type="text" name="phone" value="<?= esc($app['phone'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                                    </td>
                                    <td class="px-2 py-1 border">
                              <input type="text" name="citizenship" value="<?= esc($profile['citizenship'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Physical Info Table -->
                    <div class="overflow-x-auto mb-4">
                        <table class="table-auto w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-2 py-1 border">Height (m)</th>
                                    <th class="px-2 py-1 border">Weight (kg)</th>
                                    <th class="px-2 py-1 border">Blood Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-2 py-1 border">
                                        <input type="number" step="0.01" name="height" value="<?= esc($profile['height'] ?? '') ?>" class=" px-2 py-1 w-full text-xs">
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
                    <div class="text-right mt-3">
                        <button type="button" onclick="nextStep(1)" class="bg-clsuGreen text-white px-5 py-2 rounded text-sm font-semibold hover:bg-green-800">
                            Next
                        </button>
                    </div>
                </div>

<!-- Step 2: Family Background -->
<div class="step hidden" id="step-2">
    <div class="bg-gray-100 px-3 py-2 rounded font-semibold text-sm mb-4">Family Background</div>

    <!-- Family Tables -->
    <div class="space-y-4">

        <!-- Spouse Table -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-2 py-1 border">Surname</th>
                        <th class="px-2 py-1 border">First Name</th>
                        <th class="px-2 py-1 border">Middle Name</th>
                        <th class="px-2 py-1 border">Extension</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-2 py-1 border">
                            <input type="text" name="spouse_surname" value="<?= esc($spouse['last_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="spouse_first_name" value="<?= esc($spouse['first_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="spouse_middle_name" value="<?= esc($spouse['middle_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="spouse_ext_name" value="<?= esc($spouse['extension'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Father Table -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-2 py-1 border">Surname</th>
                        <th class="px-2 py-1 border">First Name</th>
                        <th class="px-2 py-1 border">Middle Name</th>
                        <th class="px-2 py-1 border">Extension</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-2 py-1 border">
                            <input type="text" name="father_surname" value="<?= esc($father['last_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="father_first_name" value="<?= esc($father['first_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="father_middle_name" value="<?= esc($father['middle_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="father_ext_name" value="<?= esc($father['extension'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mother Table -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-2 py-1 border">Surname (Maiden)</th>
                        <th class="px-2 py-1 border">First Name</th>
                        <th class="px-2 py-1 border">Middle Name</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-2 py-1 border">
                            <input type="text" name="mother_maiden_surname" value="<?= esc($mother['last_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="mother_first_name" value="<?= esc($mother['first_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="mother_middle_name" value="<?= esc($mother['middle_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Previous / Next Buttons -->
    <div class="text-right mt-4">
        <button type="button" onclick="prevStep(2)" class="bg-gray-400 text-white px-4 py-2 rounded text-sm font-semibold">Previous</button>
        <button type="button" onclick="nextStep(2)" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold">Next</button>
    </div>
</div>
<!-- Step 3: Educational Background -->
<div class="step hidden" id="step-3">
    <div class="bg-gray-100 px-3 py-2 rounded font-semibold text-sm mb-4">Educational Background</div>

    <div class="space-y-4">
        <!-- Elementary Table -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-left border-collapse text-xs mb-3">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-2 py-1 border">School</th>
                        <th class="px-2 py-1 border">Location</th>
                        <th class="px-2 py-1 border">Year Graduated</th>
                        <th class="px-2 py-1 border">Awards / Honors</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-2 py-1 border">
                            <input type="text" name="elementary_school" value="<?= esc($elementary['school_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="elementary_location" value="<?= esc($elementary['location'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="elementary_year" value="<?= esc($elementary['year_graduated'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="elementary_awards" value="<?= esc($elementary['awards'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- High School Table -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-left border-collapse text-xs mb-3">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-2 py-1 border">School</th>
                        <th class="px-2 py-1 border">Location</th>
                        <th class="px-2 py-1 border">Year Graduated</th>
                        <th class="px-2 py-1 border">Awards / Honors</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-2 py-1 border">
                            <input type="text" name="highschool_school" value="<?= esc($highschool['school_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="highschool_location" value="<?= esc($highschool['location'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="highschool_year" value="<?= esc($highschool['year_graduated'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="highschool_awards" value="<?= esc($highschool['awards'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- College Table -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-left border-collapse text-xs mb-3">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-2 py-1 border">School</th>
                        <th class="px-2 py-1 border">Location</th>
                        <th class="px-2 py-1 border">Year Graduated</th>
                        <th class="px-2 py-1 border">Awards / Honors</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-2 py-1 border">
                            <input type="text" name="college_school" value="<?= esc($college['school_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="college_location" value="<?= esc($college['location'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="college_year" value="<?= esc($college['year_graduated'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="college_awards" value="<?= esc($college['awards'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-right mt-4">
        <button type="button" onclick="prevStep(3)" class="bg-gray-400 text-white px-4 py-2 rounded text-sm font-semibold">Previous</button>
        <button type="button" onclick="nextStep(3)" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold">Next</button>
    </div>
</div>

<!-- Step 4: Work Experience -->
<div class="step hidden" id="step-4">
    <div class="bg-gray-100 px-3 py-2 rounded font-semibold text-sm mb-4">Work Experience</div>

    <div class="overflow-x-auto">
        <table class="table-auto w-full text-left border-collapse text-xs">
            <tbody>
                <!-- Row 1: Current Work -->
                <tr>
                    <td class="px-2 py-1 border font-semibold w-1/4">Current Work / Position / Agency</td>
                    <td class="px-2 py-1 border w-3/4">
                        <input type="text" name="current_work" value="<?= esc($applicant_work['current_work'] ?? '') ?>" class="px-2 py-1 w-full text-xs ">
                    </td>
                </tr>

                <!-- Row 2: Previous Work -->
                <tr>
                    <td class="px-2 py-1 border font-semibold w-1/4">Previous Work / Position / Agency</td>
                    <td class="px-2 py-1 border w-3/4">
                        <input type="text" name="previous_work" value="<?= esc($applicant_work['previous_work'] ?? '') ?>" class="px-2 py-1 w-full text-xs ">
                    </td>
                </tr>

                <!-- Row 3: Duration -->
                <tr>
                    <td class="px-2 py-1 border font-semibold w-1/4">Duration (From – To)</td>
                    <td class="px-2 py-1 border w-3/4">
                        <input type="text" name="work_duration" value="<?= esc($applicant_work['duration'] ?? '') ?>" class="px-2 py-1 w-full text-xs ">
                    </td>
                </tr>

                <!-- Row 4: Awards / Achievements -->
                <tr>
                    <td class="px-2 py-1 border font-semibold w-1/4">Awards / Achievements</td>
                    <td class="px-2 py-1 border w-3/4">
                        <input type="text" name="work_awards" value="<?= esc($applicant_work['awards'] ?? '') ?>" class="px-2 py-1 w-full text-xs ">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="text-right mt-4">
        <button type="button" onclick="prevStep(4)" class="bg-gray-400 text-white px-4 py-2 rounded text-sm font-semibold">Previous</button>
        <button type="button" onclick="nextStep(4)" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold">Next</button>
    </div>
</div>


<!-- Step 5: File Attachments & Submit -->
<div class="step hidden" id="step-5">
    <div class="bg-gray-100 px-3 py-2 rounded font-semibold text-sm mb-4">File Attachments</div>

    <div class="overflow-x-auto">
        <table class="table-auto w-full text-left border-collapse text-xs">
            <tbody>
                <!-- Row 1: Resume -->
                <tr>
                    <td class="px-2 py-1 border font-semibold w-1/4">Resume (PDF)</td>
                    <td class="px-2 py-1 border w-3/4">
                        <?php if (!empty($documents['resume'])): ?>
                            <p class="text-xs text-green-700 mb-1">Uploaded: <?= esc($documents['resume']) ?></p>
                        <?php endif; ?>
                        <input type="file" name="resume" accept=".pdf" class="px-2 py-1 w-full text-xs ">
                    </td>
                </tr>

                <!-- Row 2: Transcript of Records -->
                <tr>
                    <td class="px-2 py-1 border font-semibold w-1/4">Transcript of Records (TOR)</td>
                    <td class="px-2 py-1 border w-3/4">
                        <?php if (!empty($documents['tor'])): ?>
                            <p class="text-xs text-green-700 mb-1">Uploaded: <?= esc($documents['tor']) ?></p>
                        <?php endif; ?>
                        <input type="file" name="tor" accept=".pdf" class="px-2 py-1 w-full text-xs ">
                    </td>
                </tr>

                <!-- Row 3: Diploma -->
                <tr>
                    <td class="px-2 py-1 border font-semibold w-1/4">Diploma</td>
                    <td class="px-2 py-1 border w-3/4">
                        <?php if (!empty($documents['diploma'])): ?>
                            <p class="text-xs text-green-700 mb-1">Uploaded: <?= esc($documents['diploma']) ?></p>
                        <?php endif; ?>
                        <input type="file" name="diploma" accept=".pdf" class="px-2 py-1 w-full text-xs ">
                    </td>
                </tr>

                <!-- Row 4: Other Certificates -->
                <tr>
                    <td class="px-2 py-1 border font-semibold w-1/4">Other Certificates (Optional)</td>
                    <td class="px-2 py-1 border w-3/4">
                        <?php if (!empty($documents['certificates'])): ?>
                            <?php foreach (json_decode($documents['certificates'], true) as $cert): ?>
                                <p class="text-xs text-green-700">Uploaded: <?= esc($cert) ?></p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <input type="file" name="certificates[]" multiple accept=".pdf,.jpg,.png" class="px-2 py-1 w-full text-xs ">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="text-right mt-4">
        <button type="button" onclick="prevStep(5)" class="bg-gray-400 text-white px-4 py-2 rounded text-sm font-semibold">Previous</button>
        <button type="submit" id="submitBtn" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold">Submit Application</button>
    </div>
</div>

   </div> <!-- End Steps Container -->

            </div> <!-- End Steps Container -->
        </form>
    </div> <!-- End Main Form Container -->
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('applicationForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent normal submission

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to update this application?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0B6B3A',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form via JS
            this.submit();
        }
    });
});
</script>


<script>
let currentStep = 1;
function showStep(step){
    document.querySelectorAll('.step').forEach(s => s.classList.add('hidden'));
    document.getElementById('step-' + step).classList.remove('hidden');
}
function nextStep(step){
    currentStep = step + 1;
    showStep(currentStep);
}
function prevStep(step){
    currentStep = step - 1;
    showStep(currentStep);
}
showStep(currentStep);
</script>
        </div>
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

<div id="jobModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white max-w-3xl w-full rounded-lg p-6 max-h-[90vh] overflow-y-auto">
        <button onclick="closeModal()" class="float-right text-gray-500 text-xl">✕</button>
        <div id="modalContent"></div>
    </div>
</div>

</body>
</html>
