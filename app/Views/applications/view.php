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
<h2 class="text-lg font-semibold text-clsuGreen mb-2">Personal Information</h2>

<div class="overflow-x-auto mb-6">
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
        <td class="px-2 py-1 border"><?= esc($app['personal']['first_name'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($app['personal']['middle_name'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($app['personal']['last_name'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($app['personal']['extension'] ?? '-') ?></td>
      </tr>
    </tbody>
  </table>
</div>
<div class="overflow-x-auto mb-6">
  <table class="table-auto w-full border-collapse text-xs">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-2 py-1 border">Sex</th>
        <th class="px-2 py-1 border">Date of Birth</th>
        <th class="px-2 py-1 border">Civil Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="px-2 py-1 border"><?= esc($app['personal']['sex'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($app['personal']['date_of_birth_formatted'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($app['personal']['civil_status'] ?? '-') ?></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="overflow-x-auto mb-6">
  <table class="table-auto w-full border-collapse text-xs">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-2 py-1 border">Email</th>
        <th class="px-2 py-1 border">Phone Number</th>
        <th class="px-2 py-1 border">Citizenship</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="px-2 py-1 border"><?= esc($app['personal']['email'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($app['personal']['phone'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($app['personal']['citizenship'] ?? '-') ?></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="overflow-x-auto mb-6">
  <table class="table-auto w-full border-collapse text-xs">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-2 py-1 border">Residential Address</th>
        <th class="px-2 py-1 border">Permanent Address</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="px-2 py-1 border"><?= esc($app['personal']['residential_address'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($app['personal']['permanent_address'] ?? '-') ?></td>
      </tr>
    </tbody>
  </table>
</div>
<h2 class="text-lg font-semibold text-clsuGreen mb-2">Family Background</h2>
<div class="overflow-x-auto mb-6">
  <table class="table-auto w-full border-collapse text-xs">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-2 py-1 border">Relationship</th>
        <th class="px-2 py-1 border">Full Name</th>
        <th class="px-2 py-1 border">Occupation</th>
        <th class="px-2 py-1 border">Contact Number</th>
      </tr>
    </thead>
    <tbody>
      <?php
$relations = ['Spouse', 'Father', 'Mother'];
$family_by_relation = [];
if (!empty($app['family'])) {
    foreach ($app['family'] as $fam) {
        $family_by_relation[$fam['relationship']] = $fam;
    }
}

foreach ($relations as $relation):
    $fam = $family_by_relation[$relation] ?? [];

    $first = $fam['first_name'] ?? '';
    $middle = $fam['middle_name'] ?? '';
    $last = $fam['last_name'] ?? '';
    $suffix = $fam['extension'] ?? '';

    // Convert middle name to initial if not empty or N/A
    if (!empty($middle) && strtoupper($middle) !== 'N/A') {
        $middle = strtoupper(substr($middle, 0, 1)) . '.';
    } else {
        $middle = '';
    }

    // Ignore suffix if empty or N/A
    if (empty($suffix) || strtoupper($suffix) === 'N/A') {
        $suffix = '';
    }

    // Capitalize first letters of first, middle, last
    $first = ucfirst(strtolower($first));
    $last  = ucfirst(strtolower($last));
    // Middle is already capitalized as initial (G.)
    // Suffix: keep as is (Jr., III)
    
    $nameParts = array_filter([$first, $middle, $last, $suffix]);
    $fullName = $nameParts ? implode(' ', $nameParts) : '-';

    $occupation = !empty($fam['occupation']) && strtoupper($fam['occupation']) !== 'N/A' ? ucfirst(strtolower($fam['occupation'])) : '-';
    $contact    = !empty($fam['contact_no']) && strtoupper($fam['contact_no']) !== 'N/A' ? $fam['contact_no'] : '-';
?>
<tr>
    <td class="px-2 py-1 border"><?= esc($relation) ?></td>
    <td class="px-2 py-1 border"><?= esc($fullName) ?></td>
    <td class="px-2 py-1 border"><?= esc($occupation) ?></td>
    <td class="px-2 py-1 border"><?= esc($contact) ?></td>
</tr>
<?php endforeach; ?>
    </tbody>
  </table>
</div>
<h2 class="text-lg font-semibold text-clsuGreen mb-2">Educational Background</h2>
<div class="overflow-x-auto mb-6">
  <table class="table-auto w-full border-collapse text-xs">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-2 py-1 border">Level</th>
        <th class="px-2 py-1 border">School Name</th>
        <th class="px-2 py-1 border">Period of Attendance</th>
        <th class="px-2 py-1 border">Highest Level / Units Earned</th>
        <th class="px-2 py-1 border">Year Graduated</th>
        <th class="px-2 py-1 border">Awards / Honors</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($app['education'])): ?>
          <?php foreach ($app['education'] as $edu): ?>
      <tr>
        <td class="px-2 py-1 border"><?= esc($edu['level'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($edu['school_name'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($edu['period'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($edu['highest_level_units'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($edu['year_graduated'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($edu['awards'] ?? '-') ?></td>
      </tr>
          <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td class="px-2 py-1 border text-center" colspan="6">No education records found.</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- WORK EXPERIENCE -->
<h2 class="text-lg font-semibold text-clsuGreen mb-2">Work Experience</h2>
<div class="overflow-x-auto mb-6">
  <table class="table-auto w-full border-collapse text-xs">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-2 py-1 border">Position / Title</th>
        <th class="px-2 py-1 border">Office</th>
        <th class="px-2 py-1 border">Inclusive Dates</th>
        <th class="px-2 py-1 border">Status of Appointment</th>
        <th class="px-2 py-1 border">Government Service</th>
      </tr>
    </thead>
    <tbody>
  <?php if (!empty($app['work'])): ?>
    <?php foreach ($app['work'] as $work): ?>
      <tr>
        <td class="px-2 py-1 border"><?= !empty($work['position_title']) ? esc($work['position_title']) : '-' ?></td>
        <td class="px-2 py-1 border"><?= !empty($work['office']) ? esc($work['office']) : '-' ?></td>
        <td class="px-2 py-1 border">
            <?php 
              $from = !empty($work['date_from']) ? date('F d, Y', strtotime($work['date_from'])) : '-';
              $to   = !empty($work['date_to']) ? date('F d, Y', strtotime($work['date_to'])) : '-';
              echo $from . ' - ' . $to;
            ?>
        </td>
        <td class="px-2 py-1 border"><?= !empty($work['status_of_appointment']) ? esc($work['status_of_appointment']) : '-' ?></td>
        <td class="px-2 py-1 border">
            <?= (isset($work['govt_service']) && strtoupper($work['govt_service']) === 'YES') ? 'Yes' : 'No' ?>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td class="px-2 py-1 border text-center" colspan="5">No work experience records found.</td>
    </tr>
  <?php endif; ?>
</tbody>
  </table>
</div>
<!-- CIVIL SERVICE -->
<h2 class="text-lg font-semibold text-clsuGreen mb-2">Civil Service Eligibility</h2>
<div class="overflow-x-auto mb-6">
  <table class="table-auto w-full border-collapse text-xs">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-2 py-1 border">Eligibility</th>
        <th class="px-2 py-1 border">Rating / Exam</th>
        <th class="px-2 py-1 border">Date of Examination</th>
        <th class="px-2 py-1 border">Place of Examination</th>
        <th class="px-2 py-1 border">License / PRC No.</th>
        <th class="px-2 py-1 border">Valid Until</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($app['civil'])): ?>
        <?php foreach ($app['civil'] as $cs): ?>
      <tr>
        <td class="px-2 py-1 border"><?= esc($cs['eligibility'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($cs['rating'] ?? '-') ?></td>
        <td class="px-2 py-1 border">
          <?= !empty($cs['date_of_exam']) && $cs['date_of_exam'] !== '-' ? date('F d, Y', strtotime($cs['date_of_exam'])) : '-' ?>
        </td>
        <td class="px-2 py-1 border"><?= esc($cs['place_of_exam'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($cs['license_no'] ?? '-') ?></td>
        <td class="px-2 py-1 border">
          <?= !empty($cs['license_valid_until']) && $cs['license_valid_until'] !== '-' ? date('F d, Y', strtotime($cs['license_valid_until'])) : '-' ?>
        </td>
      </tr>
        <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td class="px-2 py-1 border text-center" colspan="6">No civil service records found.</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- TRAININGS -->
<h2 class="text-lg font-semibold text-clsuGreen mb-2">Trainings / Seminars / Workshops</h2>
<div class="overflow-x-auto mb-6">
  <table class="table-auto w-full border-collapse text-xs">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-2 py-1 border">Training Name</th>
        <th class="px-2 py-1 border">Category</th>
        <th class="px-2 py-1 border">Inclusive Dates</th>
        <th class="px-2 py-1 border">Facilitator</th>
        <th class="px-2 py-1 border">Hours</th>
        <th class="px-2 py-1 border">Sponsor</th>
        <th class="px-2 py-1 border">Remarks</th>
        <th class="px-2 py-1 border">Certificate</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($trainings)): ?>
        <?php foreach ($trainings as $tr): ?>
      <tr>
        <td class="px-2 py-1 border"><?= esc($tr['training_name'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($tr['training_category_name'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= $tr['date_from'] ?> - <?= $tr['date_to'] ?></td>
        <td class="px-2 py-1 border"><?= esc($tr['training_facilitator'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($tr['training_hours'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($tr['training_sponsor'] ?? '-') ?></td>
        <td class="px-2 py-1 border"><?= esc($tr['training_remarks'] ?? '-') ?></td>
        <td class="px-2 py-1 border">
          <?php if (!empty($tr['certificate_file'])): ?>
       <a href="<?= site_url('applications/viewTrainingCertificate/'.$app['id_job_application'].'/'.$tr['certificate_file']) ?>" target="_blank" class="text-blue-600 hover:underline">
    <?= esc($tr['certificate_file']) ?>
</a>

          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
        <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td class="px-2 py-1 border text-center" colspan="8">No trainings/seminars records found.</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- DOCUMENTS -->
<h2 class="text-lg font-semibold text-clsuGreen mb-2">Uploaded Documents</h2>
<div class="overflow-x-auto mb-6">
  <table class="table-auto w-full border-collapse text-xs">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-2 py-1 border">Document</th>
        <th class="px-2 py-1 border">File</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // List of document fields in the database
      $docs = ['resume', 'tor', 'diploma', 'certificate'];

      // Loop through each document type
      foreach ($docs as $doc):
          $file = $app['documents'][$doc] ?? null;
      ?>
      <tr>
        <td class="px-2 py-1 border font-semibold"><?= ucfirst($doc) ?></td>
        <td class="px-2 py-1 border">
          <?php if (!empty($file)): ?>
         <a href="<?= site_url('applications/viewDocument/'.$app['id_job_application'].'/'.$doc) ?>"
   target="_blank"
   class="text-blue-600 hover:underline">

              <?= esc($file) ?>
            </a>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>

      <?php if (empty(array_filter($app['documents'] ?? []))): ?>
      <tr>
        <td class="px-2 py-1 border text-center font-semibold" colspan="2">No documents uploaded.</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
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

<div id="jobModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white max-w-3xl w-full rounded-lg p-6 max-h-[90vh] overflow-y-auto">
        <button onclick="closeModal()" class="float-right text-gray-500 text-xl">✕</button>
        <div id="modalContent"></div>
    </div>
</div>

</body>
</html>
