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
            </div>
        </div>
        <div class="flex items-center gap-12">
            <nav class="hidden md:flex gap-6 font-semibold mt-7">
                      <a href="<?= site_url('dashboard') ?>" 
   class="text-clsuGold font-semibold border-b-2 border-clsuGold pb-0.5">
   Home
</a>
            <a href="<?= site_url('account/personal') ?>" class="hover:underline">Profile</a>
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

        <!-- Application Form -->
<form action="<?= site_url('applications/update/' . $app['id_job_application']) ?>" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="job_position_id" value="<?= esc($job['id'] ?? '') ?>">

            <!-- Steps Container -->
            <div class="space-y-6">
        <!-- Instruction Note -->
        <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs mb-4">
            *Do not leave blank entries. Put N/A for not applicable.
        </div>
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
                        <input type="text" name="first_name" value="<?= esc($personal['first_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="text" name="middle_name" value="<?= esc($personal['middle_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="text" name="last_name" value="<?= esc($personal['last_name'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="text" name="name_extension" value="<?= esc($personal['extension'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
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
                    <th class="px-2 py-1 border">Civil Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-2 py-1 border">
                        <select name="sex" class="px-2 py-1 w-full text-xs">
                            <option value="">Select Sex</option>
                            <option value="Male" <?= ($personal['sex'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($personal['sex'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="date" name="birth_date" value="<?= esc($personal['date_of_birth'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <select name="civil_status" class="px-2 py-1 w-full text-xs">
                            <option value="">Select Civil Status</option>
                            <option value="Single" <?= ($personal['civil_status'] ?? '') === 'Single' ? 'selected' : '' ?>>Single</option>
                            <option value="Married" <?= ($personal['civil_status'] ?? '') === 'Married' ? 'selected' : '' ?>>Married</option>
                            <option value="Widowed" <?= ($personal['civil_status'] ?? '') === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                            <option value="Separated" <?= ($personal['civil_status'] ?? '') === 'Separated' ? 'selected' : '' ?>>Separated</option>
                            <option value="Divorced" <?= ($personal['civil_status'] ?? '') === 'Divorced' ? 'selected' : '' ?>>Divorced</option>
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
                        <input type="email" name="email" value="<?= esc($personal['email'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="text" name="phone" value="<?= esc($personal['phone'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="text" name="citizenship" value="<?= esc($personal['citizenship'] ?? '') ?>" class="px-2 py-1 w-full text-xs">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Address Table -->
    <div class="overflow-x-auto mb-4">
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
                        <input type="text" name="residential_address" value="<?= esc($personal['residential_address'] ?? '') ?>" placeholder="Enter Residential Address" required class="px-2 py-1 w-full text-xs">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="text" name="permanent_address" value="<?= esc($personal['permanent_address'] ?? '') ?>" placeholder="Enter Permanent Address" required class="px-2 py-1 w-full text-xs">
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

    <div class="space-y-4">
        <?php foreach ($family as $fam): ?>
            <div>
                <p class="font-semibold text-xs mb-1 text-text-black">
                    <?= esc($fam['relationship']) ?><?= $fam['relationship'] === 'Mother' ? ' (Maiden Name)' : '' ?>
                </p>

                <div class="overflow-x-auto mb-2">
                    <table class="table-auto w-full border-collapse text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-1 py-1 border">Surname</th>
                                <th class="px-1 py-1 border">First Name</th>
                                <th class="px-1 py-1 border">Middle Name</th>
                                <?php if ($fam['relationship'] !== 'Mother'): ?>
                                <th class="px-1 py-1 border">Extension</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="fam_last_name[<?= $fam['id_application_fam'] ?>]" value="<?= esc($fam['last_name']) ?>" placeholder="Enter Surname" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="fam_first_name[<?= $fam['id_application_fam'] ?>]" value="<?= esc($fam['first_name']) ?>" placeholder="Enter First Name" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="fam_middle_name[<?= $fam['id_application_fam'] ?>]" value="<?= esc($fam['middle_name']) ?>" placeholder="Enter Middle Name" class="px-1 py-1 w-full text-xs">
                                </td>
                                <?php if ($fam['relationship'] !== 'Mother'): ?>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="fam_extension[<?= $fam['id_application_fam'] ?>]" value="<?= esc($fam['extension']) ?>" placeholder="Enter Extension" class="px-1 py-1 w-full text-xs">
                                </td>
                                <?php endif; ?>
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
                                    <input type="text" name="fam_occupation[<?= $fam['id_application_fam'] ?>]" value="<?= esc($fam['occupation']) ?>" placeholder="Enter Occupation" class="px-1 py-1 w-full text-xs" required>
                                </td>
                                <td class="px-1 py-1 border">
                                    <input type="text" name="fam_contact_no[<?= $fam['id_application_fam'] ?>]" value="<?= esc($fam['contact_no']) ?>" placeholder="Enter Contact Number" class="px-1 py-1 w-full text-xs" maxlength="11" pattern="\d{0,11}" oninput="this.value = this.value.replace(/[^0-9]/g,'');" required>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
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

        <?php 
        $levels = [
            'elementary' => ['label' => 'Elementary', 'data' => $elementary ?? []],
            'secondary' => ['label' => 'Secondary', 'data' => $highschool ?? []],
            'vocational' => ['label' => 'Vocational / Trade', 'data' => $vocational ?? []],
            'college' => ['label' => 'College', 'data' => $college ?? []],
            'graduate' => ['label' => 'Graduate Studies', 'data' => $graduate ?? []],
        ];
        foreach($levels as $key => $level):
            $data = $level['data'];
        ?>
        <div class="overflow-x-auto mb-2">
            <p class="font-semibold text-sm mb-1"><?= $level['label'] ?></p>

            <!-- Row 1: School, Degree, Units -->
            <table class="table-auto w-full border-collapse text-xs mb-1">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 border">School Name</th>
                        <th class="px-2 py-1 border">Degree / Course</th>
                        <th class="px-2 py-1 border">Highest Level / Units</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-2 py-1 border">
                            <input type="text" name="<?= $key ?>_school" value="<?= esc($data['school_name'] ?? 'N/A') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="<?= $key ?>_degree" value="<?= esc($data['degree_course'] ?? 'N/A') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="<?= $key ?>_units" value="<?= esc($data['highest_level_units'] ?? 'N/A') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Row 2: Period From, Period To, Year, Awards -->
            <table class="table-auto w-full border-collapse text-xs">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 border">Period From</th>
                        <th class="px-2 py-1 border">Period To</th>
                        <th class="px-2 py-1 border">Year Graduated</th>
                        <th class="px-2 py-1 border">Awards / Honors</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-2 py-1 border">
                            <input type="text" name="<?= $key ?>_period_from" value="<?= esc($data['period_from'] ?? 'N/A') ?>" class="px-2 py-1 w-full text-xs" maxlength="4" oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,4);">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="<?= $key ?>_period_to" value="<?= esc($data['period_to'] ?? 'N/A') ?>" class="px-2 py-1 w-full text-xs" maxlength="4" oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,4);">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="<?= $key ?>_year" value="<?= esc($data['year_graduated'] ?? 'N/A') ?>" class="px-2 py-1 w-full text-xs" maxlength="4" oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,4);">
                        </td>
                        <td class="px-2 py-1 border">
                            <input type="text" name="<?= $key ?>_awards" value="<?= esc($data['awards'] ?? 'N/A') ?>" class="px-2 py-1 w-full text-xs">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>

    </div>

    <div class="text-right mt-4">
        <button type="button" onclick="prevStep(3)" class="bg-gray-400 text-white px-4 py-2 rounded text-sm font-semibold">Previous</button>
        <button type="button" onclick="nextStep(3)" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold">Next</button>
    </div>
</div>

<!-- Step 4: Work Experience -->
<div class="step hidden" id="step-4">
    <div class="bg-gray-100 px-3 py-2 rounded font-semibold text-sm mb-4">Work Experience</div>

    <div class="overflow-x-auto relative mb-2">
        <!-- Add Work Experience Button -->
        <button type="button" id="add-work-btn"
            class="bg-clsuGreen text-white px-4 py-1 rounded text-xs font-semibold hover:bg-green-800 absolute right-0 top-0">
            Add Work Exp
        </button>

        <table id="work-table" class="table-auto w-full border-collapse text-xs mt-10">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 border">Position Title</th>
                    <th class="px-2 py-1 border">Office / Company</th>
                    <th class="px-2 py-1 border">Inclusive Dates</th>
                    <th class="px-2 py-1 border">Status of Appointment</th>
                    <th class="px-2 py-1 border">Government Service</th>
                    <th class="px-2 py-1 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($applicant_work)): ?>
                    <?php foreach ($applicant_work as $work): ?>
                        <tr>
                            <td class="px-2 py-1 border">
                                <input type="text" name="position_title[]" value="<?= esc($work['position_title']) ?>" class="px-2 py-1 w-full text-xs">
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="text" name="office[]" value="<?= esc($work['office']) ?>" class="px-2 py-1 w-full text-xs">
                            </td>
                            <td class="px-2 py-1 border flex space-x-1">
                                <input type="date" name="date_from[]" value="<?= esc($work['date_from']) ?>" class="px-2 py-1 w-1/2 text-xs">
                                <input type="date" name="date_to[]" value="<?= esc($work['date_to']) ?>" class="px-2 py-1 w-1/2 text-xs">
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="text" name="status_of_appointment[]" value="<?= esc($work['status_of_appointment']) ?>" class="px-2 py-1 w-full text-xs">
                            </td>
                            <td class="px-2 py-1 border">
                                <select name="govt_service[]" class="px-2 py-1 w-full text-xs">
                                    <option value="Yes" <?= $work['govt_service'] === 'Yes' ? 'selected' : '' ?>>Yes</option>
                                    <option value="No" <?= $work['govt_service'] === 'No' ? 'selected' : '' ?>>No</option>
                                </select>
                            </td>
                            <td class="px-2 py-1 border text-center">
                                <button type="button" class="text-red-500 hover:underline remove-row">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="px-2 py-1 border text-center" colspan="6">No work experience added.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(4)" class="bg-gray-400 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-gray-500">Previous</button>
        <button type="button" onclick="nextStep(4)" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold hover:bg-green-800">Next</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const workTable = document.getElementById('work-table').getElementsByTagName('tbody')[0];
    const addBtn = document.getElementById('add-work-btn');

    function checkEmptyMessage() {
        if (workTable.rows.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `<td colspan="6" class="px-2 py-1 border text-center">No work experience added.</td>`;
            workTable.appendChild(emptyRow);
        }
    }

    // Add new row
    addBtn.addEventListener('click', function() {
        // Remove "No work experience" row if exists
        if (workTable.rows.length === 1 && workTable.rows[0].cells[0].colSpan === 6) {
            workTable.innerHTML = '';
        }

        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-2 py-1 border"><input type="text" name="position_title[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border"><input type="text" name="office[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border flex space-x-1">
                <input type="date" name="date_from[]" class="px-2 py-1 w-1/2 text-xs">
                <input type="date" name="date_to[]" class="px-2 py-1 w-1/2 text-xs">
            </td>
            <td class="px-2 py-1 border"><input type="text" name="status_of_appointment[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border">
                <select name="govt_service[]" class="px-2 py-1 w-full text-xs">
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </td>
            <td class="px-2 py-1 border text-center"><button type="button" class="text-red-500 hover:underline remove-row">Delete</button></td>
        `;
        workTable.appendChild(row);
    });

    // Delete row
    workTable.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            const row = e.target.closest('tr');
            row.remove();
            checkEmptyMessage();
        }
    });
});
</script>
<?php
$user_id = session()->get('user_id');
$db = \Config\Database::connect();
?>

<!-- ===============================
     STEP 5: CIVIL SERVICE ELIGIBILITY
================================ -->
<div class="step hidden" id="step-5">
    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">
        Civil Service Eligibility
    </div>

    <div class="overflow-x-auto mb-2 relative">
        <button
            type="button"
            id="add-cs-btn"
            class="bg-clsuGreen text-white px-4 py-1 rounded text-xs font-semibold hover:bg-green-800 absolute right-0 top-0"
        >
            Add Civil Service
        </button>

        <input type="hidden" name="deleted_cs_ids" id="deleted_cs_ids" value="">

        <table id="cs-table" class="table-auto w-full border-collapse text-xs mt-10">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 border">Eligibility</th>
                    <th class="px-2 py-1 border">Rating / Exam</th>
                    <th class="px-2 py-1 border">Date of Examination</th>
                    <th class="px-2 py-1 border">Place of Examination</th>
                    <th class="px-2 py-1 border">License / PRC No.</th>
                    <th class="px-2 py-1 border">Valid Until</th>
                    <th class="px-2 py-1 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($civil_services)): ?>
                    <?php foreach ($civil_services as $cs): ?>
                        <tr data-id="<?= esc($cs['id_cs'] ?? '') ?>">
                            <td class="px-2 py-1 border"><input type="text" name="eligibility[]" value="<?= esc($cs['eligibility'] ?: '-') ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border"><input type="text" name="rating[]" value="<?= esc($cs['rating'] ?: '-') ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border"><input type="date" name="date_of_exam[]" value="<?= esc($cs['date_of_exam'] != '0000-00-00' ? $cs['date_of_exam'] : '') ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border"><input type="text" name="place_of_exam[]" value="<?= esc($cs['place_of_exam'] ?: '-') ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border"><input type="text" name="license_no[]" value="<?= esc($cs['license_no'] ?: '-') ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border"><input type="date" name="license_valid_until[]" value="<?= esc($cs['license_valid_until'] != '0000-00-00' ? $cs['license_valid_until'] : '') ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border text-center">
                                <button type="button" class="remove-cs-row text-red-600 hover:text-red-800 font-semibold">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="px-2 py-1 border text-center">No civil service record added.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(5)" class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">Previous</button>
        <button type="button" onclick="nextStep(5)" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">Next</button>
    </div>
</div>

<!-- ===============================
     STEP 6: TRAININGS
================================ -->
<div class="step hidden" id="step-6">
    <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">Trainings</div>

    <div class="overflow-x-auto mb-2 relative">
        <button type="button" id="add-training-btn" class="bg-clsuGreen text-white px-4 py-1 rounded text-xs font-semibold hover:bg-green-800 absolute right-0 top-0">
            Add Training
        </button>

        <input type="hidden" name="deleted_training_ids" id="deleted_training_ids" value="">

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
                <?php if(!empty($trainings)): ?>
                    <?php foreach($trainings as $i => $tr): ?>
                        <tr data-id="<?= esc($tr['id_application_trainings'] ?? '') ?>">
                            <td class="px-2 py-1 border">
                                <input type="hidden" name="training_id[]" value="<?= esc($tr['id_application_trainings']) ?>">
                                <select name="training_category_id[]" class="px-2 py-1 w-full text-xs">
                                    <option value="">Select Category</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= esc($cat['id_training_category']) ?>" <?= $cat['id_training_category'] == $tr['training_category_id'] ? 'selected' : '' ?>>
                                            <?= esc($cat['training_category_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="px-2 py-1 border"><input type="text" name="training_name[]" value="<?= esc($tr['training_name']) ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border"><input type="date" name="training_date_from[]" value="<?= esc($tr['date_from']) ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border"><input type="date" name="training_date_to[]" value="<?= esc($tr['date_to']) ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border"><input type="text" name="training_facilitator[]" value="<?= esc($tr['training_facilitator']) ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border"><input type="number" name="training_hours[]" value="<?= esc($tr['training_hours']) ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border"><input type="text" name="training_sponsor[]" value="<?= esc($tr['training_sponsor']) ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border"><input type="text" name="training_remarks[]" value="<?= esc($tr['training_remarks']) ?>" class="px-2 py-1 w-full text-xs"></td>
                            <td class="px-2 py-1 border">
                                <?php if(!empty($tr['certificate_file'])): ?>
                                    <p class="text-xs text-green-700 mb-1">Uploaded: <a href="<?= base_url('files/training/' . $tr['certificate_file']) ?>" target="_blank" class="underline text-blue-600 text-xs">View</a></p>
                                    <input type="hidden" name="existing_certificate_file[]" value="<?= esc($tr['certificate_file']) ?>">
                                <?php else: ?>
                                    <input type="hidden" name="existing_certificate_file[]" value="">
                                <?php endif; ?>
                                <input type="file" name="training_certificate[<?= $i ?>]" class="text-xs mt-1">
                            </td>
                            <td class="px-2 py-1 border text-center">
                                <button type="button" class="remove-training-btn text-red-600 px-1 font-semibold">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="10" class="px-2 py-1 border text-center">No training record added.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-right mt-3">
        <button type="button" onclick="prevStep(6)" class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500">Previous</button>
        <button type="button" onclick="nextStep(6)" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">Next</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const table = document.getElementById('training-table').getElementsByTagName('tbody')[0];
    const addBtn = document.getElementById('add-training-btn');
    const deletedIds = document.getElementById('deleted_training_ids');

    // Add new row
    addBtn.addEventListener('click', function () {
        const rowCount = table.rows.length;
        const newIndex = rowCount;
        const newRow = table.insertRow();

        newRow.innerHTML = `
            <td class="px-2 py-1 border">
                <input type="hidden" name="training_id[]" value="">
                <select name="training_category_id[]" class="px-2 py-1 w-full text-xs">
                    <option value="">Select Category</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= esc($cat['id_training_category']) ?>"><?= esc($cat['training_category_name']) ?></option>
                    <?php endforeach; ?>
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
                <input type="hidden" name="existing_certificate_file[]" value="">
                <input type="file" name="training_certificate[${newIndex}]" class="text-xs mt-1">
            </td>
            <td class="px-2 py-1 border text-center">
                <button type="button" class="remove-training-btn text-red-600 px-1 font-semibold">Delete</button>
            </td>
        `;
    });

    // Remove row
    table.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-training-btn')) {
            const row = e.target.closest('tr');
            const idInput = row.querySelector('input[name="training_id[]"]');
            if (idInput && idInput.value) {
                // Append deleted id
                deletedIds.value += idInput.value + ',';
            }
            row.remove();
        }
    });

});
</script>


<!-- ===============================
     STEP 7: FILE ATTACHMENTS & SUBMIT
================================ -->
<div class="step hidden" id="step-7">
    <div class="bg-gray-100 px-3 py-2 rounded font-semibold text-sm mb-4">File Attachments</div>

    <div class="overflow-x-auto">
        <table class="table-auto w-full text-left border-collapse text-xs">
            <tbody>
                <!-- Resume -->
                <tr>
                    <td class="px-2 py-1 border font-semibold w-1/4">Resume (PDF)</td>
                    <td class="px-2 py-1 border w-3/4">
                        <?php if (!empty($documents['resume'])): ?>
                            <p class="text-xs text-green-700 mb-1">Uploaded: <a href="<?= base_url('files/document/' . $documents['resume']) ?>" target="_blank" class="underline text-blue-600 text-xs">View</a></p>
                            <input type="hidden" name="existing_resume" value="<?= esc($documents['resume']) ?>">
                        <?php endif; ?>
                        <input type="file" name="resume" accept=".pdf" class="px-2 py-1 w-full text-xs">
                    </td>
                </tr>

                <!-- TOR -->
                <tr>
                    <td class="px-2 py-1 border font-semibold w-1/4">Transcript of Records (TOR)</td>
                    <td class="px-2 py-1 border w-3/4">
                        <?php if (!empty($documents['tor'])): ?>
                            <p class="text-xs text-green-700 mb-1">Uploaded: <a href="<?= base_url('files/document/' . $documents['tor']) ?>" target="_blank" class="underline text-blue-600 text-xs">View</a></p>
                            <input type="hidden" name="existing_tor" value="<?= esc($documents['tor']) ?>">
                        <?php endif; ?>
                        <input type="file" name="tor" accept=".pdf" class="px-2 py-1 w-full text-xs">
                    </td>
                </tr>

                <!-- Diploma -->
                <tr>
                    <td class="px-2 py-1 border font-semibold w-1/4">Diploma</td>
                    <td class="px-2 py-1 border w-3/4">
                        <?php if (!empty($documents['diploma'])): ?>
                            <p class="text-xs text-green-700 mb-1">Uploaded: <a href="<?= base_url('files/document/' . $documents['diploma']) ?>" target="_blank" class="underline text-blue-600 text-xs">View</a></p>
                            <input type="hidden" name="existing_diploma" value="<?= esc($documents['diploma']) ?>">
                        <?php endif; ?>
                        <input type="file" name="diploma" accept=".pdf" class="px-2 py-1 w-full text-xs">
                    </td>
                </tr>

                <!-- Other Certificates -->
                <tr>
                    <td class="px-2 py-1 border font-semibold w-1/4">Other Certificates (Optional)</td>
                    <td class="px-2 py-1 border w-3/4">
                        <?php if (!empty($documents['certificate'])): ?>
                            <p class="text-xs text-green-700 mb-1">Uploaded: <a href="<?= base_url('files/document/' . $documents['certificate']) ?>" target="_blank" class="underline text-blue-600 text-xs">View</a></p>
                            <input type="hidden" name="existing_certificate" value="<?= esc($documents['certificate']) ?>">
                        <?php endif; ?>
                        <input type="file" name="certificate" accept=".pdf,.jpg,.png" class="px-2 py-1 w-full text-xs">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="text-right mt-4">
        <button type="button" onclick="prevStep(7)" class="bg-gray-400 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-gray-500">Previous</button>
        <button type="submit" id="submitBtn" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold hover:bg-green-800">Update Application</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // ===== CIVIL SERVICE =====
    const csTbody = document.querySelector('#cs-table tbody');
    const addCsBtn = document.getElementById('add-cs-btn');
    const deletedCsInput = document.getElementById('deleted_cs_ids');

    addCsBtn.addEventListener('click', function () {
        const emptyRow = csTbody.querySelector('td[colspan="7"]');
        if (emptyRow) emptyRow.closest('tr').remove();

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-2 py-1 border"><input type="text" name="eligibility[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border"><input type="text" name="rating[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border"><input type="date" name="date_of_exam[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border"><input type="text" name="place_of_exam[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border"><input type="text" name="license_no[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border"><input type="date" name="license_valid_until[]" class="px-2 py-1 w-full text-xs"></td>
            <td class="px-2 py-1 border text-center"><button type="button" class="remove-cs-row text-red-600 hover:text-red-800 font-semibold">Delete</button></td>
        `;
        csTbody.appendChild(tr);
    });

    csTbody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-cs-row')) {
            const tr = e.target.closest('tr');
            const id = tr.dataset.id;
            if(id) {
                const ids = deletedCsInput.value ? deletedCsInput.value.split(',') : [];
                ids.push(id);
                deletedCsInput.value = ids.join(',');
            }
            tr.remove();
            if(!csTbody.querySelector('tr')) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="7" class="px-2 py-1 border text-center">No civil service record added.</td>`;
                csTbody.appendChild(tr);
            }
        }
    });
});
</script>

</form>
    </div>
</div>
   </div> <!-- End Steps Container -->

            </div> <!-- End Steps Container -->
        </form>
    </div> <!-- End Main Form Container -->
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="applications/update/"]');
    const submitBtn = document.getElementById('submitBtn');

    submitBtn.addEventListener('click', function(e) {
        e.preventDefault(); // stop default submission

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
                form.submit(); // submit the form only if user confirms
            }
        });
    });
});
</script>

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
