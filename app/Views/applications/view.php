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

<div class="w-full min-h-screen flex flex-col lg:flex-row gap-2 p-4 mx-auto flex-1">

<div class="left bg-white p-6 rounded-lg text-center shadow-md self-start flex-shrink-0 lg:basis-[220px]">
    <div class="profile-pic w-32 h-32 mx-auto rounded-full bg-gray-200 overflow-hidden flex items-center justify-center mb-4">
        <?php
        $photoPath = FCPATH . 'uploads/' . ($profile['photo'] ?? '');
        if(!empty($profile['photo']) && file_exists($photoPath)): ?>
            <img src="<?= base_url('uploads/' . esc($profile['photo'])) ?>" class="w-full h-full object-cover rounded-full">
        <?php else: ?>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M12 2a5 5 0 100 10 5 5 0 000-10zm-7 18a7 7 0 0114 0H5z" clip-rule="evenodd"/>
            </svg>
        <?php endif; ?>
    </div>
<h3 class="text-clsuGreen font-bold mb-1">
    <?= esc($user['first_name'] ?? $profile['first_name'] ?? '') ?>
    <?= !empty($user['middle_name'] ?? $profile['middle_name'] ?? '')
        ? esc(substr($user['middle_name'] ?? $profile['middle_name'], 0, 1)) . '. '
        : '' ?>
    <?= esc($user['last_name'] ?? $profile['last_name'] ?? '') ?>
    <?= esc($user['extension'] ?? $profile['suffix'] ?? '') ?>
</h3>

    <p class="text-gray-700 mb-1"><?= esc($user['email'] ?? $profile['email'] ?? 'noemail@example.com') ?></p>
</div>

<div class="right w-full flex-1 space-y-6">

<div class="w-full bg-white shadow rounded-lg p-5 text-gray-700 text-sm">
<div class="flex justify-between items-center mb-2">
    <h1 class="text-2xl font-bold text-clsuGreen">Application Details</h1>
    <div class="flex gap-2">
        <a href="<?= site_url('dashboard') ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm font-medium">
            ✕
        </a>
    </div>
</div>
        <?php 
            function renderRow($label, $value) {
                return '<div class="grid grid-cols-3 gap-4 py-1 border-b">
                            <p class="font-bold text-gray-700 col-span-1">' . $label . '</p>
                            <p class="font-normal text-gray-700 col-span-2">' . $value . '</p>
                        </div>';
            }
        ?>
<div class="w-full bg-transparent p-0 text-gray-700 text-sm">
<!-- JOB DETAILS -->
<div class="overflow-x-auto mb-4">
  <table class="table-auto w-full border-collapse text-xs">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-2 py-1 border">Position</th>
        <th class="px-2 py-1 border">Office</th>
        <th class="px-2 py-1 border">Department</th>
        <th class="px-2 py-1 border">Monthly Salary</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="px-2 py-1 border"><?= esc($job['position_title'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($job['office'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($job['department'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= isset($job['monthly_salary']) ? 'Php ' . number_format($job['monthly_salary'],2) : 'Php 0.00' ?></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="overflow-x-auto mb-4">
  <table class="table-auto w-full border-collapse text-xs">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-2 py-1 border">Applied At</th>
        <th class="px-2 py-1 border">Application Deadline</th>
        <th class="px-2 py-1 border">Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="px-2 py-1 border">
          <?= isset($app['applied_at']) ? date('F j, Y', strtotime($app['applied_at'])) : '-' ?>
        </td>
        <td class="px-2 py-1 border">
            <?= !empty($job['application_deadline']) 
            ? date('F j, Y', strtotime($job['application_deadline'])) 
            : '-' ?>
        </td>
        <td class="px-2 py-1 border"><?= esc($app['application_status'] ?? '-') ?></td>
      </tr>
    </tbody>
  </table>
</div>
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-clsuGreen" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
        </svg>
        <h2 class="text-xl font-bold text-clsuGreen">Personal Information</h2>
    </div>
    
    <!-- Name Information -->
    <h3 class="text-lg font-semibold text-gray-800 mb-3">Name Information</h3>
    <div class="overflow-x-auto mb-6">
        <table class="table-auto w-full border-collapse text-sm">
            <tbody>
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700 w-1/4">First Name</td>
                    <td class="py-2 text-gray-800"><?= esc($app['personal']['first_name'] ?? '-') ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700">Middle Name</td>
                    <td class="py-2 text-gray-800"><?= esc($app['personal']['middle_name'] ?? '-') ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700">Last Name</td>
                    <td class="py-2 text-gray-800"><?= esc($app['personal']['last_name'] ?? '-') ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700">Suffix</td>
                    <td class="py-2 text-gray-800"><?= esc($app['personal']['extension'] ?? '-') ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Personal Details -->
    <h3 class="text-lg font-semibold text-gray-800 mb-3">Personal Details</h3>
    <div class="overflow-x-auto mb-6">
        <table class="table-auto w-full border-collapse text-sm">
            <tbody>
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700 w-1/4">Sex</td>
                    <td class="py-2 text-gray-800"><?= esc($app['personal']['sex'] ?? '-') ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700">Date of Birth</td>
                    <td class="py-2 text-gray-800"><?= esc($app['personal']['date_of_birth_formatted'] ?? '-') ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700">Civil Status</td>
                    <td class="py-2 text-gray-800"><?= esc($app['personal']['civil_status'] ?? '-') ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Contact Information -->
    <h3 class="text-lg font-semibold text-gray-800 mb-3">Contact Information</h3>
    <div class="overflow-x-auto mb-6">
        <table class="table-auto w-full border-collapse text-sm">
            <tbody>
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700 w-1/4">Email</td>
                    <td class="py-2 text-gray-800"><?= esc($app['personal']['email'] ?? '-') ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700">Phone Number</td>
                    <td class="py-2 text-gray-800"><?= esc($app['personal']['phone'] ?? '-') ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700">Citizenship</td>
                    <td class="py-2 text-gray-800"><?= esc($app['personal']['citizenship'] ?? '-') ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Address Information -->
    <h3 class="text-lg font-semibold text-gray-800 mb-3">Address Information</h3>
    <div class="overflow-x-auto">
        <table class="table-auto w-full border-collapse text-sm">
            <tbody>
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700 w-1/4">Residential Address</td>
                    <td class="py-2 text-gray-800"><?= esc($app['personal']['residential_address'] ?? '-') ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700">Permanent Address</td>
                    <td class="py-2 text-gray-800"><?= esc($app['personal']['permanent_address'] ?? '-') ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="mb-8">
    <h2 class="text-xl font-bold text-clsuGreen mb-4 pb-2 border-b border-clsuGreen">Additional Personal Details</h2>
    
    <div class="space-y-2">
        <div>
            <p class="font-medium text-gray-700">Are you CLSU Employee?</p>
            <p class="ml-4"><?= esc($app['personal']['is_clsu_employee'] ?? 'No') ?></p>
            <?php if (!empty($app['personal']['clsu_employee_specify']) && $app['personal']['is_clsu_employee'] === 'Yes'): ?>
                <p class="ml-8 text-gray-600">Specify: <?= esc($app['personal']['clsu_employee_specify']) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <p class="font-medium text-gray-700">Religion</p>
            <p class="ml-4"><?= esc($app['personal']['religion'] ?? '-') ?></p>
        </div>
        <div>
            <p class="font-medium text-gray-700">Indigenous Person</p>
            <p class="ml-4"><?= esc($app['personal']['is_indigenous'] ?? 'No') ?></p>
            <?php if (!empty($app['personal']['indigenous_specify']) && $app['personal']['is_indigenous'] === 'Yes'): ?>
                <p class="ml-8 text-gray-600">Specify: <?= esc($app['personal']['indigenous_specify']) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <p class="font-medium text-gray-700">Person with Disability</p>
            <p class="ml-4"><?= esc($app['personal']['is_pwd'] ?? 'No') ?></p>
            <?php if (!empty($app['personal']['pwd_specify']) && $app['personal']['is_pwd'] === 'Yes'): ?>
                <p class="ml-8 text-gray-600">Specify: <?= esc($app['personal']['pwd_specify']) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <p class="font-medium text-gray-700">Solo Parent</p>
            <p class="ml-4"><?= esc($app['personal']['is_solo_parent'] ?? 'No') ?></p>
        </div>
    </div>
</div>
<div class="mb-8">
    <h2 class="text-xl font-bold text-clsuGreen mb-4 pb-2 border-b border-clsuGreen">Educational Background</h2>
    
    <?php if (!empty($app['education_display'])): ?>
        <?php 
        $currentLevel = '';
        foreach ($app['education_display'] as $edu): 
            if (!empty($edu['level']) && $edu['level'] !== $currentLevel):
                $currentLevel = $edu['level'];
                if ($currentLevel !== '-'): ?>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 mt-6"><?= esc($currentLevel) ?></h3>
                <?php endif;
            endif;
            if ($edu['school_name'] !== '-' || $edu['degree_course'] !== '-'): ?>
                <div class="space-y-2 mb-4 ml-4">
                    <div>
                        <p class="font-medium text-gray-700">School Name</p>
                        <p class="ml-4"><?= esc($edu['school_name']) ?></p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Degree / Course</p>
                        <p class="ml-4"><?= esc($edu['degree_course']) ?></p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Period</p>
                        <p class="ml-4"><?= esc($edu['period_from']) ?> - <?= esc($edu['period_to']) ?></p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Year Graduated</p>
                        <p class="ml-4"><?= esc($edu['year_graduated']) ?></p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Highest Level / Units Earned</p>
                        <p class="ml-4"><?= esc($edu['highest_level_units']) ?></p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Scholarship / Academic Honors</p>
                        <p class="ml-4"><?= esc($edu['awards']) ?></p>
                    </div>
                </div>
            <?php endif;
        endforeach; ?>
    <?php else: ?>
        <div class="text-center py-4">
            <p class="text-gray-500">No education records found.</p>
        </div>
    <?php endif; ?>
</div>

<div class="mb-8">
    <h2 class="text-xl font-bold text-clsuGreen mb-4 pb-2 border-b border-clsuGreen">Work Experience</h2>
    
    <?php if (!empty($app['work'])): ?>
        <?php foreach ($app['work'] as $work): ?>
            <div class="space-y-2 mb-6 ml-4">
                <div>
                    <p class="font-medium text-gray-700">Position / Title</p>
                    <p class="ml-4"><?= !empty($work['position_title']) ? esc($work['position_title']) : '-' ?></p>
                </div>
                <div>
                    <p class="font-medium text-gray-700">Office</p>
                    <p class="ml-4"><?= !empty($work['office']) ? esc($work['office']) : '-' ?></p>
                </div>
                <div>
                    <p class="font-medium text-gray-700">Inclusive Dates</p>
                    <?php 
                      $from = !empty($work['date_from']) ? date('F d, Y', strtotime($work['date_from'])) : '-';
                      $to   = !empty($work['date_to']) ? date('F d, Y', strtotime($work['date_to'])) : '-';
                    ?>
                    <p class="ml-4"><?= $from ?> - <?= $to ?></p>
                </div>
                <div>
                    <p class="font-medium text-gray-700">Status of Appointment</p>
                    <p class="ml-4"><?= !empty($work['status_of_appointment']) ? esc($work['status_of_appointment']) : '-' ?></p>
                </div>
                <div>
                    <p class="font-medium text-gray-700">Government Service</p>
                    <p class="ml-4"><?= (isset($work['govt_service']) && strtoupper($work['govt_service']) === 'YES') ? 'Yes' : 'No' ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-4">
            <p class="text-gray-500">No work experience records found.</p>
        </div>
    <?php endif; ?>
</div>
<div class="mb-8">
    <h2 class="text-xl font-bold text-clsuGreen mb-4 pb-2 border-b border-clsuGreen">Civil Service Eligibility</h2>
    
    <?php if (!empty($app['civil'])): ?>
        <div class="space-y-4">
            <?php foreach ($app['civil'] as $cs): ?>
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Eligibility</p>
                            <p class="text-base font-medium text-gray-800"><?= esc($cs['eligibility'] ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Rating / Exam</p>
                            <p class="text-base text-gray-800"><?= esc($cs['rating'] ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Date of Examination</p>
                            <p class="text-base text-gray-800">
                                <?= !empty($cs['date_of_exam']) && $cs['date_of_exam'] !== '-' ? date('F d, Y', strtotime($cs['date_of_exam'])) : '-' ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Place of Examination</p>
                            <p class="text-base text-gray-800"><?= esc($cs['place_of_exam'] ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">License / PRC No.</p>
                            <p class="text-base text-gray-800"><?= esc($cs['license_no'] ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Valid Until</p>
                            <p class="text-base text-gray-800">
                                <?= !empty($cs['license_valid_until']) && $cs['license_valid_until'] !== '-' ? date('F d, Y', strtotime($cs['license_valid_until'])) : '-' ?>
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-600 mb-1">Certificate</p>
                            <?php if (!empty($cs['certificate'])): ?>
                                <button type="button" 
                                        class="view-certificate-btn text-blue-600 text-sm font-medium hover:text-blue-800 underline">
                                    View Certificate
                                </button>
                            <?php else: ?>
                                <span class="text-gray-500">No certificate available</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-8">
            <p class="text-gray-500 text-lg">No civil service records found.</p>
        </div>
    <?php endif; ?>
</div>

<div class="mb-8">
    <h2 class="text-xl font-bold text-clsuGreen mb-4 pb-2 border-b border-clsuGreen">Trainings</h2>
    
    <?php if (!empty($trainings)): ?>
        <div class="space-y-4">
            <?php foreach ($trainings as $tr): ?>
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Training Name</p>
                            <p class="text-base font-medium text-gray-800"><?= esc($tr['training_name'] ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Category</p>
                            <p class="text-base text-gray-800"><?= esc($tr['training_category_name'] ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Inclusive Dates</p>
                            <p class="text-base text-gray-800"><?= $tr['date_from'] ?> - <?= $tr['date_to'] ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Facilitator</p>
                            <p class="text-base text-gray-800"><?= esc($tr['training_facilitator'] ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Hours</p>
                            <p class="text-base text-gray-800"><?= esc($tr['training_hours'] ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Sponsor</p>
                            <p class="text-base text-gray-800"><?= esc($tr['training_sponsor'] ?? '-') ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-600 mb-1">Remarks</p>
                            <p class="text-base text-gray-800"><?= esc($tr['training_remarks'] ?? '-') ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-600 mb-1">Certificate</p>
                            <?php if (!empty($tr['certificate_file'])): ?>
                                <button type="button" 
                                        class="view-training-certificate-btn text-blue-600 text-sm font-medium hover:text-blue-800 underline"
                                        data-file="<?= site_url('applications/viewTrainingCertificate/'.$app['id_job_application'].'/'.$tr['certificate_file']) ?>">
                                    View Certificate
                                </button>
                            <?php else: ?>
                                <span class="text-gray-500">No certificate available</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-8">
            <p class="text-gray-500 text-lg">No trainings records found.</p>
        </div>
    <?php endif; ?>
</div>

<div class="mb-8">
    <h2 class="text-xl font-bold text-clsuGreen mb-4 pb-2 border-b border-clsuGreen">Uploaded Documents</h2>
    
    <?php
    // List of document fields in the database
    $docs = [
        'pds' => 'Fully accomplished Personal Data Sheet (PDS) with recent passport-sized picture (CS Form No. 212, Revised 2017)',
        'performance_rating' => 'Performance Rating in the present position for the last rating period',
        'resume' => 'Resume',
        'tor' => 'Transcript of Records (TOR)',
        'diploma' => 'Diploma'
    ];

    $hasDocuments = false;
    foreach ($docs as $key => $label) {
        if (!empty($app['documents'][$key])) {
            $hasDocuments = true;
            break;
        }
    }
    ?>
    
    <?php if ($hasDocuments): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($docs as $key => $label): 
                $file = $app['documents'][$key] ?? null;
            ?>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 mb-1">Document</p>
                            <p class="text-base font-medium text-gray-800 mb-2"><?= esc($label) ?></p>
                        </div>
                        <div>
                            <?php if (!empty($file)): ?>
                                <button type="button" 
                                        class="view-document-btn text-blue-600 text-sm font-medium hover:text-blue-800 underline"
                                        data-file="<?= site_url('applications/viewDocument/'.$app['id_job_application'].'/'.$key) ?>">
                                    View Document
                                </button>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm">No file uploaded</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-8">
            <p class="text-gray-500 text-lg">No documents uploaded.</p>
        </div>
    <?php endif; ?>
</div>
</div>
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

<!-- Certificate Modal -->
<div id="certificate-modal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-6xl h-full flex flex-col shadow-lg">
        <iframe id="certificate-frame" class="flex-1 w-full h-full border-none"></iframe>
    </div>
</div>

<!-- Training Certificate Modal -->
<div id="training-certificate-modal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-6xl h-full flex flex-col shadow-lg">
        <iframe id="training-certificate-frame" class="flex-1 w-full h-full border-none"></iframe>
    </div>
</div>

<!-- Documents Modal -->
<div id="document-modal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-6xl h-full flex flex-col shadow-lg">
        <iframe id="document-frame" class="flex-1 w-full h-full border-none"></iframe>
    </div>
</div>

<div id="jobModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white max-w-3xl w-full rounded-lg p-6 max-h-[90vh] overflow-y-auto">
        <button onclick="closeModal()" class="float-right text-gray-500 text-xl">✕</button>
        <div id="modalContent"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Civil Service Certificate Modal
    const certModal = document.getElementById('certificate-modal');
    const certIframe = document.getElementById('certificate-frame');
    
    // Training Certificate Modal
    const trainingModal = document.getElementById('training-certificate-modal');
    const trainingIframe = document.getElementById('training-certificate-frame');
    
    // Documents Modal
    const documentModal = document.getElementById('document-modal');
    const documentIframe = document.getElementById('document-frame');

    // Handle clicks on view buttons
    document.addEventListener('click', async function(e) {
        const civilBtn = e.target.closest('.view-certificate-btn');
        const trainingBtn = e.target.closest('.view-training-certificate-btn');
        const documentBtn = e.target.closest('.view-document-btn');
        
        if (civilBtn || trainingBtn || documentBtn) {
            e.preventDefault();
            e.stopPropagation();
            
            const fileUrl = (civilBtn || trainingBtn || documentBtn).dataset.file;
            const isTraining = !!trainingBtn;
            const isDocument = !!documentBtn;
            
            if (!fileUrl) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No file selected',
                    showConfirmButton: false,
                    timer: 1500
                });
                return;
            }

            try {
                // Check if file exists first
                const res = await fetch(fileUrl);
                
                // If JSON returned → file missing or error
                if (res.headers.get('content-type')?.includes('application/json')) {
                    const data = await res.json();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'File not found or already deleted.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }
                 
                // File exists → show in appropriate modal
                if (isTraining) {
                    trainingIframe.src = fileUrl;
                    trainingModal.classList.remove('hidden');
                } else if (isDocument) {
                    documentIframe.src = fileUrl;
                    documentModal.classList.remove('hidden');
                } else {
                    certIframe.src = fileUrl;
                    certModal.classList.remove('hidden');
                }
                
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to open file.',
                    showConfirmButton: false,
                    timer: 1500
                });
                console.error(err);
            }
        }
    });

    // Close civil service modal when clicking outside
    certModal.addEventListener('click', function(e) {
        if (e.target === certModal) {
            certIframe.src = '';
            certModal.classList.add('hidden');
        }
    });

    // Close training modal when clicking outside
    trainingModal.addEventListener('click', function(e) {
        if (e.target === trainingModal) {
            trainingIframe.src = '';
            trainingModal.classList.add('hidden');
        }
    });

    // Close documents modal when clicking outside
    documentModal.addEventListener('click', function(e) {
        if (e.target === documentModal) {
            documentIframe.src = '';
            documentModal.classList.add('hidden');
        }
    });

    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!certModal.classList.contains('hidden')) {
                certIframe.src = '';
                certModal.classList.add('hidden');
            }
            if (!trainingModal.classList.contains('hidden')) {
                trainingIframe.src = '';
                trainingModal.classList.add('hidden');
            }
            if (!documentModal.classList.contains('hidden')) {
                documentIframe.src = '';
                documentModal.classList.add('hidden');
            }
        }
    });

});
</script>

</body>
</html>
