<?php
// --- Backend: Prepare Vacancies ---
// We'll handle filtering & pagination on the client side via JS, so no need for PHP filtering
?>

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

<div class="container flex flex-col lg:flex-row gap-6 p-6 max-w-6xl mx-auto flex-1">

<!-- LEFT PANEL -->
<div class="left bg-white p-6 rounded-lg text-center shadow-md self-start">
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

<!-- RIGHT PANEL -->
<div class="right w-full lg:w-2/3 space-y-6">
<!-- My Job Applications -->
<div class="card bg-white p-6 rounded-lg shadow">
    <h3 class="text-clsuGreen font-bold mb-4">My Job Applications</h3>
    <table class="w-full table-auto border-collapse text-sm">
        <thead class="bg-gray-100">
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
                <tr>
                    <td class="p-2"><?= $i++ ?></td>
                    <td class="p-2"><?= esc($app['position_title']) ?></td>
                    <td class="p-2"><?= esc($app['department']) ?></td>
                    <td class="p-2"><?= date('M d, Y', strtotime($app['posting_date'])) ?></td>
                    <td class="p-2"><?= date('M d, Y', strtotime($app['closing_date'])) ?></td>
                    <td class="p-2"><?= esc($app['application_status']) ?></td>
                    <td class="p-2">
                        <a href="<?= base_url('applications/view/' . $app['id']) ?>" class="text-blue-600 hover:underline text-xs">View</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>



<!-- Available Job Vacancies -->
<div class="card bg-white p-6 rounded-lg">
    <h3 class="text-clsuGreen font-bold mb-4">Available Job Vacancies</h3>

    <!-- FILTER + SEARCH -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex gap-2">
            <?php $filters = ['All','Contractual','Permanent']; foreach($filters as $f): ?>
                <button class="filter-btn px-4 py-2 rounded-full text-sm" data-type="<?= strtolower($f) ?>"><?= $f ?></button>
            <?php endforeach; ?>
        </div>
        <input type="text" placeholder="Search jobs..." class="border rounded-full px-4 py-2 text-sm w-full md:w-64 focus:ring-2 focus:ring-clsuGreen outline-none">
    </div>

<!-- JOB CARDS -->
<div class="space-y-6">
    <?php foreach($vacancies as $vac): ?>
    <div class="border rounded-xl p-5 hover:shadow-md transition job-card" 
         data-title="<?= strtolower($vac['position_title']) ?>" 
         data-type="<?= strtolower($vac['employment_type']) ?>">
        
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            <div>
                <h4 class="text-lg font-bold text-gray-900">
                    <?= esc($vac['position_title']) ?> 
                    <span class="text-sm font-normal text-gray-500"><?= esc($vac['employment_type']) ?></span>
                </h4>
                <p class="text-sm text-gray-500 mt-1">
                    <?= esc($vac['office']) ?> • Posted on <?= date('M d, Y', strtotime($vac['created_at'])) ?>
                </p>
            </div>
            <div class="text-right text-clsuGreen font-semibold text-lg">
                <?= esc($vac['monthly_salary']) ?>
            </div>
        </div>

        <p class="text-sm text-gray-700 mt-4">
            <?= esc($vac['description']) ?> 
            <button onclick="openModal(<?= $vac['id'] ?>)" class="text-blue-600 hover:underline ml-1">See more</button>
        </p>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mt-6">
            <p class="text-sm text-red-600 font-semibold">
                Deadline: <?= date('F j, Y', strtotime($vac['application_deadline'])) ?>
            </p>

         <form method="GET" action="<?= base_url('applications/apply/' . $vac['item_no']) ?>">
    <button class="bg-clsuGreen text-white px-5 py-2 rounded-lg text-sm hover:bg-green-800">
        Apply
    </button>
</form>

        </div>
    </div>
    <?php endforeach; ?>
</div>

    <!-- PAGINATION -->
    <div id="pagination" class="flex justify-center mt-6 gap-2"></div>
</div>

</div></div>

<!-- UPDATED FOOTER -->
<footer class="flex-shrink-0 w-full bg-gray-100 py-4 border-t mt-auto">
    <div class="flex justify-end px-6 text-xs text-gray-600">
        <div class="text-right">
            &copy; <?= date('Y') ?> CLSU-HRMO. All rights reserved.<br>
            Powered by <span class="text-green-700">Management Information System Office (CLSU-MISO)</span>
        </div>
    </div>
</footer>
<!-- JOB MODAL -->
<div id="jobModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white max-w-3xl w-full rounded-lg p-6 max-h-[90vh] overflow-y-auto">
        <button onclick="closeModal()" class="float-right text-gray-500 text-xl">✕</button>
        <div id="modalContent"></div>
    </div>
</div>

<script>
const jobs = <?= json_encode($vacancies) ?>;
const jobCards = Array.from(document.querySelectorAll('.job-card'));
let perPage = 5, currentPage = 1, filteredType = 'all';

function renderJobs() {
    // Filter
    let filteredJobs = jobCards.filter(card => filteredType==='all'||card.dataset.type===filteredType);

    // Search filter
    const searchValue = document.querySelector('input[placeholder="Search jobs..."]').value.toLowerCase();
    filteredJobs = filteredJobs.filter(card=>card.dataset.title.includes(searchValue));

    // Pagination
    const totalPages = Math.ceil(filteredJobs.length/perPage);
    const start = (currentPage-1)*perPage;
    const paginated = filteredJobs.slice(start,start+perPage);

    jobCards.forEach(card=>card.style.display='none');
    paginated.forEach(card=>card.style.display='block');

    // Pagination buttons
    const pagination = document.getElementById('pagination');
    pagination.innerHTML='';
    for(let i=1;i<=totalPages;i++){
        const btn=document.createElement('button');
        btn.textContent=i;
        btn.className=`px-3 py-1 rounded ${i===currentPage?'bg-clsuGreen text-white':'bg-gray-100 hover:bg-gray-200'}`;
        btn.addEventListener('click',()=>{ currentPage=i; renderJobs(); });
        pagination.appendChild(btn);
    }
}

// Filter buttons
document.querySelectorAll('.filter-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{
        filteredType = btn.dataset.type;
        currentPage=1;
        // highlight active button
        document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('bg-clsuGreen','text-white'));
        btn.classList.add('bg-clsuGreen','text-white');
        renderJobs();
    });
});

// Search input
document.querySelector('input[placeholder="Search jobs..."]').addEventListener('keyup',()=>renderJobs());

// Modal functions
function openModal(id){
    const job = jobs.find(j=>j.id==id);
    if(!job) return;
    document.getElementById('modalContent').innerHTML=`
        <h2 class="text-2xl font-bold text-clsuGreen mb-2">${job.position_title}</h2>
        <p><strong>Office:</strong> ${job.office}</p>
        <p><strong>Department:</strong> ${job.department}</p>
        <p><strong>Employment Type:</strong> ${job.employment_type}</p>
        <p><strong>Salary:</strong> ${job.monthly_salary}</p>
        <hr class="my-4">
        <p><strong>Education:</strong> ${job.education}</p>
        <p><strong>Training:</strong> ${job.training}</p>
        <p><strong>Experience:</strong> ${job.experience}</p>
        <p><strong>Eligibility:</strong> ${job.eligibility}</p>
        <hr class="my-4">
        <p><strong>Duties & Responsibilities:</strong><br>${job.duties_responsibilities}</p>
        <hr class="my-4">
        <p><strong>Application Requirements:</strong><br>${job.application_requirements}</p>
    `;
    document.getElementById('jobModal').classList.remove('hidden');
    document.getElementById('jobModal').classList.add('flex');
}

function closeModal(){ document.getElementById('jobModal').classList.add('hidden'); }

// Initial render
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


</body>
</html>
