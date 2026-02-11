<?php
// Connect to database
$db = \Config\Database::connect();

// Fetch only posted jobs
$builder = $db->table('job_vacancies');
$builder->where('is_posted', 1);
$jobs = $builder->get()->getResultArray();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLSU Online Job Application</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<body class="flex flex-col min-h-screen bg-gray-100 text-gray-800">

<header class="bg-clsuGreen text-white py-4 px-4">
    <div class="max-w-5xl mx-auto flex items-center justify-center">
        <div class="flex items-center gap-3 text-center md:text-left">
            <img src="/HRMO/public/assets/images/clsu-logo2.png" alt="CLSU Logo" class="w-16 h-auto"/>
            <div>
                <h1 class="text-3xl font-bold mb-1">CLSU Online Job Application</h1>
                <p class="text-base max-w-2xl">
                    Explore career opportunities and apply online with ease and security.
                </p>
            </div>
        </div>
    </div>
</header>
<main class="flex-1 w-full px-6 py-10">
    <h2 class="text-2xl md:text-3xl font-bold text-center mb-6">Available Job Vacancies</h2>

    <?php if (!empty($jobs)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
            <?php foreach ($jobs as $job): ?>
                <div class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col shadow-sm hover:shadow-lg transition duration-200 w-full">

                    <!-- Job Title -->
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-2 hover:text-clsuGreen transition">
                        <?= esc($job['position_title']) ?>
                    </h3>

                    <!-- Job Description -->
                    <p class="text-xs text-gray-700 mb-3 leading-relaxed">
                        <?= esc($job['description']) ?>
                    </p>

                    <!-- Job Info -->
                    <div class="text-xs text-gray-600 mb-5 space-y-1">
                        <div class="flex gap-2">
                            <span class="font-medium text-gray-800">Office:</span>
                            <span class="text-gray-700"><?= esc($job['office']) ?></span>
                        </div>
                        <div class="flex gap-2">
                            <span class="font-medium text-gray-800">Item No:</span>
                            <span class="text-gray-700"><?= esc($job['plantilla_item_no']) ?></span>
                        </div>
                        <div class="flex gap-2">
                            <span class="font-medium text-gray-800">Salary Grade:</span>
                            <span class="text-gray-700"><?= esc($job['salary_grade']) ?></span>
                        </div>
                        <div class="flex gap-2">
                            <span class="font-medium text-gray-800">Monthly Salary:</span>
                            <span class="text-gray-700">₱<?= number_format($job['monthly_salary'], 2) ?></span>
                        </div>
                        <div class="flex gap-2">
                            <span class="font-medium text-gray-800">Deadline:</span>
                            <span class="text-red-600 font-semibold"><?= date('F j, Y', strtotime($job['application_deadline'])) ?></span>
                        </div>
                    </div>

                    <!-- View Details Button -->
                    <div class="mt-auto flex justify-end">
                        <button onclick="openJobModal(<?= $job['id'] ?>)"
                           class="inline-block bg-clsuGreen text-white px-3 py-1.5 rounded text-xs hover:bg-green-800 transition">
                            View Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-500 text-sm mt-10">
            No job vacancies available at the moment.
        </p>
    <?php endif; ?>
</main>


<!-- Job Details Modal -->
<div id="jobModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
    <div id="modalCard" class="bg-white w-full max-w-5xl max-h-[90vh] rounded-lg shadow-xl flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0">
            <div>
                <h2 id="modalJobTitle" class="text-lg font-bold text-clsuGreen leading-tight"></h2>
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
        <div class="border-t px-6 py-4 flex justify-end bg-gray-50 flex-shrink-0 rounded-b-lg gap-2">
            <button onclick="closeJobModal()" 
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors shadow-sm hover:shadow-md">
                Cancel
            </button>
            <button onclick="applyForJobModal()" 
                    class="px-5 py-2.5 text-sm font-medium text-white bg-clsuGreen hover:bg-green-800 rounded-lg transition-colors shadow-sm hover:shadow-md">
                Apply Now
            </button>
        </div>
    </div>
</div>


<footer class="w-full bg-gray-100 py-4 mt-auto border-t">
    <div class="flex justify-end px-6 text-xs text-gray-600">
        <div class="text-right">
            &copy; <?= date('Y') ?> CLSU-HRMO. All rights reserved.<br>
            Powered by <span class="text-green-700">Management Information System Office (CLSU-MISO)</span>
        </div>
    </div>
</footer>

</body>
</html>

<script>
// Store jobs data globally for modal access
let jobsData = [];

// Open job modal with smooth animation
function openJobModal(jobId) {
    // Find job in our data
    const job = jobsData.find(j => j.id == jobId);
    if (!job) {
        console.error('Job not found:', jobId);
        return;
    }

    // Populate modal data
    document.getElementById('modalJobTitle').textContent = job.position_title;
    document.getElementById('modalJobTitle').dataset.jobId = job.id;  // Store job ID
    document.getElementById('modalOfficeText').textContent = job.office;
    document.getElementById('modalOffice').textContent = job.office;
    document.getElementById('modalSalaryGrade').textContent = job.salary_grade;
    document.getElementById('modalItemNo').textContent = job.plantilla_item_no;
    document.getElementById('modalSalary').textContent = '₱' + parseFloat(job.monthly_salary).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    document.getElementById('modalPosted').textContent = job.created_at 
        ? new Date(job.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
        : 'N/A';
        
    document.getElementById('modalDeadline').textContent = new Date(job.application_deadline).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    
    document.getElementById('modalDescription').textContent = job.description || 'N/A';
    document.getElementById('modalEducation').textContent = job.education || 'N/A';
    document.getElementById('modalTraining').textContent = job.training || 'N/A';
    document.getElementById('modalExperience').textContent = job.experience || 'N/A';
    document.getElementById('modalEligibility').textContent = job.eligibility || 'N/A';
    document.getElementById('modalDuties').textContent = job.duties_responsibilities || 'N/A';
    document.getElementById('modalRequirements').textContent = job.application_requirements || 'N/A';

    const modal = document.getElementById('jobModal');
    const card = document.getElementById('modalCard');

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

// Close job modal with smooth animation
function closeJobModal() {
    const modal = document.getElementById('jobModal');
    const card = document.getElementById('modalCard');

    // Animate card OUT
    card.classList.remove('opacity-100', 'scale-100');
    card.classList.add('opacity-0', 'scale-95');

    setTimeout(() => {
        modal.classList.remove('flex', 'transition-all', 'duration-300', 'ease-out');
        card.classList.remove('transition-all', 'duration-300', 'ease-out');
        modal.classList.add('hidden');
    }, 300);
}

// Apply for job function (smart logic)
function applyForJobModal() {
    const jobId = document.getElementById('modalJobTitle').dataset.jobId;
    
    // Check if user is logged in (by checking if session data exists)
    const isLoggedIn = <?= session()->get('logged_in') ? 'true' : 'false' ?>;
    
    if (!isLoggedIn) {
        // Show login required alert for logged out users
        Swal.fire({
            icon: 'warning',
            title: 'Login Required',
            text: 'You need to log in first before applying for this job.',
            confirmButtonColor: '#0B6B3A',
            confirmButtonText: 'Go to Login'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to apply page - the controller will handle the redirect to login
                window.location.href = `<?= base_url('applications/apply/') ?>${jobId}`;
            }
        });
    } else {
        // User is logged in - redirect directly to apply
        window.location.href = `<?= base_url('applications/apply/') ?>${jobId}`;
    }
}

// Close modal when clicking outside
document.getElementById('jobModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeJobModal();
    }
});

// Load jobs data on page load
document.addEventListener('DOMContentLoaded', function() {
    // Fetch jobs data for modal use
    fetch('<?= base_url('jobs/getAllPosted') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                jobsData = data.jobs;
            }
        })
        .catch(error => {
            console.error('Error loading jobs data:', error);
        });
});
</script>
