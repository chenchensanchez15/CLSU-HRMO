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
        <?= esc($user['first_name'] ?? '') ?>
        <?= !empty($user['middle_name'] ?? $profile['middle_name'] ?? '') ? esc(substr($user['middle_name'] ?? $profile['middle_name'],0,1)).'. ' : '' ?>
        <?= esc($user['last_name'] ?? $profile['last_name'] ?? '') ?>
        <?= esc($user['extension'] ?? $profile['suffix'] ?? '') ?>
    </h3>
    <p class="text-gray-700 mb-1"><?= esc($user['email'] ?? $profile['email'] ?? 'noemail@example.com') ?></p>
</div>

<div class="right w-full flex-1 space-y-2">
<div class="card bg-white p-6 rounded-lg shadow">
    <h3 class="text-clsuGreen font-bold mb-2">My Job Applications</h3>
 <table class="w-full table-auto border-collapse text-xs"> <!-- smaller font -->
    <thead class="bg-gray-100 text-xs"> <!-- header smaller font -->
            <tr>
                <th class="border-b p-2 text-left">No.</th>
                <th class="border-b p-2 text-left">Position</th>
                <th class="border-b p-2 text-left">Office / Department</th>
                <th class="border-b p-2 text-left">Posting Date</th>
                <th class="border-b p-2 text-left">Closing Date</th>
                <th class="border-b p-2 text-left">Status</th>
                <th class="border-b p-2 text-left">Action</th>
            </tr>
        </thead>
       <tbody>
    <?php if(empty($applications)): ?>
        <tr>
            <td colspan="7" class="p-2 text-gray-500 text-center">No data available in table</td>
        </tr>
    <?php else: $i = 1; foreach($applications as $app): ?>
        <tr class="bg-white hover:bg-gray-50">
            <td class="p-2"><?= $i++ ?></td>
            <td class="p-2"><?= esc($app['position_title']) ?></td>
            <td class="p-2"><?= esc($app['department']) ?></td>
            <td class="p-2"><?= !empty($app['posting_date']) ? date('M d, Y', strtotime($app['posting_date'])) : 'N/A' ?></td>
            <td class="p-2"><?= !empty($app['closing_date']) ? date('M d, Y', strtotime($app['closing_date'])) : 'N/A' ?></td>
            <td class="p-2">
                <?php
                    $status = $app['application_status'] ?? 'Submitted. For Evaluation';

                    $statusClasses = [
                        'Submitted' => 'bg-yellow-400 text-black',
                        'Under Evaluation' => 'bg-blue-500 text-white',
                        'Not qualified' => 'bg-red-500 text-white',
                        'Shortlisted.' => 'bg-blue-300 text-black',
                        'Scheduled for Interview' => 'bg-purple-500 text-white',
                        'Withdrawn application' => 'bg-gray-400 text-black',
                        'Did not attend interview' => 'bg-red-600 text-white',
                        'Interviewed. Awaiting Result' => 'bg-yellow-300 text-black',
                        'Not selected.' => 'bg-red-400 text-white',
                        'Job offered.' => 'bg-green-500 text-white',
                        'Rejected job offer.' => 'bg-red-700 text-white',
                        'ACCEPTED.' => 'bg-green-600 text-white',
                    ];

                    $displayText = ($status === 'Submitted. For Evaluation') ? 'Submitted' : $status;
                    $badgeClass = $statusClasses[$status] ?? 'bg-gray-400 text-white';
                ?>
                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                    <?= esc($displayText) ?>
                </span>
            </td>
           <td class="p-2 whitespace-nowrap">
    <div class="flex items-center gap-1">
        <a href="<?= base_url('applications/view/' . $app['id_job_application']) ?>" 
           class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium px-2 py-1 rounded flex items-center">
            <i class="fas fa-eye mr-1"></i> View
        </a>

<a href="javascript:void(0)"
   class="bg-yellow-400 hover:bg-yellow-500 text-black text-xs font-medium px-2 py-1 rounded flex items-center edit-btn"
   data-id="<?= $app['id_job_application'] ?>"
   data-url="<?= base_url('applications/getFiles/' . $app['id_job_application']) ?>">
    <i class="fas fa-pencil-alt mr-1"></i> Edit
</a>


        <a href="#"
            class="bg-red-500 hover:bg-red-600 text-white text-xs font-medium px-2 py-1 rounded flex items-center withdraw-btn"
            data-id="<?= $app['id_job_application'] ?>">
            <i class="fas fa-times mr-1"></i> Withdraw
        </a>
    </div>
</td>

        </tr>
    <?php endforeach; endif; ?>
</tbody>
    </table>
   <div id="applicationsPagination" class="flex justify-end mt-4 gap-2"></div>
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
            <button onclick="openModal(<?= $vac['id'] ?>)" class="text-blue-600 hover:underline ml-1 text-sm">See more</button>
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
    <span class="px-3 py-1 rounded-lg bg-yellow-400 text-black text-sm font-semibold cursor-not-allowed">
        Submitted
    </span>
<?php else: ?>
    <form method="GET" action="<?= base_url('applications/apply/' . $vac['id']) ?>">
        <button class="bg-clsuGreen text-white px-4 py-1 rounded-lg text-sm hover:bg-green-800">
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
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    
<div id="modalCard" class="bg-white w-full max-w-4xl max-h-[90vh] rounded-2xl shadow-2xl flex flex-col">
    <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0">
      <div>
        <h2 id="modalTitle" class="text-xl font-bold text-clsuGreen leading-tight"></h2>
        <p id="modalOfficeText" class="text-sm text-gray-500 mt-1"></p>
      </div>
      <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-xl font-bold">✕</button>
    </div>

    <!-- Content (scrollable) -->
    <div class="px-6 py-5 space-y-4 text-sm text-gray-800 overflow-y-auto flex-1">
      
      <!-- Job Overview (2 columns) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2">
        <p><strong>Office:</strong> <span id="modalOffice"></span></p>
        <p><strong>Salary Grade:</strong> <span id="modalSalaryGrade"></span></p>
        <p><strong>Item No.:</strong> <span id="modalItemNo"></span></p>
        <p><strong>Monthly Salary:</strong> <span id="modalSalary"></span></p>
        <p><strong>Posted:</strong> <span id="modalPosted"></span></p>
        <p class="text-red-600 font-semibold"><strong>Deadline:</strong> <span id="modalDeadline"></span></p>
      </div>

      <hr class="my-3">

      <!-- Job Description -->
      <details class="border rounded">
        <summary class="cursor-pointer px-3 py-1.5 font-semibold bg-gray-100 text-sm">
          Job Description
        </summary>
        <div class="px-4 py-2 text-gray-700 max-h-60 overflow-y-auto">
          <p id="modalDescription"></p>
        </div>
      </details>

      <!-- Qualification Standards -->
      <details class="border rounded">
        <summary class="cursor-pointer px-3 py-1.5 font-semibold bg-gray-100 text-sm">
          Qualification Standards
        </summary>
        <div class="px-4 py-2 text-gray-700 max-h-60 overflow-y-auto">
          <ul class="list-disc ml-4 space-y-0.5">
            <li><strong>Education:</strong> <span id="modalEducation"></span></li>
            <li><strong>Training:</strong> <span id="modalTraining"></span></li>
            <li><strong>Experience:</strong> <span id="modalExperience"></span></li>
            <li><strong>Eligibility:</strong> <span id="modalEligibility"></span></li>
          </ul>
        </div>
      </details>

      <!-- Duties -->
      <details class="border rounded">
        <summary class="cursor-pointer px-3 py-1.5 font-semibold bg-gray-100 text-sm">
          Duties and Responsibilities
        </summary>
        <div class="px-4 py-2 text-gray-700 max-h-60 overflow-y-auto">
          <p id="modalDuties"></p>
        </div>
      </details>

      <!-- Requirements -->
      <details class="border rounded">
        <summary class="cursor-pointer px-3 py-1.5 font-semibold bg-gray-100 text-sm">
          Application Requirements
        </summary>
        <div class="px-4 py-2 text-gray-700 max-h-60 overflow-y-auto">
          <p id="modalRequirements"></p>
        </div>
      </details>
    </div>
    <div class="border-t px-6 py-4 flex justify-between bg-gray-50 flex-shrink-0 rounded-b-2xl">
    </div>
  </div>
</div>

<script>
const jobs = <?= json_encode($vacancies) ?>;
const jobCards = Array.from(document.querySelectorAll('.job-card'));
let perPage = 5, currentPage = 1, filteredType = 'all';
function renderJobs() {
    let filteredJobs = jobCards; // no filter, show all jobs

    const searchValue = document.querySelector('input[placeholder="Search jobs..."]').value.toLowerCase();
    filteredJobs = filteredJobs.filter(card => card.dataset.title.includes(searchValue));

    const totalPages = Math.ceil(filteredJobs.length / perPage);
    if(currentPage > totalPages) currentPage = totalPages || 1; // handle empty

    const start = (currentPage - 1) * perPage;
    const paginated = filteredJobs.slice(start, start + perPage);

    // Hide all cards
    jobCards.forEach(card => card.style.display = 'none');
    // Show paginated cards
    paginated.forEach(card => card.style.display = 'block');

    // Render pagination
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    pagination.className = 'flex justify-end mt-6 gap-1';

    // Prev button
    const prevBtn = document.createElement('button');
    prevBtn.textContent = 'Prev';
    prevBtn.className = `px-3 py-1 rounded text-sm ${currentPage === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-100 hover:bg-gray-200'}`;
    prevBtn.disabled = currentPage === 1;
    prevBtn.addEventListener('click', () => { if(currentPage > 1){ currentPage--; renderJobs(); } });
    pagination.appendChild(prevBtn);

    // Current page (single number)
    const currentBtn = document.createElement('button');
    currentBtn.textContent = currentPage;
    currentBtn.className = 'px-3 py-1 rounded bg-clsuGreen text-white text-sm';
    pagination.appendChild(currentBtn);

    // Next button
    const nextBtn = document.createElement('button');
    nextBtn.textContent = 'Next';
    nextBtn.className = `px-3 py-1 rounded text-sm ${currentPage === totalPages ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-100 hover:bg-gray-200'}`;
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.addEventListener('click', () => { if(currentPage < totalPages){ currentPage++; renderJobs(); } });
    pagination.appendChild(nextBtn);
}


document.querySelector('input[placeholder="Search jobs..."]').addEventListener('keyup',()=>renderJobs());
function openModal(id){
    const job = jobs.find(j => j.id == id);
    if(!job) return;

    // Populate data
    document.getElementById('modalTitle').textContent = job.position_title;
    document.getElementById('modalOfficeText').textContent = job.office;
    document.getElementById('modalOffice').textContent = job.office;
    document.getElementById('modalSalaryGrade').textContent = job.salary_grade;
    document.getElementById('modalItemNo').textContent = job.plantilla_item_no;
    document.getElementById('modalSalary').textContent = job.monthly_salary;
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

    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');

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
    card.classList.add('opacity-0', 'scale-95');
    card.classList.remove('opacity-100', 'scale-100');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 200);
}

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
