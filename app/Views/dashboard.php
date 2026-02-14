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
    // Toggle between hidden and block display
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
    } else {
        dropdown.classList.add('hidden');
    }
}
// Close dropdown when clicking outside
window.onclick = function(event) {
    const dropdown = document.getElementById('accountDropdown');
    const accountMenu = document.querySelector('.account-menu');
    if (!accountMenu.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
}
</script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- NAVBAR -->
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
                <div id="accountDropdown" class="absolute right-0 mt-2 hidden bg-white text-black min-w-[220px] rounded-lg shadow-xl z-50 border border-gray-100 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            <?= esc($user['first_name'] ?? '') ?> 
                            <?= !empty($user['middle_name'] ?? $profile['middle_name'] ?? '') ? esc(substr($user['middle_name'] ?? $profile['middle_name'],0,1)).'. ' : '' ?>
                            <?= esc($user['last_name'] ?? $profile['last_name'] ?? '') ?>
                            <?= esc($user['extension'] ?? $profile['suffix'] ?? '') ?>
                        </p>
                        <p class="text-xs text-gray-500 truncate mt-1">
                            <?= esc($user['email'] ?? $profile['email'] ?? 'noemail@example.com') ?>
                        </p>
                    </div>
                    <div class="py-1">
                        <a href="<?= site_url('account/changePassword') ?>" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-clsuGreen transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Change Password
                        </a>
                    </div>
                    <div class="border-t border-gray-100 py-1">
                        <a href="<?= site_url('logout') ?>" class="flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </a>
                    </div>
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
        <?= esc($user['first_name'] ?? '') ?>
        <?= !empty($user['middle_name'] ?? $profile['middle_name'] ?? '') ? esc(substr($user['middle_name'] ?? $profile['middle_name'],0,1)).'. ' : '' ?>
        <?= esc($user['last_name'] ?? $profile['last_name'] ?? '') ?>
        <?= esc($user['extension'] ?? $profile['suffix'] ?? '') ?>
    </h3>
    <p class="text-gray-700 mb-1"><?= esc($user['email'] ?? $profile['email'] ?? 'noemail@example.com') ?></p>
</div>

<div class="right w-full flex-1 space-y-2">
<div class="card bg-white p-4 rounded-lg shadow">
    <h3 class="text-clsuGreen font-bold mb-3 text-sm">My Job Applications</h3>
    <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse text-xs">
            <thead class="bg-gray-50 text-xs">
                <tr>
                    <th class="border-b p-2 text-left font-semibold">No.</th>
                    <th class="border-b p-2 text-left font-semibold">Position</th>
                    <th class="border-b p-2 text-left font-semibold">Office</th>
                    <th class="border-b p-2 text-left font-semibold">Date Applied</th>
                    <th class="border-b p-2 text-left font-semibold">Interview</th>
                    <th class="border-b p-2 text-left font-semibold">Status</th>
                    <th class="border-b p-2 text-left font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($applications)): ?>
                    <tr>
                        <td colspan="7" class="p-3 text-gray-500 text-center italic text-xs">No applications found</td>
                    </tr>
                <?php else: $i = 1; foreach($applications as $app): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-2 border-b"><?= $i++ ?></td>
                        <td class="p-2 border-b font-medium"><?= esc($app['position_title']) ?></td>
                        <td class="p-2 border-b text-gray-600"><?= esc($app['department']) ?></td>
                        <td class="p-2 border-b"><?= !empty($app['applied_at']) ? date('M d, Y', strtotime($app['applied_at'])) : '-' ?></td>
                        <td class="p-2 border-b"><?= '-' ?></td>
                        <td class="p-2 border-b">
                            <?php
                                $status = $app['application_status'] ?? 'Submitted';
                                $displayText = ($status === 'Submitted. For Evaluation') ? 'Submitted' : $status;
                                
                                $statusClasses = [
                                    'Submitted' => 'bg-yellow-100 text-yellow-800',
                                    'Under Evaluation' => 'bg-blue-100 text-blue-800',
                                    'Not qualified' => 'bg-red-100 text-red-800',
                                    'Shortlisted' => 'bg-blue-100 text-blue-800',
                                    'Scheduled for Interview' => 'bg-purple-100 text-purple-800',
                                    'Withdrawn application' => 'bg-gray-100 text-gray-800',
                                    'Did not attend interview' => 'bg-red-100 text-red-800',
                                    'Interviewed. Awaiting Result' => 'bg-yellow-100 text-yellow-800',
                                    'Not selected' => 'bg-red-100 text-red-800',
                                    'Job offered' => 'bg-green-100 text-green-800',
                                    'Rejected job offer' => 'bg-red-100 text-red-800',
                                    'ACCEPTED' => 'bg-green-100 text-green-800',
                                ];
                                
                                $badgeClass = $statusClasses[$displayText] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 rounded-full text-xs font-medium <?= $badgeClass ?>">
                                <?= esc($displayText) ?>
                            </span>
                        </td>
                        <td class="p-2 border-b">
                            <div class="flex gap-1">
                                <a href="<?= base_url('applications/view/' . $app['id_job_application']) ?>" 
                                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                
                                <a href="#" 
                                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors withdraw-btn"
                                   data-id="<?= $app['id_job_application'] ?>">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Withdraw
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card bg-white p-6 rounded-lg">
    <h3 class="text-clsuGreen font-bold mb-4">Available Job Vacancies</h3>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
        <p class="text-gray-700 font-semibold mb-2">
            <?= count($vacancies) ?> Vacant Positions
        </p>
        <input type="text" placeholder="Search jobs..." class="border rounded-full px-4 py-2 text-sm w-full md:w-64 focus:ring-2 focus:ring-clsuGreen outline-none">
    </div>
    <div class="space-y-4"> <!-- small gap between cards -->
    <?php foreach($vacancies as $vac): ?>
    <div class="border rounded-xl p-4 hover:shadow-sm transition job-card" 
         data-title="<?= strtolower($vac['position_title']) ?>"> <!-- removed data-type -->
        
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-2">
    <div>
        <h4 class="text-base font-semibold text-gray-900">
            <?= esc($vac['position_title'] ?? 'No Position Title') ?> 
        </h4>
        <p class="text-sm text-gray-500 mt-1">
            <?= esc($vac['office'] ?? 'No Office') ?>
            <?php if(!empty($vac['posted_at']) && $vac['posted_at'] != '0000-00-00 00:00:00'): ?>
                • Posted on <?= date('M d, Y', strtotime($vac['posted_at'])) ?>
            <?php endif; ?>
        </p>
    </div>
    <div class="text-right text-clsuGreen font-semibold text-base">
        <?= isset($vac['monthly_salary']) ? '₱' . number_format($vac['monthly_salary'], 2) : '₱0.00' ?>
    </div>
</div>


        <p class="text-sm text-gray-700 mt-2">
            <?= esc($vac['description']) ?> 
            <button onclick="openModal(<?= $vac['id'] ?>)" class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                See more
            </button>
        </p>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mt-3">
            <p class="text-sm text-red-600 font-semibold">
                Deadline: <?= date('F j, Y', strtotime($vac['application_deadline'])) ?>
            </p>


   <?php 
// Define inactive statuses
$inactiveStatuses = [
    'Not qualified',
    'Withdrawn application',
    'Did not attend interview',
    'Not selected.',
    'Rejected job offer.'
];
$applied = false;
$appStatus = null;

foreach ($applications as $app) {
    if ($app['job_vacancy_id'] == $vac['id']) {

        // Normalize status
        $status = trim($app['application_status']);
        if ($status === 'Submitted. For Evaluation') {
            $status = 'Submitted';
        }

        // If status is ACTIVE, mark as applied
        if (!in_array($status, $inactiveStatuses)) {
            $applied = true;
            $appStatus = $status;
            break; // stop ONLY when active found
        }
    }
}

?>
<?php if($applied && !in_array($appStatus, $inactiveStatuses)): ?>
    <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-lg bg-yellow-400 text-black cursor-not-allowed">
        Submitted
    </span>
<?php else: ?>
    <form method="GET" action="<?= base_url('applications/apply/' . $vac['id']) ?>">
        <button class="inline-flex items-center px-3 py-1 text-xs font-medium bg-clsuGreen text-white rounded-lg hover:bg-green-800 transition-colors">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Apply
        </button>
    </form>
<?php endif; ?>

        </div>
    </div>
    <?php endforeach; ?>
</div>
    <div id="pagination" class="flex justify-center mt-6 gap-2"></div>
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

<!-- Job Details Modal -->
<div id="jobModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
    
<div id="modalCard" class="bg-white w-full max-w-5xl max-h-[90vh] rounded-lg shadow-xl flex flex-col">
    <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0">
      <div>
        <h2 id="modalTitle" class="text-lg font-bold text-clsuGreen leading-tight"></h2>
        <p id="modalOfficeText" class="text-sm text-gray-600 mt-1"></p>
      </div>
    </div>

    <!-- Content (scrollable) -->
    <div class="px-6 py-5 space-y-4 text-sm text-gray-800 overflow-y-auto flex-1">
      
      <!-- Job Overview -->
      <div class="grid grid-cols-2 gap-3">
        <div><span class="font-medium text-gray-700">Office:</span> <span id="modalOffice" class="ml-2"></span></div>
        <div><span class="font-medium text-gray-700">Salary Grade:</span> <span id="modalSalaryGrade" class="ml-2"></span></div>
        <div><span class="font-medium text-gray-700">Item No.:</span> <span id="modalItemNo" class="ml-2"></span></div>
        <div><span class="font-medium text-gray-700">Monthly Salary:</span> <span id="modalSalary" class="text-clsuGreen font-semibold ml-2"></span></div>
        <div><span class="font-medium text-gray-700">Posted:</span> <span id="modalPosted" class="ml-2"></span></div>
        <div class="text-red-600 font-semibold"><span class="font-medium text-gray-700">Deadline:</span> <span id="modalDeadline" class="ml-2"></span></div>
      </div>

      <hr class="my-4 border-gray-200">

      <!-- Job Details with Dropdowns -->
      <div class="space-y-3">
        <!-- Job Description -->
        <details class="border border-gray-200 rounded-lg bg-white">
          <summary class="cursor-pointer px-4 py-3 font-medium text-gray-800 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-sm flex items-center justify-between">
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2 text-clsuGreen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Job Description
            </div>
            <svg class="w-4 h-4 text-gray-500 transform transition-transform duration-200 group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </summary>
          <div class="px-4 py-3 text-gray-700 border-t border-gray-200 text-sm">
            <p id="modalDescription"></p>
          </div>
        </details>

        <!-- Qualification Standards -->
        <details class="border border-gray-200 rounded-lg bg-white">
          <summary class="cursor-pointer px-4 py-3 font-medium text-gray-800 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-sm flex items-center justify-between">
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2 text-clsuGreen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              Qualification Standards
            </div>
            <svg class="w-4 h-4 text-gray-500 transform transition-transform duration-200 group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </summary>
          <div class="px-4 py-3 text-gray-700 border-t border-gray-200 text-sm">
            <ul class="space-y-2">
              <li class="flex">
                <span class="font-medium text-gray-700 w-20">Education:</span>
                <span id="modalEducation" class="text-gray-900"></span>
              </li>
              <li class="flex">
                <span class="font-medium text-gray-700 w-20">Training:</span>
                <span id="modalTraining" class="text-gray-900"></span>
              </li>
              <li class="flex">
                <span class="font-medium text-gray-700 w-20">Experience:</span>
                <span id="modalExperience" class="text-gray-900"></span>
              </li>
              <li class="flex">
                <span class="font-medium text-gray-700 w-20">Eligibility:</span>
                <span id="modalEligibility" class="text-gray-900"></span>
              </li>
            </ul>
          </div>
        </details>

        <!-- Duties and Responsibilities -->
        <details class="border border-gray-200 rounded-lg bg-white">
          <summary class="cursor-pointer px-4 py-3 font-medium text-gray-800 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-sm flex items-center justify-between">
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2 text-clsuGreen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
              Duties and Responsibilities
            </div>
            <svg class="w-4 h-4 text-gray-500 transform transition-transform duration-200 group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </summary>
          <div class="px-4 py-3 text-gray-700 border-t border-gray-200 text-sm">
            <p id="modalDuties"></p>
          </div>
        </details>

        <!-- Application Requirements -->
        <details class="border border-gray-200 rounded-lg bg-white">
          <summary class="cursor-pointer px-4 py-3 font-medium text-gray-800 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-sm flex items-center justify-between">
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2 text-clsuGreen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Application Requirements
            </div>
            <svg class="w-4 h-4 text-gray-500 transform transition-transform duration-200 group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </summary>
          <div class="px-4 py-3 text-gray-700 border-t border-gray-200 text-sm">
            <p id="modalRequirements"></p>
          </div>
        </details>
      </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="border-t px-6 py-4 flex justify-end bg-gray-50 flex-shrink-0 rounded-b-lg">
        <button onclick="closeModal()" 
                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors shadow-sm hover:shadow-md">
            Cancel
        </button>
    </div>
  </div>
</div>

<script>
const jobs = <?= json_encode($vacancies) ?>;
const jobCards = Array.from(document.querySelectorAll('.job-card'));
let perPage = 5, currentPage = 1, filteredJobs = [];

// Initialize filtered jobs with all jobs
filteredJobs = [...jobCards];

function renderJobs() {
    // Get search term
    const searchValue = document.querySelector('input[placeholder="Search jobs..."]').value.toLowerCase().trim();
    
    // Filter jobs based on search
    if (searchValue) {
        filteredJobs = jobCards.filter(card => {
            const title = card.dataset.title || '';
            return title.includes(searchValue);
        });
    } else {
        filteredJobs = [...jobCards]; // Reset to all jobs
    }
    
    const totalPages = Math.ceil(filteredJobs.length / perPage);
    
    // Reset to first page if current page exceeds total pages
    if (currentPage > totalPages && totalPages > 0) {
        currentPage = 1;
    }
    
    // Handle empty state
    if (filteredJobs.length === 0) {
        // Hide all job cards
        jobCards.forEach(card => card.style.display = 'none');
        
        // Show no results message
        showNoResultsMessage(searchValue);
        hidePagination();
        return;
    }
    
    // Hide no results message if it exists
    hideNoResultsMessage();
    
    const start = (currentPage - 1) * perPage;
    const paginated = filteredJobs.slice(start, start + perPage);
    
    // Hide all cards
    jobCards.forEach(card => card.style.display = 'none');
    
    // Show paginated cards
    paginated.forEach(card => card.style.display = 'block');
    
    // Render pagination
    renderPagination(totalPages);
}

function showNoResultsMessage(searchTerm) {
    // Check if message already exists
    let noResultsDiv = document.getElementById('noResultsMessage');
    if (!noResultsDiv) {
        noResultsDiv = document.createElement('div');
        noResultsDiv.id = 'noResultsMessage';
        noResultsDiv.className = 'text-center py-12';
        document.querySelector('.space-y-4').appendChild(noResultsDiv);
    }
    
    noResultsDiv.innerHTML = `
        <div class="bg-gray-50 rounded-xl p-8 border border-gray-200 max-w-md mx-auto">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-200 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">No positions found</h3>
            <p class="text-gray-500">
                ${searchTerm ? 
                    `No job positions match for "<span class="font-medium text-gray-700">${searchTerm}</span>". Try adjusting your search terms.` :
                    'There are currently no available job positions.'
                }
            </p>
        </div>
    `;
}

function hideNoResultsMessage() {
    const noResultsDiv = document.getElementById('noResultsMessage');
    if (noResultsDiv) {
        noResultsDiv.remove();
    }
}

function hidePagination() {
    const pagination = document.getElementById('pagination');
    if (pagination) {
        pagination.innerHTML = '';
        pagination.className = 'flex justify-center mt-6 gap-2 hidden';
    }
}

function renderPagination(totalPages) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    pagination.className = 'flex justify-center mt-6 gap-2';
    
    if (totalPages <= 1) {
        pagination.className = 'flex justify-center mt-6 gap-2 hidden';
        return;
    }
    
    // Prev button
    const prevBtn = document.createElement('button');
    prevBtn.textContent = 'Prev';
    prevBtn.className = `px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ${
        currentPage === 1 
            ? 'bg-gray-200 text-gray-400 cursor-not-allowed' 
            : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 shadow-sm'
    }`;
    prevBtn.disabled = currentPage === 1;
    prevBtn.addEventListener('click', () => { 
        if(currentPage > 1){ 
            currentPage--; 
            renderJobs(); 
        } 
    });
    pagination.appendChild(prevBtn);
    
    // Page numbers (show up to 5 pages around current page)
    const maxVisible = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible/2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
    
    // Adjust start page if near the end
    if (endPage - startPage + 1 < maxVisible) {
        startPage = Math.max(1, endPage - maxVisible + 1);
    }
    
    // First page button (if not in visible range)
    if (startPage > 1) {
        const firstBtn = document.createElement('button');
        firstBtn.textContent = '1';
        firstBtn.className = 'px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 shadow-sm';
        firstBtn.addEventListener('click', () => { 
            currentPage = 1; 
            renderJobs(); 
        });
        pagination.appendChild(firstBtn);
        
        if (startPage > 2) {
            const dots = document.createElement('span');
            dots.textContent = '...';
            dots.className = 'px-2 py-2 text-gray-500';
            pagination.appendChild(dots);
        }
    }
    
    // Page number buttons
    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.textContent = i;
        pageBtn.className = `px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ${
            i === currentPage 
                ? 'bg-clsuGreen text-white shadow-md' 
                : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 shadow-sm'
        }`;
        if (i !== currentPage) {
            pageBtn.addEventListener('click', () => { 
                currentPage = i; 
                renderJobs(); 
            });
        }
        pagination.appendChild(pageBtn);
    }
    
    // Last page button (if not in visible range)
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            const dots = document.createElement('span');
            dots.textContent = '...';
            dots.className = 'px-2 py-2 text-gray-500';
            pagination.appendChild(dots);
        }
        
        const lastBtn = document.createElement('button');
        lastBtn.textContent = totalPages;
        lastBtn.className = 'px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 shadow-sm';
        lastBtn.addEventListener('click', () => { 
            currentPage = totalPages; 
            renderJobs(); 
        });
        pagination.appendChild(lastBtn);
    }
    
    // Next button
    const nextBtn = document.createElement('button');
    nextBtn.textContent = 'Next';
    nextBtn.className = `px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ${
        currentPage === totalPages 
            ? 'bg-gray-200 text-gray-400 cursor-not-allowed' 
            : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 shadow-sm'
    }`;
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.addEventListener('click', () => { 
        if(currentPage < totalPages){ 
            currentPage++; 
            renderJobs(); 
        } 
    });
    pagination.appendChild(nextBtn);
}

// Debounced search handler
let searchTimeout;
document.querySelector('input[placeholder="Search jobs..."]').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage = 1; // Reset to first page on new search
        renderJobs();
    }, 300); // 300ms debounce
});

// Also handle Enter key
document.querySelector('input[placeholder="Search jobs..."]').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        clearTimeout(searchTimeout);
        currentPage = 1;
        renderJobs();
    }
});
function openModal(id){
    const job = jobs.find(j => j.id == id);
    if(!job) return;

    // Populate data
    document.getElementById('modalTitle').textContent = job.position_title;
    document.getElementById('modalOfficeText').textContent = job.office;
    document.getElementById('modalOffice').textContent = job.office;
    document.getElementById('modalSalaryGrade').textContent = job.salary_grade;
    document.getElementById('modalItemNo').textContent = job.plantilla_item_no;
    document.getElementById('modalSalary').textContent = '₱' + parseFloat(job.monthly_salary).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
   document.getElementById('modalPosted').textContent =
    new Date(job.created_at).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });

document.getElementById('modalDeadline').textContent =
    new Date(job.application_deadline).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    document.getElementById('modalDescription').textContent = job.description;
    document.getElementById('modalEducation').textContent = job.education;
    document.getElementById('modalTraining').textContent = job.training;
    document.getElementById('modalExperience').textContent = job.experience;
    document.getElementById('modalEligibility').textContent = job.eligibility;
    document.getElementById('modalDuties').textContent = job.duties_responsibilities;
    document.getElementById('modalRequirements').textContent = job.application_requirements;

    const modal = document.getElementById('jobModal');
    const card  = document.getElementById('modalCard');

    // Show modal instantly
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Force reflow
    modal.offsetHeight;
    
    // Add smooth transition classes
    modal.classList.add('transition-all', 'duration-300', 'ease-out');
    card.classList.add('transition-all', 'duration-300', 'ease-out');
    
    // Animate card IN
    setTimeout(() => {
        card.classList.remove('opacity-0', 'scale-95');
        card.classList.add('opacity-100', 'scale-100');
    }, 10);
}

function closeModal(){
    const modal = document.getElementById('jobModal');
    const card  = document.getElementById('modalCard');

    // Animate card OUT
    card.classList.remove('opacity-100', 'scale-100');
    card.classList.add('opacity-0', 'scale-95');

    setTimeout(() => {
        modal.classList.remove('flex', 'transition-all', 'duration-300', 'ease-out');
        card.classList.remove('transition-all', 'duration-300', 'ease-out');
        modal.classList.add('hidden');
    }, 300);
}



// Initialize on page load
renderJobs();
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // --- SweetAlert if first_login = 1 ---
    <?php if(isset($user['first_login']) && $user['first_login'] == 1): ?>
    Swal.fire({
        icon: 'info',
        title: 'Action Required!',
        text: 'You need to change your password before accessing the dashboard.',
        allowOutsideClick: false,  // Cannot click outside
        allowEscapeKey: false,     // Cannot press ESC
        confirmButtonColor: '#0B6B3A',
        confirmButtonText: 'OK'
    }).then((result) => {
        if(result.isConfirmed){
            // Redirect to change_password page
            window.location.href = "<?= site_url('account/changePassword') ?>";
        }
    });
    <?php endif; ?>

    // --- Success message after updating password ---
    <?php if(session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '<?= session()->getFlashdata('success') ?>',
        confirmButtonColor: '#0B6B3A'
    }).then(() => {
        window.location.href = "<?= site_url('dashboard') ?>";
    });
    <?php endif; ?>

    // --- Error message ---
    <?php if(session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '<?= session()->getFlashdata('error') ?>',
        confirmButtonColor: '#0B6B3A'
    });
    <?php endif; ?>

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('a.withdraw-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const row = this.closest('tr');
            const statusCell = row.querySelector('td:nth-child(6) span');
            const statusText = statusCell.textContent.trim();

            // If already withdrawn, show info modal
            if(statusText === 'Withdrawn application' || statusText === 'Withdrawn'){
                Swal.fire({
                    icon: 'info',
                    title: 'Cannot Withdraw',
                    text: 'This application has already been withdrawn.',
                    confirmButtonColor: '#0B6B3A'
                });
                return;
            }

            const appId = this.dataset.id;

            Swal.fire({
                title: 'Withdraw Application?',
                text: 'Are you sure you want to withdraw this application?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0B6B3A',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then(result => {
                if(result.isConfirmed){
                    fetch(`<?= base_url('applications/withdraw') ?>/${appId}`, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success){
                            // Update status badge
                            statusCell.textContent = 'Withdrawn application';
                            statusCell.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-gray-400 text-black';

                            // Show info modal instead of disabling
                            Swal.fire({
                                icon: 'success',
                                title: 'Application Withdrawn',
                                text: 'Your application status has been updated.',
                                confirmButtonColor: '#0B6B3A'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to withdraw application.',
                                confirmButtonColor: '#0B6B3A'
                            });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred.',
                            confirmButtonColor: '#0B6B3A'
                        });
                    });
                }
            });
        });
    });

});
</script>
<!-- ================= EDIT FILES MODAL ================= -->
<div id="editFilesModal"
     class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50
            opacity-0 pointer-events-none transition-opacity duration-300 z-50">

    <div id="editFilesModalBox"
         class="bg-white rounded-xl w-11/12 max-w-4xl p-4
                transform scale-95 opacity-0 transition-all duration-300">

        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-bold text-clsuGreen">
                Edit File Attachments
            </h3>
            <button onclick="closeEditModal()" class="text-gray-500 text-lg">✕</button>
        </div>

        <form id="editFilesForm"
              method="POST"
              enctype="multipart/form-data"
              class="text-xs">

            <input type="hidden" name="job_application_id" id="editAppId">

            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse">
                    <tbody id="existingFiles">

                        <!-- JS injects rows here -->

                    </tbody>
                </table>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button"
                        onclick="closeEditModal()"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700
                               px-4 py-1 rounded text-xs font-semibold transition">
                    Cancel
                </button>

                <button type="submit"
                        class="bg-clsuGreen hover:bg-green-800 text-white
                               px-4 py-1 rounded text-xs font-semibold transition">
                    Update Files
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal() {
    const modal = document.getElementById('editFilesModal');
    const box = document.getElementById('editFilesModalBox');

    modal.classList.remove('opacity-0', 'pointer-events-none');
    modal.classList.add('opacity-100');

    setTimeout(() => {
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
    }, 50);
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
</script>



<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const row = this.closest('tr');
            const statusCell = row.querySelector('td:nth-child(6) span');
            const statusText = statusCell.textContent.trim();

            const appId = this.dataset.id;
            const fetchUrl = this.dataset.url;

            // ❌ Not editable
            if (statusText !== 'Submitted') {
                Swal.fire({
                    icon: 'info',
                    title: 'Cannot Edit',
                    text: 'You cannot edit this application right now!',
                    confirmButtonColor: '#0B6B3A'
                });
                return;
            }

            // ✅ Confirm edit
            Swal.fire({
                title: 'Edit Application?',
                text: 'Do you want to edit the attached files for this application?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0B6B3A',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit files'
            }).then(result => {
                if (!result.isConfirmed) return;

                // Set form action
                document.getElementById('editAppId').value = appId;
                document.getElementById('editFilesForm').action =
                    `<?= base_url('applications/updateFiles/') ?>${appId}`;

                // Fetch existing files
                fetch(fetchUrl)
                    .then(res => res.json())
                    .then(data => {
let html = '';

function fileRow(label, name, value, accept) {
    return `
        <tr>
            <td class="px-2 py-1 border font-semibold w-1/4">${label}</td>
            <td class="px-2 py-1 border w-3/4">
                ${value ? `
                    <p class="text-green-700 mb-1">
                        Uploaded:
                        <a href="${value}" target="_blank"
                           class="underline text-blue-600">
                           View
                        </a>
                    </p>
                ` : ''}

                <input type="file"
                       name="${name}"
                       accept="${accept}"
                       class="px-2 py-1 w-full text-xs border rounded">
            </td>
        </tr>
    `;
}

html += fileRow('Resume (PDF)', 'resume', data.resume, '.pdf');
html += fileRow('Transcript of Records (TOR)', 'tor', data.tor, '.pdf');
html += fileRow('Diploma', 'diploma', data.diploma, '.pdf');
html += fileRow('Other Certificates (Optional)', 'certificate', data.certificate, '.pdf,.jpg,.png');

document.getElementById('existingFiles').innerHTML = html;


                        document.getElementById('existingFiles').innerHTML = html;
                        openEditModal();
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load application files.',
                            confirmButtonColor: '#0B6B3A'
                        });
                    });
            });
        });
    });

});
</script>

</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Show SweetAlert if user tried to apply for already applied job
document.addEventListener('DOMContentLoaded', function() {
    <?php if(session()->getFlashdata('already_applied')): ?>
        Swal.fire({
            icon: 'info',
            title: 'Already Applied',
            text: 'You have already submitted an application for "<?= session()->getFlashdata('job_title') ?>"',
            confirmButtonColor: '#0B6B3A'
        });
    <?php endif; ?>
});
</script>
