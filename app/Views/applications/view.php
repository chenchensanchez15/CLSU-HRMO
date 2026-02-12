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
                <a href="<?= site_url('dashboard') ?>" class="text-xl font-bold no-underline hover:no-underline" style="text-decoration: none;">CLSU Online Job Application</a>
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
<!-- ROW 1: Status with Back Button on Right -->
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 rounded">
    <div class="flex justify-between items-center">

        <!-- LEFT: Status Info -->
        <div class="flex items-center">
            <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" 
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" 
                    clip-rule="evenodd">
                </path>
            </svg>
            <div>
                <h3 class="text-sm font-semibold text-yellow-800 mb-1">
                    Application Status
                </h3>
                <p class="text-sm font-bold text-yellow-700">
                    <?= esc($app['application_status'] ?? '-') ?>
                </p>
            </div>
        </div>

        <!-- RIGHT: Back Button -->
        <a href="<?= site_url('dashboard') ?>"
           class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm font-medium transition">
            Back
        </a>

    </div>
</div>

<div class="flex justify-between items-center mb-2">
    
    <h1 class="text-2xl font-bold text-clsuGreen">Application Details</h1>
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
<div class="mb-5">
    <!-- ROW 2: Position, Office, Department -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-3">
        <div>
            <p class="text-xs font-bold text-gray-700 mb-1">POSITION</p>
            <p class="text-xs text-gray-800"><?= esc($job['position_title'] ?? '-') ?></p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-700 mb-1">OFFICE</p>
            <p class="text-xs text-gray-800"><?= esc($job['office'] ?? '-') ?></p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-700 mb-1">DEPARTMENT</p>
            <p class="text-xs text-gray-800"><?= esc($job['department'] ?? '-') ?></p>
        </div>
    </div>
    
    <!-- ROW 3: Monthly Salary, Applied At, Application Deadline -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
        <div>
            <p class="text-xs font-bold text-gray-700 mb-1">MONTHLY SALARY</p>
            <p class="text-xs text-gray-800"><?= isset($job['monthly_salary']) ? 'Php ' . number_format($job['monthly_salary'],2) : 'Php 0.00' ?></p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-700 mb-1">APPLIED AT</p>
            <p class="text-xs text-gray-800">
                <?= isset($app['applied_at']) ? date('F j, Y', strtotime($app['applied_at'])) : '-' ?>
            </p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-700 mb-1">APPLICATION DEADLINE</p>
            <p class="text-xs text-gray-800">
                <?= !empty($job['application_deadline']) 
                ? date('F j, Y', strtotime($job['application_deadline'])) 
                : '-' ?>
            </p>
        </div>
    </div>
</div>

<!-- Green Divider Line -->
<div class="border-t-2 border-clsuGreen my-3"></div>
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-clsuGreen" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
        </svg>
        <h2 class="text-sm font-bold text-clsuGreen">Personal Information</h2>
    </div>
    
    <!-- ROW 1: Full Name + Personal Details + Citizenship + Phone (7 items) -->
    <div class="flex flex-wrap gap-0 mb-4">
        <!-- FULL NAME -->
        <div class="flex-1 min-w-[120px]">
            <p class="text-xs font-bold text-gray-700 mb-0.5">FULL NAME</p>
            <p class="text-xs text-gray-800">
                <?= esc(($app['personal']['first_name'] ?? '') . ' ' . ($app['personal']['middle_name'] ?? '') . ' ' . ($app['personal']['last_name'] ?? '') . ($app['personal']['extension'] ? ' ' . $app['personal']['extension'] : '')) ?: '-' ?>
            </p>
        </div>
        <!-- SEX -->
        <div class="flex-1 min-w-[80px]">
            <p class="text-xs font-bold text-gray-700 mb-0.5">SEX</p>
            <p class="text-xs text-gray-800"><?= esc($app['personal']['sex'] ?? '-') ?></p>
        </div>
        <!-- DATE OF BIRTH -->
        <div class="flex-1 min-w-[120px]">
            <p class="text-xs font-bold text-gray-700 mb-0.5">DATE OF BIRTH</p>
            <p class="text-xs text-gray-800"><?= esc($app['personal']['date_of_birth_formatted'] ?? '-') ?></p>
        </div>
        <!-- CIVIL STATUS -->
        <div class="flex-1 min-w-[100px]">
            <p class="text-xs font-bold text-gray-700 mb-0.5">CIVIL STATUS</p>
            <p class="text-xs text-gray-800"><?= esc($app['personal']['civil_status'] ?? '-') ?></p>
        </div>
        <!-- CITIZENSHIP -->
        <div class="flex-1 min-w-[100px]">
            <p class="text-xs font-bold text-gray-700 mb-0.5">CITIZENSHIP</p>
            <p class="text-xs text-gray-800"><?= esc($app['personal']['citizenship'] ?? '-') ?></p>
        </div>
        <!-- PHONE -->
        <div class="flex-1 min-w-[120px]">
            <p class="text-xs font-bold text-gray-700 mb-0.5">PHONE</p>
            <p class="text-xs text-gray-800"><?= esc($app['personal']['phone'] ?? '-') ?></p>
        </div>
    </div>
    
    <!-- ROW 2: Contact + Address Information (3 items in single row) -->
    <div class="flex flex-wrap gap-0">
        <!-- EMAIL -->
        <div class="flex-1 min-w-[150px]">
            <p class="text-xs font-bold text-gray-700 mb-0.5">EMAIL</p>
            <p class="text-xs text-gray-800 break-words"><?= esc($app['personal']['email'] ?? '-') ?></p>
        </div>
        <!-- RESIDENTIAL ADDRESS -->
        <div class="flex-1 min-w-[200px]">
            <p class="text-xs font-bold text-gray-700 mb-0.5">RESIDENTIAL ADDRESS</p>
            <p class="text-xs text-gray-800"><?= esc($app['personal']['residential_address'] ?? '-') ?></p>
        </div>
        <!-- PERMANENT ADDRESS -->
        <div class="flex-1 min-w-[200px]">
            <p class="text-xs font-bold text-gray-700 mb-0.5">PERMANENT ADDRESS</p>
            <p class="text-xs text-gray-800"><?= esc($app['personal']['permanent_address'] ?? '-') ?></p>
        </div>
    </div>
</div>

<!-- Green Divider Line -->
<div class="border-t-2 border-clsuGreen my-3"></div>
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-clsuGreen" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
        <h2 class="text-sm font-bold text-clsuGreen">Additional Personal Details</h2>
    </div>
    
    <!-- ROW 1: CLSU Employee, Religion, Indigenous Person -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-3">
        <div>
            <p class="text-xs font-bold text-gray-700 mb-1">ARE YOU CLSU EMPLOYEE?</p>
            <?php if ($app['personal']['is_clsu_employee'] === 'Yes'): ?>
                <p class="text-xs text-gray-800">Yes</p>
                <?php if (!empty($app['personal']['clsu_employee_type'])): ?>
                    <p class="text-xs text-gray-600">Type: <?= esc($app['personal']['clsu_employee_type']) ?></p>
                <?php endif; ?>
                <?php if (!empty($app['personal']['clsu_employee_specify'])): ?>
                    <p class="text-xs text-gray-600">Specify: <?= esc($app['personal']['clsu_employee_specify']) ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-xs text-gray-800">No</p>
            <?php endif; ?>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-700 mb-1">RELIGION</p>
            <p class="text-xs text-gray-800"><?= esc($app['personal']['religion'] ?? '-') ?></p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-700 mb-1">INDIGENOUS PERSON</p>
            <p class="text-xs text-gray-800"><?= esc($app['personal']['is_indigenous'] ?? 'No') ?></p>
            <?php if (!empty($app['personal']['indigenous_specify']) && $app['personal']['is_indigenous'] === 'Yes'): ?>
                <p class="text-xs text-gray-600">Specify: <?= esc($app['personal']['indigenous_specify']) ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- ROW 2: Person with Disability, Solo Parent -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        <div>
            <p class="text-xs font-bold text-gray-700 mb-1">PERSON WITH DISABILITY</p>
            <?php if ($app['personal']['is_pwd'] === 'Yes'): ?>
                <p class="text-xs text-gray-800">Yes</p>
                <?php if (!empty($app['personal']['pwd_type'])): ?>
                    <p class="text-xs text-gray-600">Type: <?= esc($app['personal']['pwd_type']) ?></p>
                <?php endif; ?>
                <?php if (!empty($app['personal']['pwd_specify'])): ?>
                    <p class="text-xs text-gray-600">Specify: <?= esc($app['personal']['pwd_specify']) ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-xs text-gray-800">No</p>
            <?php endif; ?>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-700 mb-1">SOLO PARENT</p>
            <p class="text-xs text-gray-800"><?= esc($app['personal']['is_solo_parent'] ?? 'No') ?></p>
        </div>
    </div>
</div>

<!-- Green Divider Line -->
<div class="border-t-2 border-clsuGreen my-3"></div>
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-clsuGreen" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l1.818-.78v3.957a9.026 9.026 0 00-2.364 1.638z"/>
            <path d="M15.75 12.5a1 1 0 00-1 1v2a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 00-1-1h-2z"/>
        </svg>
        <h2 class="text-sm font-bold text-clsuGreen">Educational Background</h2>
    </div>
    
    <?php if (!empty($app['education_display'])): ?>
        <!-- Labels Row (shown only once at the top) -->
        <div class="grid grid-cols-1 md:grid-cols-7 gap-2 mb-1">
            <div><p class="text-xs font-bold text-gray-700">LEVEL</p></div>
            <div><p class="text-xs font-bold text-gray-700">SCHOOL NAME</p></div>
            <div><p class="text-xs font-bold text-gray-700">DEGREE / COURSE</p></div>
            <div><p class="text-xs font-bold text-gray-700">PERIOD</p></div>
            <div><p class="text-xs font-bold text-gray-700">YEAR GRADUATED</p></div>
            <div><p class="text-xs font-bold text-gray-700">HIGHEST LEVEL / UNITS EARNED</p></div>
            <div><p class="text-xs font-bold text-gray-700">SCHOLARSHIP / ACADEMIC HONORS</p></div>
        </div>
        
        <!-- Data Rows -->
        <?php foreach ($app['education_display'] as $edu): 
            if ($edu['school_name'] !== '-' || $edu['degree_course'] !== '-'): 
                // Combine degree and course information
                $degreeCourseDisplay = '';
                if (!empty($edu['degree_course']) && $edu['degree_course'] !== '-') {
                    $degreeCourseDisplay = esc($edu['degree_course']);
                }
                if (!empty($edu['course']) && $edu['course'] !== '-') {
                    if (!empty($degreeCourseDisplay)) {
                        $degreeCourseDisplay .= ' - ' . esc($edu['course']);
                    } else {
                        $degreeCourseDisplay = esc($edu['course']);
                    }
                }
                if (empty($degreeCourseDisplay)) {
                    $degreeCourseDisplay = '-';
                }
                ?>
                <div class="grid grid-cols-1 md:grid-cols-7 gap-2 mb-2 p-2 bg-gray-50 rounded">
                    <div><p class="text-xs text-gray-800"><?= !empty($edu['level']) && $edu['level'] !== '-' ? esc($edu['level']) : '' ?></p></div>
                    <div><p class="text-xs text-gray-800"><?= esc($edu['school_name']) ?></p></div>
                    <div><p class="text-xs text-gray-800"><?= $degreeCourseDisplay ?></p></div>
                    <div><p class="text-xs text-gray-800"><?= esc($edu['period_from']) ?> - <?= esc($edu['period_to']) ?></p></div>
                    <div><p class="text-xs text-gray-800"><?= esc($edu['year_graduated']) ?></p></div>
                    <div><p class="text-xs text-gray-800"><?= esc($edu['highest_level_units']) ?></p></div>
                    <div><p class="text-xs text-gray-800"><?= esc($edu['awards']) ?></p></div>
                </div>
            <?php endif;
        endforeach; ?>
        
   <?php else: ?>
    <div class="bg-gray-50 rounded p-2">
        <p class="text-xs text-gray-600">
            No educational background records found.
        </p>
    </div>
<?php endif; ?>

</div>

<!-- Green Divider Line -->
<div class="border-t-2 border-clsuGreen my-3"></div>
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-clsuGreen" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
        </svg>
        <h2 class="text-sm font-bold text-clsuGreen">Work Experience</h2>
    </div>
    
    <?php if (!empty($app['work'])): ?>
        <!-- Labels Row (shown only once at the top) -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-2 mb-1">
            <div><p class="text-xs font-bold text-gray-700">POSITION / TITLE</p></div>
            <div><p class="text-xs font-bold text-gray-700">OFFICE</p></div>
            <div><p class="text-xs font-bold text-gray-700">INCLUSIVE DATES</p></div>
            <div><p class="text-xs font-bold text-gray-700">STATUS OF APPOINTMENT</p></div>
            <div><p class="text-xs font-bold text-gray-700">GOVERNMENT SERVICE</p></div>
        </div>
        
        <!-- Data Rows -->
        <?php foreach ($app['work'] as $work): ?>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-2 mb-2 p-2 bg-gray-50 rounded">
                <div><p class="text-xs text-gray-800"><?= !empty($work['position_title']) ? esc($work['position_title']) : '-' ?></p></div>
                <div><p class="text-xs text-gray-800"><?= !empty($work['office']) ? esc($work['office']) : '-' ?></p></div>
                <div><p class="text-xs text-gray-800">
                    <?php 
                      $from = !empty($work['date_from']) ? date('F d, Y', strtotime($work['date_from'])) : '-';
                      $to   = !empty($work['date_to']) ? date('F d, Y', strtotime($work['date_to'])) : '-';
                      echo $from . ' - ' . $to;
                    ?>
                </p></div>
                <div><p class="text-xs text-gray-800"><?= !empty($work['status_of_appointment']) ? esc($work['status_of_appointment']) : '-' ?></p></div>
                <div><p class="text-xs text-gray-800"><?= (isset($work['govt_service']) && strtoupper($work['govt_service']) === 'YES') ? 'Yes' : 'No' ?></p></div>
            </div>
        <?php endforeach; ?>
        
   <?php else: ?>
    <div class="bg-gray-50 rounded p-2">
        <p class="text-xs text-gray-600">
            No civil work experience records found.
        </p>
    </div>
<?php endif; ?>

</div>

<div class="border-t-2 border-clsuGreen my-3"></div>
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-clsuGreen" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
        <h2 class="text-sm font-bold text-clsuGreen">Civil Service Eligibility</h2>
    </div>
    
    <?php if (!empty($app['civil'])): ?>
        <!-- Labels Row (shown only once at the top) -->
        <div class="grid grid-cols-1 md:grid-cols-7 gap-2 mb-1">
            <div><p class="text-xs font-bold text-gray-700">ELIGIBILITY</p></div>
            <div><p class="text-xs font-bold text-gray-700">RATING / EXAM</p></div>
            <div><p class="text-xs font-bold text-gray-700">DATE OF EXAMINATION</p></div>
            <div><p class="text-xs font-bold text-gray-700">PLACE OF EXAMINATION</p></div>
            <div><p class="text-xs font-bold text-gray-700">LICENSE / PRC NO.</p></div>
            <div><p class="text-xs font-bold text-gray-700">VALID UNTIL</p></div>
            <div><p class="text-xs font-bold text-gray-700">CERTIFICATE</p></div>
        </div>
        
        <!-- Data Rows -->
        <?php foreach ($app['civil'] as $cs): ?>
            <div class="grid grid-cols-1 md:grid-cols-7 gap-2 mb-2 p-2 bg-gray-50 rounded">
                <div><p class="text-xs text-gray-800"><?= esc($cs['eligibility'] ?? '-') ?></p></div>
                <div><p class="text-xs text-gray-800"><?= esc($cs['rating'] ?? '-') ?></p></div>
                <div><p class="text-xs text-gray-800">
                    <?= !empty($cs['date_of_exam']) && $cs['date_of_exam'] !== '-' ? date('F d, Y', strtotime($cs['date_of_exam'])) : '-' ?>
                </p></div>
                <div><p class="text-xs text-gray-800"><?= esc($cs['place_of_exam'] ?? '-') ?></p></div>
                <div><p class="text-xs text-gray-800"><?= esc($cs['license_no'] ?? '-') ?></p></div>
                <div><p class="text-xs text-gray-800">
                    <?= !empty($cs['license_valid_until']) && $cs['license_valid_until'] !== '-' ? date('F d, Y', strtotime($cs['license_valid_until'])) : '-' ?>
                </p></div>
                <div>
                    <?php if (!empty($cs['certificate'])): ?>
                        <button type="button" 
                                class="view-certificate-btn inline-flex items-center text-blue-600 text-xs hover:text-blue-800"
                                data-file="<?= site_url('applications/viewCivilCertificate/'.$cs['certificate']) ?>">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Certificate
                        </button>
                    <?php else: ?>
                        <span class="text-gray-500 text-xs">-</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
   <?php else: ?>
    <div class="bg-gray-50 rounded p-2">
        <p class="text-xs text-gray-600">
            No civil service eligibility records found.
        </p>
    </div>
<?php endif; ?>

</div>

<!-- Green Divider Line -->
<div class="border-t-2 border-clsuGreen my-3"></div>
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-clsuGreen" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l1.818-.78v3.957a9.026 9.026 0 00-2.364 1.638z"/>
            <path d="M15.75 12.5a1 1 0 00-1 1v2a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 00-1-1h-2z"/>
        </svg>
        <h2 class="text-sm font-bold text-clsuGreen">Trainings</h2>
    </div>
    
    <?php if (!empty($trainings)): ?>
        <!-- Labels Row (shown only once at the top) -->
        <div class="grid grid-cols-1 md:grid-cols-8 gap-2 mb-1">
            <div><p class="text-xs font-bold text-gray-700">TRAINING NAME</p></div>
            <div><p class="text-xs font-bold text-gray-700">CATEGORY</p></div>
            <div><p class="text-xs font-bold text-gray-700">INCLUSIVE DATES</p></div>
            <div><p class="text-xs font-bold text-gray-700">FACILITATOR</p></div>
            <div><p class="text-xs font-bold text-gray-700">HOURS</p></div>
            <div><p class="text-xs font-bold text-gray-700">SPONSOR</p></div>
            <div><p class="text-xs font-bold text-gray-700">REMARKS</p></div>
            <div><p class="text-xs font-bold text-gray-700">CERTIFICATE</p></div>
        </div>
        
        <!-- Data Rows -->
        <?php foreach ($trainings as $tr): ?>
            <div class="grid grid-cols-1 md:grid-cols-8 gap-2 mb-2 p-2 bg-gray-50 rounded">
                <div><p class="text-xs text-gray-800"><?= esc($tr['training_name'] ?? '-') ?></p></div>
                <div><p class="text-xs text-gray-800"><?= esc($tr['training_category_name'] ?? '-') ?></p></div>
                <div><p class="text-xs text-gray-800">
                    <?php 
                    $from = !empty($tr['date_from']) ? date('F d, Y', strtotime($tr['date_from'])) : '-';
                    $to = !empty($tr['date_to']) ? date('F d, Y', strtotime($tr['date_to'])) : '-';
                    echo $from . ' - ' . $to;
                    ?>
                </p></div>
                <div><p class="text-xs text-gray-800"><?= esc($tr['training_facilitator'] ?? '-') ?></p></div>
                <div><p class="text-xs text-gray-800"><?= esc($tr['training_hours'] ?? '-') ?></p></div>
                <div><p class="text-xs text-gray-800"><?= esc($tr['training_sponsor'] ?? '-') ?></p></div>
                <div><p class="text-xs text-gray-800"><?= esc($tr['training_remarks'] ?? '-') ?></p></div>
                <div>
                    <button type="button" 
                            class="view-training-certificate-btn inline-flex items-center text-blue-600 text-xs hover:text-blue-800"
                            data-file="<?= !empty($tr['certificate_file']) ? site_url('applications/viewTrainingCertificate/'.$app['id_job_application'].'/'.$tr['certificate_file']) : '' ?>">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Certificate
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
        
 <?php else: ?>
    <div class="bg-gray-50 rounded p-2">
        <p class="text-xs text-gray-600">
            No trainings records found.
        </p>
    </div>
<?php endif; ?>

</div>

<!-- Green Divider Line -->
<div class="border-t-2 border-clsuGreen my-3"></div>

<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-clsuGreen" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
        </svg>
        <h2 class="text-sm font-bold text-clsuGreen">Uploaded Documents</h2>
    </div>
    
    <?php
    // List of document fields in the database
    $docs = [
        'pds' => 'Fully accomplished Personal Data Sheet (PDS) with recent passport-sized picture (CS Form No. 212, Revised 2017)',
        'performance_rating' => 'Latest Performance Rating in the Present Position (Most Recent Rating Period)',
        'resume' => 'Updated Resume / Curriculum Vitae',
        'tor' => 'Official Transcript of Records (TOR) Issued by the School',
        'diploma' => 'Copy of Diploma or Proof of Graduation'
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
        <!-- Labels Row (shown only once at the top) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-1">
            <div><p class="text-xs font-bold text-gray-700">DOCUMENT</p></div>
            <div><p class="text-xs font-bold text-gray-700">UPLOADS</p></div>
        </div>
        
        <!-- Data Rows -->
        <?php foreach ($docs as $key => $label): 
            $file = $app['documents'][$key] ?? null;
            if (!empty($file)):
        ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2 p-2 bg-gray-50 rounded">
                <div>
                    <p class="text-xs text-gray-800"><?= ucfirst(str_replace('_', ' ', $key)) ?></p>
                    <p class="text-xs text-gray-600 mt-1"><?= esc($label) ?></p>
                </div>
                <div>
                    <button type="button" 
                            class="view-document-btn inline-flex items-center text-blue-600 text-xs hover:text-blue-800"
                            data-file="<?= site_url('file/viewDocument/'.$app['id_job_application'].'/'.$key) ?>">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Document
                    </button>
                </div>
            </div>
        <?php 
            endif;
        endforeach; ?>
        
 <?php else: ?>
    <div class="bg-gray-50 rounded p-2">
        <p class="text-xs text-gray-600">
            No documents records found.
        </p>
    </div>
<?php endif; ?>

</div>

<!-- Edit Prompt Section - Yellow/Orange Style -->
<?php if (($app['application_status'] ?? '') === 'Submitted'): ?>
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-6 rounded">
    <div class="flex items-start">
        <svg class="w-4 h-4 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
        </svg>
        <div class="flex-1">
            <h3 class="text-sm font-semibold text-yellow-800 mb-1">Need to Edit Your Documents?</h3>
            <p class="text-yellow-700 text-xs mb-2">You can edit your application files now while your application is in Submitted status.</p>
            <button type="button" 
                    onclick="openEditModal(<?= esc($app['id_job_application']) ?>)"
                    class="inline-flex items-center px-3 py-1.5 bg-clsuGreen text-white text-xs font-medium rounded-lg hover:bg-green-800 transition-colors">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Documents Now
            </button>
        </div>
    </div>
</div>
<?php endif; ?>
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

<!-- Edit Files Modal (Compact Full Version) -->
<div id="editFilesModal"
     class="fixed inset-0 flex items-center justify-center bg-black/50
            opacity-0 pointer-events-none transition-opacity duration-300 z-50">

    <div id="editFilesModalBox"
         class="bg-white rounded-lg w-11/12 max-w-xl p-2
                transform scale-95 opacity-0 transition-all duration-300">

        <!-- Header -->
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-sm font-semibold text-clsuGreen">
                Edit File Attachments
            </h3>
            <button onclick="closeEditModal()"
                    class="text-gray-500 hover:text-gray-700 text-base">✕</button>
        </div>

        <!-- Notice -->
        <div class="mb-2 p-1.5 bg-blue-50 rounded text-[11px] text-blue-800">
            <strong>Note:</strong> PDF only. Max 5 MB.
        </div>

        <form id="editFilesForm"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-2 text-xs">

            <input type="hidden" name="job_application_id" id="editAppId">

            <div class="border border-gray-200 rounded-md overflow-hidden">
                <table class="w-full border-collapse text-[11px]">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 py-1 border text-left font-medium">Files</th>
                            <th class="px-2 py-1 border text-left font-medium">Upload</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!-- 1. PDS -->
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-2 py-1 border">
                                <div class="font-medium">1. Personal Data Sheet (PDS)</div>
                                <div id="pds-view-link" class="mt-0.5"></div>
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="file" name="pds" accept=".pdf"
                                       id="pds-upload" data-max-size="5242880"
                                       class="text-[11px]">
                            </td>
                        </tr>

                        <!-- 2. Performance Rating -->
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-2 py-1 border">
                                <div class="font-medium">2. Performance Rating</div>
                                <div id="performance-rating-view-link" class="mt-0.5"></div>
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="file" name="performance_rating" accept=".pdf"
                                       id="performance-rating-upload" data-max-size="5242880"
                                       class="text-[11px]">
                            </td>
                        </tr>

                        <!-- 3. Resume -->
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-2 py-1 border">
                                <div class="font-medium">3. Resume</div>
                                <div id="resume-view-link" class="mt-0.5"></div>
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="file" name="resume" accept=".pdf"
                                       id="resume-upload" data-max-size="5242880"
                                       class="text-[11px]">
                            </td>
                        </tr>

                        <!-- 4. TOR -->
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-2 py-1 border">
                                <div class="font-medium">4. Transcript of Records</div>
                                <div id="tor-view-link" class="mt-0.5"></div>
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="file" name="tor" accept=".pdf"
                                       id="tor-upload" data-max-size="5242880"
                                       class="text-[11px]">
                            </td>
                        </tr>

                        <!-- 5. Diploma -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-1 border">
                                <div class="font-medium">5. Diploma</div>
                                <div id="diploma-view-link" class="mt-0.5"></div>
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="file" name="diploma" accept=".pdf"
                                       id="diploma-upload" data-max-size="5242880"
                                       class="text-[11px]">
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-2 mt-2 pt-2 border-t border-gray-200">
                <button type="button"
                        onclick="closeEditModal()"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700
                               px-2 py-1 rounded text-[11px] font-medium transition">
                    Cancel
                </button>

                <button type="submit"
                        class="bg-clsuGreen hover:bg-green-800 text-white
                               px-2 py-1 rounded text-[11px] font-medium transition">
                    Update
                </button>
            </div>

        </form>
    </div>
</div>


<div id="jobModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white max-w-3xl w-full rounded-lg p-6 max-h-[90vh] overflow-y-auto">
        <button onclick="closeModal()" class="float-right text-gray-500 text-xl">✕</button>
        <div id="modalContent"></div>
    </div>
</div>

<script>
// Edit Modal Functions
function openEditModal(applicationId) {
    const modal = document.getElementById('editFilesModal');
    const box = document.getElementById('editFilesModalBox');
    
    // Set form action
    document.getElementById('editAppId').value = applicationId;
    document.getElementById('editFilesForm').action = '<?= base_url('applications/updateFiles') ?>';
    
    // Reset file inputs and filenames
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.value = '';
    });
    document.querySelectorAll('[id$="-filename"]').forEach(el => {
        el.textContent = 'No file chosen';
    });
    document.querySelectorAll('[id$="-view-link"]').forEach(el => {
        el.innerHTML = '';
    });
    document.querySelectorAll('[id$="-current"]').forEach(el => {
        el.textContent = 'No file uploaded';
        el.className = 'text-gray-600 text-sm';
    });
    
    // Fetch existing files
    fetch('<?= base_url('applications/getFiles/') ?>' + applicationId)
        .then(res => res.json())
        .then(data => {
            // Populate current files and view links
            if (data.pds) {
                const fileName = data.pds.split('/').pop();
                document.getElementById('pds-current').textContent = fileName;
                document.getElementById('pds-current').className = 'text-green-700 text-sm';
                document.getElementById('pds-view-link').innerHTML = 
                    `<a href="${data.pds}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm underline">View File</a>`;
            }
            if (data.performance_rating) {
                const fileName = data.performance_rating.split('/').pop();
                document.getElementById('performance-rating-current').textContent = fileName;
                document.getElementById('performance-rating-current').className = 'text-green-700 text-sm';
                document.getElementById('performance-rating-view-link').innerHTML = 
                    `<a href="${data.performance_rating}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm underline">View File</a>`;
            }
            if (data.resume) {
                const fileName = data.resume.split('/').pop();
                document.getElementById('resume-current').textContent = fileName;
                document.getElementById('resume-current').className = 'text-green-700 text-sm';
                document.getElementById('resume-view-link').innerHTML = 
                    `<a href="${data.resume}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm underline">View File</a>`;
            }
            if (data.tor) {
                const fileName = data.tor.split('/').pop();
                document.getElementById('tor-current').textContent = fileName;
                document.getElementById('tor-current').className = 'text-green-700 text-sm';
                document.getElementById('tor-view-link').innerHTML = 
                    `<a href="${data.tor}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm underline">View File</a>`;
            }
            if (data.diploma) {
                const fileName = data.diploma.split('/').pop();
                document.getElementById('diploma-current').textContent = fileName;
                document.getElementById('diploma-current').className = 'text-green-700 text-sm';
                document.getElementById('diploma-view-link').innerHTML = 
                    `<a href="${data.diploma}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm underline">View File</a>`;
            }
            
            // Open modal with animation
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');
            
            setTimeout(() => {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 50);
        })
        .catch(err => {
            console.error('Error fetching files:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load application files.',
                confirmButtonColor: '#0B6B3A'
            });
        });
}

function closeEditModal() {
    const modal = document.getElementById('editFilesModal');
    const box = document.getElementById('editFilesModalBox');
    
    box.classList.remove('scale-100', 'opacity-100');
    box.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.classList.remove('opacity-100');
    }, 200);
}

// Handle file input changes
document.addEventListener('change', function(e) {
    if (e.target.type === 'file') {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
        const fileId = e.target.id.replace('-upload', '');
        document.getElementById(fileId + '-filename').textContent = fileName;
        
        // File size validation
        if (e.target.files[0] && e.target.files[0].size > 5242880) { // 5MB
            Swal.fire({
                icon: 'warning',
                title: 'File Too Large',
                text: 'File size must not exceed 5 MB.',
                confirmButtonColor: '#0B6B3A'
            });
            e.target.value = '';
            document.getElementById(fileId + '-filename').textContent = 'No file chosen';
        }
    }
});

// Close modal when clicking outside
document.getElementById('editFilesModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Close with Escape key
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('editFilesModal');
    if (e.key === 'Escape' && !modal.classList.contains('opacity-0')) {
        closeEditModal();
    }
});
</script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Modals
    const certModal = document.getElementById('certificate-modal');
    const certIframe = document.getElementById('certificate-frame');

    const trainingModal = document.getElementById('training-certificate-modal');
    const trainingIframe = document.getElementById('training-certificate-frame');

    const documentModal = document.getElementById('document-modal');
    const documentIframe = document.getElementById('document-frame');

    // Handle all view buttons
    document.addEventListener('click', async (e) => {
        const civilBtn = e.target.closest('.view-certificate-btn');
        const trainingBtn = e.target.closest('.view-training-certificate-btn');
        const documentBtn = e.target.closest('.view-document-btn');

        if (!civilBtn && !trainingBtn && !documentBtn) return;

        e.preventDefault();
        e.stopPropagation();

        const btn = civilBtn || trainingBtn || documentBtn;
        const fileUrl = btn.dataset.file?.trim();

        // Show loading first
        Swal.fire({
            title: 'Loading...',
            text: 'Please wait while the file loads.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            // Short delay to simulate loading
            await new Promise(resolve => setTimeout(resolve, 500));

            // If no file URL, still show loading briefly then show warning
            if (!fileUrl) {
                Swal.close();

                let title = 'No File Available';
                let message = 'No file has been uploaded for this document.';

                // Specific message for training certificates
                if (trainingBtn) {
                    message = 'No training certificate has been uploaded for this record.';
                } else if (civilBtn) {
                    message = 'No civil service certificate has been uploaded for this record.';
                }

                Swal.fire({
                    icon: 'warning',
                    title: title,
                    text: message,
                    showConfirmButton: false,
                    timer: 1500
                });
                return;
            }

            const res = await fetch(fileUrl);
            const contentType = res.headers.get('content-type') || '';

            // If JSON returned → file missing
            if (contentType.includes('application/json')) {
                const data = await res.json();
                Swal.close();

                let title = 'No File Available';
                let message = 'No file has been uploaded for this document.';

                if (trainingBtn) {
                    message = 'No training certificate has been uploaded for this record.';
                } else if (civilBtn) {
                    message = 'No civil service certificate has been uploaded for this record.';
                }

                Swal.fire({
                    icon: 'warning',
                    title: title,
                    text: data.message || message,
                    showConfirmButton: false,
                    timer: 1500
                });
                return;
            }

            // File exists → show modal
            Swal.close();
            if (trainingBtn) {
                trainingIframe.src = fileUrl;
                trainingModal.classList.remove('hidden');
            } else if (documentBtn) {
                documentIframe.src = fileUrl;
                documentModal.classList.remove('hidden');
            } else if (civilBtn) {
                certIframe.src = fileUrl;
                certModal.classList.remove('hidden');
            }

        } catch (err) {
            Swal.close();

            let title = 'No File Available';
            let message = 'No file has been uploaded for this document.';

            if (trainingBtn) {
                message = 'No training certificate has been uploaded for this record.';
            } else if (civilBtn) {
                message = 'No civil service certificate has been uploaded for this record.';
            }

            Swal.fire({
                icon: 'warning',
                title: title,
                text: message,
                showConfirmButton: false,
                timer: 1500
            });
            console.error(err);
        }
    });

    // Close modals when clicking outside
    [certModal, trainingModal, documentModal].forEach(modal => {
        const iframe = modal.querySelector('iframe');
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                iframe.src = '';
                modal.classList.add('hidden');
            }
        });
    });

    // Close modals with Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            [certModal, trainingModal, documentModal].forEach(modal => {
                const iframe = modal.querySelector('iframe');
                if (!modal.classList.contains('hidden')) {
                    iframe.src = '';
                    modal.classList.add('hidden');
                }
            });
        }
    });
});
</script>


<script>
document.getElementById('editFilesForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {

            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message,
                confirmButtonColor: '#0B6B3A'
            }).then(() => {
                window.location.reload();
            });

        } else {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#0B6B3A'
            });

        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong.',
            confirmButtonColor: '#0B6B3A'
        });
    });
});
</script>

</body>
</html>
