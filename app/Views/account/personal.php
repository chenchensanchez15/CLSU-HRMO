<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Applicant Dashboard | CLSU HRMO</title>
<script src="https://cdn.tailwindcss.com"></script>
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

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
                <a href="<?= site_url('dashboard') ?>" class="hover:underline">Home</a>
        <a href="<?= site_url('account/personal') ?>"
   class="<?= service('uri')->getSegment(1) === 'account' && service('uri')->getSegment(2) === 'personal'
        ? 'text-clsuGold border-b-2 border-clsuGold pb-0.5'
        : 'hover:underline' ?>">
    Profile
</a>
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
<div class="profile-pic relative w-32 h-32 mx-auto rounded-full bg-gray-200 overflow-visible flex items-center justify-center mb-4">
    <?php
    $photoPath = FCPATH . 'uploads/' . ($profile['photo'] ?? '');
    if (!empty($profile['photo']) && file_exists($photoPath)): ?>
        <img id="profilePhoto" src="<?= base_url('uploads/' . esc($profile['photo'])) ?>" class="w-full h-full object-cover rounded-full">
    <?php else: ?>
        <svg id="profilePhoto" xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M12 2a5 5 0 100 10 5 5 0 000-10zm-7 18a7 7 0 0114 0H5z" clip-rule="evenodd"/>
        </svg>
    <?php endif; ?>

    <button id="editPhotoBtn" class="absolute bottom-0 right-0 translate-x-1/4 translate-y-1/4 bg-clsuGold rounded-full p-1 shadow-md hover:bg-yellow-400 z-10" title="Change Photo">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l5 5L21 9l-5-5-7 7z" />
        </svg>
    </button>

    <input type="file" id="photoInput" name="photo" class="hidden" accept="image/*">
</div>

    <h3 class="text-clsuGreen font-bold mb-1">
        <?= esc($user['first_name'] ?? '') ?>
        <?= !empty($user['middle_name'] ?? $profile['middle_name'] ?? '') ? esc(substr($user['middle_name'] ?? $profile['middle_name'],0,1)).'. ' : '' ?>
        <?= esc($user['last_name'] ?? $profile['last_name'] ?? '') ?>
        <?= esc($user['extension'] ?? $profile['suffix'] ?? '') ?>
    </h3>
    <p class="text-gray-700 mb-1"><?= esc($user['email'] ?? $profile['email'] ?? 'noemail@example.com') ?></p>
</div>

<!-- Right Panel -->
<div class="right w-full flex-1 space-y-6">
    <!-- Inside Right Panel, replace current Personal Information section -->
<div class="right w-full flex-1 space-y-6">

 <!-- TABS -->
<div class="bg-white shadow rounded-lg p-5 text-gray-700 text-sm">
  <div class="flex flex-wrap border-b text-sm font-semibold mb-4">
    <button class="tab-btn px-4 py-2 text-clsuGreen border-b-2 border-clsuGreen" data-tab="personal">Personal</button>
    <button class="tab-btn px-4 py-2 text-gray-600 hover:text-clsuGreen" data-tab="education">Education</button>
    <button class="tab-btn px-4 py-2 text-gray-600 hover:text-clsuGreen" data-tab="work">Work Experience</button>
    <button class="tab-btn px-4 py-2 text-gray-600 hover:text-clsuGreen" data-tab="civil">Civil Service</button>
    <button class="tab-btn px-4 py-2 text-gray-600 hover:text-clsuGreen" data-tab="training">Trainings</button>
    <button class="tab-btn px-4 py-2 text-gray-600 hover:text-clsuGreen" data-tab="files">Files</button>
  </div>

  <!-- TAB CONTENTS -->
  <div class="tab-content" id="tab-personal">

    <!-- === Personal Information Tables === -->
    <h2 class="text-xl font-bold text-clsuGreen mb-2">Personal Information</h2>

    <!-- Name Table -->
    <div class="overflow-x-auto mb-4">
      <table class="table-auto w-full text-left border-collapse text-xs" id="table-name">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-2 py-1 border">First Name</th>
            <th class="px-2 py-1 border">Middle Name</th>
            <th class="px-2 py-1 border">Last Name</th>
            <th class="px-2 py-1 border">Suffix</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="px-2 py-1 border" data-key="first_name"><?= esc($profile['first_name'] ?? '-') ?></td>
            <td class="px-2 py-1 border" data-key="middle_name"><?= esc($profile['middle_name'] ?? '-') ?></td>
            <td class="px-2 py-1 border" data-key="last_name"><?= esc($profile['last_name'] ?? '-') ?></td>
            <td class="px-2 py-1 border" data-key="suffix"><?= esc($profile['suffix'] ?? '-') ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Basic Info Table -->
    <div class="overflow-x-auto mb-4">
      <table class="table-auto w-full text-left border-collapse text-xs" id="table-basic">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-2 py-1 border">Sex</th>
            <th class="px-2 py-1 border">Date of Birth</th>
            <th class="px-2 py-1 border">Civil Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="px-2 py-1 border" data-key="sex"><?= esc($profile['sex'] ?? '-') ?></td>
           <td class="px-2 py-1 border" data-key="date_of_birth">
    <?= isset($profile['date_of_birth']) && $profile['date_of_birth'] != '' 
        ? date('F j, Y', strtotime($profile['date_of_birth'])) 
        : '-' ?>
</td>
 <td class="px-2 py-1 border" data-key="civil_status"><?= esc($profile['civil_status'] ?? '-') ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Contact Table -->
    <div class="overflow-x-auto mb-4">
      <table class="table-auto w-full text-left border-collapse text-xs" id="table-contact">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-2 py-1 border">Email</th>
            <th class="px-2 py-1 border">Phone Number</th>
            <th class="px-2 py-1 border">Citizenship</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="px-2 py-1 border" data-key="email"><?= esc($profile['email'] ?? '-') ?></td>
            <td class="px-2 py-1 border" data-key="phone"><?= esc($profile['phone'] ?? '-') ?></td>
            <td class="px-2 py-1 border" data-key="citizenship"><?= esc($profile['citizenship'] ?? '-') ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Addresses Table -->
    <div class="overflow-x-auto mb-4">
      <table class="table-auto w-full text-left border-collapse text-xs" id="table-addresses">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-2 py-1 border">Residential Address</th>
            <th class="px-2 py-1 border" colspan="2">Permanent Address</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="px-2 py-1 border" data-key="residential_address"><?= esc($profile['residential_address'] ?? '-') ?></td>
            <td class="px-2 py-1 border" colspan="2" data-key="permanent_address"><?= esc($profile['permanent_address'] ?? '-') ?></td>
          </tr>
        </tbody>
      </table>
    </div>
<!-- Family Background -->
<h2 class="text-xl font-bold text-clsuGreen mb-2 mt-6">Family Background</h2>

<?php $relations = ['Spouse', 'Father', 'Mother']; ?>
<?php foreach ($relations as $rel): ?>
  <?php $key = strtolower($rel); ?>
  <div class="mb-4">
    <p class="font-semibold text-xs mb-1"><?= $rel ?><?php if($rel==='Mother') echo ' (Maiden Name)'; ?></p>

    <!-- Name Table -->
    <div class="overflow-x-auto mb-2">
      <table class="table-auto w-full text-left border-collapse text-xs family-table" data-relation="<?= $key ?>">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-2 py-1 border">First Name</th>
            <th class="px-2 py-1 border">Middle Name</th>
            <th class="px-2 py-1 border">Last Name</th>
            <?php if($rel !== 'Mother'): ?><th class="px-2 py-1 border">Extension</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="px-2 py-1 border" data-key="<?= $key ?>_first_name"><?= esc($familyProfile[$key]['first_name'] ?? '') ?></td>
            <td class="px-2 py-1 border" data-key="<?= $key ?>_middle_name"><?= esc($familyProfile[$key]['middle_name'] ?? '') ?></td>
            <td class="px-2 py-1 border" data-key="<?= $key ?>_last_name"><?= esc($familyProfile[$key]['last_name'] ?? '') ?></td>
            <?php if($rel !== 'Mother'): ?>
              <td class="px-2 py-1 border" data-key="<?= $key ?>_extension"><?= esc($familyProfile[$key]['extension'] ?? '') ?></td>
            <?php endif; ?>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Contact Number & Occupation Table -->
    <div class="overflow-x-auto">
      <table class="table-auto w-full text-left border-collapse text-xs family-table" data-relation="<?= $key ?>">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-2 py-1 border">Contact No.</th>
            <th class="px-2 py-1 border">Occupation</th>
          </tr>
        </thead>
        <tbody>
          <tr>
        <td class="px-2 py-1 border" data-key="<?= $key ?>_contact_no" data-value="<?= esc($familyProfile[$key]['contact_no'] ?? '') ?>">
    <?= esc($familyProfile[$key]['contact_no'] ?? '-') ?>
</td>

            <td class="px-2 py-1 border" data-key="<?= $key ?>_occupation">
              <input type="text" value="<?= esc($familyProfile[$key]['occupation'] ?? '') ?>" class="w-full text-xs px-1">
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
<?php endforeach; ?>

<script>
// Contact number validation: numbers only, max 11 digits
document.querySelectorAll('.contact-number').forEach(input => {
  input.addEventListener('input', e => {
    let value = e.target.value.replace(/\D/g,''); // Remove non-digit chars
    if(value.length > 11) value = value.slice(0,11);
    e.target.value = value;
  });
});
</script>

    <!-- Buttons -->
    <div class="flex justify-end -mt-1 gap-2">
      <button id="cancelBtn" class="bg-gray-400 px-4 py-1.5 rounded text-xs font-semibold hidden">Cancel</button>
      <button id="saveBtn" class="bg-clsuGreen px-4 py-1.5 rounded text-xs font-semibold hidden">Save</button>
      <button id="editBtn" class="bg-clsuGold px-4 py-1.5 rounded text-xs font-semibold">Edit Profile</button>
    </div>
  </div>

<!-- === EDUCATION TAB === -->
<div class="tab-content hidden" id="tab-education">
    <h2 class="text-lg font-bold text-clsuGreen mb-3">Educational Background</h2>

    <div class="overflow-x-auto mb-5">
        <table class="table-auto w-full text-left border-collapse text-xs" id="table-education">
            <thead>
                <tr class="bg-gray-100">
                    <th rowspan="2" class="px-2 py-2 border w-20">Level</th>
                    <th rowspan="2" class="px-2 py-2 border w-40">Name of School<br>(Write in full)</th>
                    <th rowspan="2" class="px-2 py-2 border w-40">Degree / Course<br>(Write in full)</th>
                    <th colspan="2" class="px-2 py-2 border text-center w-28">Period of Attendance</th>
                    <th rowspan="2" class="px-2 py-2 border w-28">Highest Level /<br>Units Earned</th>
                    <th rowspan="2" class="px-2 py-2 border w-20">Year Graduated</th>
                    <th rowspan="2" class="px-2 py-2 border w-36">Scholarship / Academic<br>Honors Received</th>
                </tr>
                <tr class="bg-gray-100">
                    <th class="px-2 py-2 border w-14">From</th>
                    <th class="px-2 py-2 border w-14">To</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $levels = ['Elementary','Secondary','Vocational/Trade','College','Graduate Studies'];

                foreach($levels as $level):
                    $edu = null;
                    if(!empty($educationRecords)){
                        foreach($educationRecords as $record){
                            if($record['level'] === $level){
                                $edu = $record;
                                break;
                            }
                        }
                    }

                    if(!$edu){
                        $edu = [
                            'school_name' => '-',
                            'degree_course' => '-',
                            'period_from' => '-',
                            'period_to' => '-',
                            'highest_level_units' => '-',
                            'year_graduated' => '-',
                            'awards' => '-'
                        ];
                    }
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-2 py-2 border font-semibold"><?= esc($level) ?></td>

                    <td class="px-2 py-2 border" data-key="school_name"><?= esc($edu['school_name']) ?></td>
                    <td class="px-2 py-2 border" data-key="degree_course"><?= esc($edu['degree_course']) ?></td>
                    <td class="px-2 py-2 border" data-key="period_from"><?= esc($edu['period_from']) ?></td>
                    <td class="px-2 py-2 border" data-key="period_to"><?= esc($edu['period_to']) ?></td>
                    <td class="px-2 py-2 border" data-key="highest_level_units"><?= esc($edu['highest_level_units']) ?></td>
                    <td class="px-2 py-2 border" data-key="year_graduated"><?= esc($edu['year_graduated']) ?></td>
                    <td class="px-2 py-2 border" data-key="awards"><?= esc($edu['awards']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="flex justify-end gap-2 -mt-1">
        <button id="cancelEducationBtn" class="bg-gray-400 px-4 py-2 rounded text-xs font-semibold hidden">Cancel</button>
        <button id="saveEducationBtn" class="bg-clsuGreen px-4 py-2 rounded text-xs font-semibold hidden">Save</button>
        <button id="editEducationBtn" class="bg-clsuGold px-4 py-2 rounded text-xs font-semibold">Edit Education</button>
    </div>
</div>

<!-- === WORK EXPERIENCE TAB === -->
<div class="tab-content hidden" id="tab-work">
    <h2 class="text-lg font-bold text-clsuGreen mb-2">Work Experience</h2>

    <div class="overflow-x-auto mb-4">
        <table class="table-auto w-full text-left border-collapse text-xs" id="table-work">
            <thead>
                <tr class="bg-gray-100">
                    <th rowspan="2" class="px-1 py-1 border w-24">Position Title</th>
                    <th rowspan="2" class="px-1 py-1 border w-28">Office / Company</th>
                    <th colspan="2" class="px-1 py-1 border text-center w-28">Inclusive Dates</th>
                    <th rowspan="2" class="px-1 py-1 border w-28">Status of Appointment</th>
                    <th rowspan="2" class="px-1 py-1 border w-20">Government Service</th>
                    <th rowspan="2" class="px-1 py-1 border w-20">Actions</th>
                </tr>
                <tr class="bg-gray-100">
                    <th class="px-1 py-1 border w-14">From</th>
                    <th class="px-1 py-1 border w-14">To</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($workRecords)): ?>
                    <?php foreach($workRecords as $work): ?>
                  <?php
// Text fields only
foreach (['position_title','office','status_of_appointment','govt_service'] as $key) {
    if (empty($work[$key])) {
        $work[$key] = '-';
    }
}

// Safe date handling (NO 1970) in "Month day, Year" format
$work['date_from'] = (!empty($work['date_from']) && $work['date_from'] !== '0000-00-00')
    ? date('F j, Y', strtotime($work['date_from']))
    : '-';

$work['date_to'] = (!empty($work['date_to']) && $work['date_to'] !== '0000-00-00')
    ? date('F j, Y', strtotime($work['date_to']))
    : '-';
?>


<tr data-id="<?= esc($work['id']) ?>">
    <td class="px-1 py-1 border" data-key="position_title"><?= esc($work['position_title']) ?></td>
    <td class="px-1 py-1 border" data-key="office"><?= esc($work['office']) ?></td>
    <td class="px-1 py-1 border" data-key="date_from"><?= esc($work['date_from']) ?></td>
    <td class="px-1 py-1 border" data-key="date_to"><?= esc($work['date_to']) ?></td>
    <td class="px-1 py-1 border" data-key="status_of_appointment"><?= esc($work['status_of_appointment']) ?></td>
    <td class="px-1 py-1 border" data-key="govt_service"><?= !empty($work['govt_service']) ? esc($work['govt_service']) : '-' ?></td>
    <td class="px-1 py-1 border text-center">
        <button class="editWorkBtn text-blue-600 px-1"><i class="fa-solid fa-pen-to-square"></i></button>
        <button class="deleteWorkBtn text-red-600 px-1"><i class="fa-solid fa-trash"></i></button>
    </td>
</tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="px-1 py-1 border text-center" colspan="7">No work experience found for this applicant.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="flex justify-end gap-2 -mt-1" id="workButtons">
        <button id="addWorkBtn" class="bg-clsuGold px-3 py-1 rounded text-xs font-semibold">Add Work Experience</button>
    </div>
</div>

<!-- Modal Overlay -->
<div id="editWorkModal"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 z-50">
    <!-- Modal Box -->
    <div
        class="bg-white rounded-2xl w-11/12 max-w-md p-6 transform scale-95 opacity-0 transition-all duration-300">
        <h3 class="text-lg font-bold text-clsuGreen mb-4">Edit Work Experience</h3>
  <form id="editWorkForm" class="space-y-3">
    <div>
        <label class="text-xs font-semibold">Position Title</label>
        <input type="text" name="position_title" class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter position title" required>
    </div>
    <div>
        <label class="text-xs font-semibold">Office / Company</label>
        <input type="text" name="office" class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter office or company" required>
    </div>
    <div class="flex gap-2">
        <div class="flex-1">
            <label class="text-xs font-semibold">From</label>
            <input type="date" name="date_from" class="w-full border px-2 py-1 rounded text-xs" required>
        </div>
        <div class="flex-1">
            <label class="text-xs font-semibold">To</label>
            <input type="date" name="date_to" class="w-full border px-2 py-1 rounded text-xs" required>
        </div>
    </div>
    <div>
        <label class="text-xs font-semibold">Status of Appointment</label>
        <input type="text" name="status_of_appointment" class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter status" required>
    </div>
    <div>
        <label class="text-xs font-semibold">Government Service</label>
        <select name="govt_service" class="w-full border px-2 py-1 rounded text-xs" required>
            <option value="" disabled selected>Select Yes or No</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select>
    </div>
    <div class="flex justify-end gap-2 mt-3">
        <button type="button" id="cancelEditModal" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition-colors">
            Cancel
        </button>
        <button type="submit" class="bg-clsuGreen hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition-colors">Save</button>
    </div>
</form>

    </div>
</div>

<div class="tab-content hidden" id="tab-civil">
    <h2 class="text-lg font-bold text-clsuGreen mb-2">Civil Service Eligibility</h2>
    <div class="overflow-x-auto mb-4">
        <table class="table-auto w-full text-left border-collapse text-xs" id="table-civil">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-1 py-1 border w-28">Eligibility</th>
                    <th class="px-1 py-1 border w-20">Rating / Exam</th>
                    <th class="px-1 py-1 border w-20">Date of Examination</th>
                    <th class="px-1 py-1 border w-24">Place of Examination</th>
                    <th class="px-1 py-1 border w-20">License / PRC No.</th>
                    <th class="px-1 py-1 border w-20">Valid Until</th>
                    <th class="px-1 py-1 border w-20">Actions</th>
                </tr>
            </thead>
            <tbody>
               <?php if(!empty($civilRecords)): ?>
    <?php foreach($civilRecords as $civil): ?>
        <?php
        // Format dates safely
        $civil['date_of_exam'] = (!empty($civil['date_of_exam']) && $civil['date_of_exam'] !== '0000-00-00')
            ? date('F j, Y', strtotime($civil['date_of_exam']))
            : '-';

        $civil['license_valid_until'] = (!empty($civil['license_valid_until']) && $civil['license_valid_until'] !== '0000-00-00')
            ? date('F j, Y', strtotime($civil['license_valid_until']))
            : '-';
        ?>
        <tr data-id="<?= esc($civil['id']) ?>">
            <td class="px-1 py-1 border" data-key="eligibility"><?= esc($civil['eligibility']) ?></td>
            <td class="px-1 py-1 border" data-key="rating"><?= esc($civil['rating']) ?></td>
            <td class="px-1 py-1 border" data-key="date_of_exam"><?= esc($civil['date_of_exam']) ?></td>
            <td class="px-1 py-1 border" data-key="place_of_exam"><?= esc($civil['place_of_exam']) ?></td>
            <td class="px-1 py-1 border" data-key="license_no"><?= esc($civil['license_no']) ?></td>
            <td class="px-1 py-1 border" data-key="license_valid_until"><?= esc($civil['license_valid_until']) ?></td>
            <td class="px-1 py-1 border text-center">
                <button class="editCivilBtn text-blue-600 px-1"><i class="fa-solid fa-pen-to-square"></i></button>
                <button class="deleteCivilBtn text-red-600 px-1"><i class="fa-solid fa-trash"></i></button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr id="noCivilRow">
        <td class="px-1 py-1 border text-center text-gray-500" colspan="7">
            No civil service records found.
        </td>
    </tr>
<?php endif; ?>

            </tbody>

        </table>
    </div>

    <div class="flex justify-end gap-2 -mt-1">
        <button id="addCivilBtn" class="bg-clsuGold px-3 py-1 rounded text-xs font-semibold">Add Civil Service</button>
    </div>
</div>

<div id="editCivilModal"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 z-50">
    <div class="bg-white rounded-2xl w-11/12 max-w-md p-6 transform scale-95 opacity-0 transition-all duration-300">
        <h3 class="text-lg font-bold text-clsuGreen mb-4">Civil Service</h3>
      <form id="editCivilForm" class="space-y-3">
    <input type="hidden" name="id" value="">
    
    <div>
        <label class="text-xs font-semibold">Eligibility</label>
        <input type="text" name="eligibility" placeholder="Enter eligibility" required class="w-full border px-2 py-1 rounded text-xs">
    </div>
    
    <div>
        <label class="text-xs font-semibold">Rating / Exam</label>
        <input type="text" name="rating" placeholder="Enter rating or exam" required class="w-full border px-2 py-1 rounded text-xs">
    </div>
    
    <div class="flex gap-2">
        <div class="flex-1">
            <label class="text-xs font-semibold">Date of Examination</label>
            <input type="date" name="date_of_exam" placeholder="dd/mm/yyyy" required class="w-full border px-2 py-1 rounded text-xs">
        </div>
        <div class="flex-1">
            <label class="text-xs font-semibold">Valid Until</label>
            <input type="date" name="license_valid_until" placeholder="dd/mm/yyyy" required class="w-full border px-2 py-1 rounded text-xs">
        </div>
    </div>
    
    <div>
        <label class="text-xs font-semibold">Place of Examination</label>
        <input type="text" name="place_of_exam" placeholder="Enter place of examination" required class="w-full border px-2 py-1 rounded text-xs">
    </div>
    
    <div>
        <label class="text-xs font-semibold">License / PRC No.</label>
        <input type="text" name="license_no" placeholder="Enter license or PRC number" required class="w-full border px-2 py-1 rounded text-xs">
    </div>
    
    <div class="flex justify-end gap-2 mt-3">
        <button type="button" id="cancelCivilModal"
            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition-colors">
            Cancel
        </button>
        <button type="submit"
            class="bg-clsuGreen hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition-colors">
            Save
        </button>
    </div>
</form>
 </div>
</div>

<div class="tab-content hidden" id="tab-training">
    <h2 class="text-lg font-bold text-clsuGreen mb-2">Trainings</h2>

    <div class="overflow-x-auto mb-4">
        <table class="table-auto w-full border border-gray-300 text-xs" id="table-training">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-1 py-1 border border-gray-300 w-36">Training Name</th>
                    <th class="px-1 py-1 border border-gray-300 w-28">Category</th>
                    <th class="px-1 py-1 border border-gray-300 w-28">Facilitator</th>
                    <th class="px-1 py-1 border border-gray-300 w-24">Sponsor</th>
                    <th class="px-1 py-1 border border-gray-300 w-14">Hours</th>
                    <th class="px-1 py-1 border border-gray-300 w-28">Remarks</th>
                    <th class="px-1 py-1 border border-gray-300 w-20">Certificate</th>
                    <th class="px-1 py-1 border border-gray-300 w-20">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($trainingRecords)): ?>
                    <?php foreach($trainingRecords as $training): ?>
                        <tr 
                            data-id="<?= esc($training['id_applicant_training']) ?>"
                            data-date_from="<?= esc($training['date_from']) ?>"
                            data-date_to="<?= esc($training['date_to']) ?>"
                        >
                            <td class="px-1 py-1 border border-gray-300" data-key="training_name"><?= esc($training['training_name']) ?></td>
                            <td class="px-1 py-1 border border-gray-300" data-key="training_category_id"><?= esc($training['training_category_name'] ?? '-') ?></td>
                            <td class="px-1 py-1 border border-gray-300" data-key="training_facilitator"><?= esc($training['training_facilitator'] ?? '-') ?></td>
                            <td class="px-1 py-1 border border-gray-300" data-key="training_sponsor"><?= esc($training['training_sponsor']) ?></td>
                            <td class="px-1 py-1 border border-gray-300" data-key="training_hours"><?= esc($training['training_hours']) ?></td>
                            <td class="px-1 py-1 border border-gray-300" data-key="training_remarks"><?= esc($training['training_remarks'] ?? '-') ?></td>
                            <td class="px-1 py-1 border border-gray-300" data-key="certificate_file">
                                <?php if(!empty($training['certificate_file'])): ?>
                                    <a href="<?= base_url('uploads/' . $training['certificate_file']) ?>" target="_blank" class="text-blue-600 hover:underline">View</a>
                                <?php else: ?> - <?php endif; ?>
                            </td>
                            <td class="px-1 py-1 border border-gray-300 text-center">
                                <button class="editTrainingBtn text-blue-600 px-1"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="deleteTrainingBtn text-red-600 px-1"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-1 py-1 border border-gray-300 text-center text-gray-500">No trainings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="flex justify-end gap-2 -mt-1">
        <button id="addTrainingBtn" class="bg-clsuGold px-3 py-1 rounded text-xs font-semibold">Add Training</button>
    </div>
</div>

<!-- Modal -->
<div id="editTrainingModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 z-50">
    <div class="bg-white rounded-2xl w-11/12 max-w-2xl p-6 transform scale-95 opacity-0 transition-all duration-300">
        <h3 class="text-lg font-bold text-clsuGreen mb-4">Training</h3>
        <form id="editTrainingForm" class="space-y-3" enctype="multipart/form-data">
            <input type="hidden" name="id" value="">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold">Training Name</label>
                    <input type="text" name="training_name" placeholder="Enter training name" required class="w-full border px-2 py-1 rounded text-xs">
                </div>
                <div>
                    <label class="text-xs font-semibold">Category</label>
                    <select name="training_category_id" required class="w-full border px-2 py-1 rounded text-xs">
                        <option value="">Select category</option>
                        <?php foreach($trainingCategories as $cat): ?>
                            <option value="<?= esc($cat['id_training_category']) ?>"><?= esc($cat['training_category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold">Facilitator</label>
                    <input type="text" name="training_facilitator" placeholder="Enter facilitator" required class="w-full border px-2 py-1 rounded text-xs">
                </div>
                <div>
                    <label class="text-xs font-semibold">Sponsor</label>
                    <input type="text" name="training_sponsor" placeholder="Enter sponsor" required class="w-full border px-2 py-1 rounded text-xs">
                </div>

                <div>
                    <label class="text-xs font-semibold">Training From</label>
                    <input type="date" name="date_from" required class="w-full border px-2 py-1 rounded text-xs">
                </div>
                <div>
                    <label class="text-xs font-semibold">Training To</label>
                    <input type="date" name="date_to" required class="w-full border px-2 py-1 rounded text-xs">
                </div>

                <div>
                    <label class="text-xs font-semibold">Hours</label>
                    <input type="number" name="training_hours" placeholder="Enter hours" required class="w-full border px-2 py-1 rounded text-xs">
                </div>
                <div>
                    <label class="text-xs font-semibold">Remarks</label>
                    <input type="text" name="training_remarks" placeholder="Enter remarks" class="w-full border px-2 py-1 rounded text-xs">
                </div>

                <div class="col-span-2">
                    <label class="text-xs font-semibold">Certificate File</label>
                    <input type="file" name="training_certificate_file" class="w-full border px-2 py-1 rounded text-xs">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-3">
                <button type="button" id="cancelTrainingModal" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition-colors">Cancel</button>
                <button type="submit" class="bg-clsuGreen hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition-colors">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="tab-content hidden" id="tab-files">
    <h2 class="text-xl font-bold text-clsuGreen mb-2">Files / Documents</h2>

    <div id="filesContent">
        <!-- Resume -->
        <div class="overflow-x-auto mb-2">
            <table class="table-auto w-full border-collapse text-xs">
                <tbody>
                    <tr>
                        <th class="px-2 py-1 border text-left w-1/3">1. Resume (PDF)</th>
                        <td class="px-2 py-1 border view-mode">
                            <?= !empty($fileRecords['resume']) 
                                ? '<a href="'.base_url('uploads/'.$fileRecords['resume']).'" target="_blank" class="text-blue-600 hover:underline">'.esc($fileRecords['resume']).'</a>'
                                : '<span class="text-gray-500">-</span>'; ?>
                        </td>
                        <td class="px-2 py-1 border edit-mode hidden">
                            <input type="file" name="resume" accept=".pdf" class="px-2 py-1 w-full text-xs">
                            <span class="text-xs text-gray-500">
                                <?= !empty($fileRecords['resume']) ? 'Current: '.esc($fileRecords['resume']) : 'No file chosen'; ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- TOR -->
        <div class="overflow-x-auto mb-2">
            <table class="table-auto w-full border-collapse text-xs">
                <tbody>
                    <tr>
                        <th class="px-2 py-1 border text-left w-1/3">2. Transcript of Records (TOR) (PDF)</th>
                        <td class="px-2 py-1 border view-mode">
                            <?= !empty($fileRecords['tor']) 
                                ? '<a href="'.base_url('uploads/'.$fileRecords['tor']).'" target="_blank" class="text-blue-600 hover:underline">'.esc($fileRecords['tor']).'</a>'
                                : '<span class="text-gray-500">-</span>'; ?>
                        </td>
                        <td class="px-2 py-1 border edit-mode hidden">
                            <input type="file" name="tor" accept=".pdf" class="px-2 py-1 w-full text-xs">
                            <span class="text-xs text-gray-500">
                                <?= !empty($fileRecords['tor']) ? 'Current: '.esc($fileRecords['tor']) : 'No file chosen'; ?>
                            </span>
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
                        <td class="px-2 py-1 border view-mode">
                            <?= !empty($fileRecords['diploma']) 
                                ? '<a href="'.base_url('uploads/'.$fileRecords['diploma']).'" target="_blank" class="text-blue-600 hover:underline">'.esc($fileRecords['diploma']).'</a>'
                                : '<span class="text-gray-500">-</span>'; ?>
                        </td>
                        <td class="px-2 py-1 border edit-mode hidden">
                            <input type="file" name="diploma" accept=".pdf" class="px-2 py-1 w-full text-xs">
                            <span class="text-xs text-gray-500">
                                <?= !empty($fileRecords['diploma']) ? 'Current: '.esc($fileRecords['diploma']) : 'No file chosen'; ?>
                            </span>
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
                        <td class="px-2 py-1 border view-mode">
                            <?= !empty($fileRecords['certificate']) 
                                ? '<a href="'.base_url('uploads/'.$fileRecords['certificate']).'" target="_blank" class="text-blue-600 hover:underline">'.esc($fileRecords['certificate']).'</a>'
                                : '<span class="text-gray-500">-</span>'; ?>
                        </td>
                        <td class="px-2 py-1 border edit-mode hidden">
                            <input type="file" name="certificate" accept=".pdf" class="px-2 py-1 w-full text-xs">
                            <span class="text-xs text-gray-500">
                                <?= !empty($fileRecords['certificate']) ? 'Current: '.esc($fileRecords['certificate']) : 'No file chosen'; ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Uploaded date -->
        <p class="text-xs text-gray-500 mb-2 view-mode">
            <?= (!empty($fileRecords['uploaded_at']) && $fileRecords['uploaded_at'] != '0000-00-00 00:00:00') 
                ? 'Uploaded on: '.date('d/m/Y H:i', strtotime($fileRecords['uploaded_at'])) 
                : 'Upload date not available'; ?>
        </p>
    </div>

    <!-- Update / Save / Cancel buttons -->
    <div class="flex justify-end gap-2 mt-2">

        <button id="updateFilesBtn" class="bg-clsuGold px-3 py-1 rounded text-xs font-semibold hover:bg-yellow-500">Edit Files</button>
<!-- Save & Cancel buttons (hidden by default) -->
<button type="button" id="cancelFilesBtn" class="bg-gray-400 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-gray-500 hidden">Cancel</button>
<button type="button" id="saveFilesBtn" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800 hidden">Save</button>
    </div>
</div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const tabButtons = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

// Function to activate a tab
function activateTab(tabName) {
    // Hide all contents
    tabContents.forEach(tc => tc.classList.add('hidden'));

    // Reset button styles
    tabButtons.forEach(b => {
        b.classList.remove('text-clsuGreen','border-b-2','border-clsuGreen');
        b.classList.add('text-gray-600');
    });

    // Show selected tab
    document.getElementById('tab-' + tabName).classList.remove('hidden');

    // Highlight active button
    const activeBtn = document.querySelector(`.tab-btn[data-tab="${tabName}"]`);
    if(activeBtn){
        activeBtn.classList.add('text-clsuGreen','border-b-2','border-clsuGreen');
        activeBtn.classList.remove('text-gray-600');
    }

    // Save active tab to localStorage
    localStorage.setItem('activeTab', tabName);
}

// Click event for tabs
tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        activateTab(btn.dataset.tab);
    });
});

// Restore tab after refresh
document.addEventListener('DOMContentLoaded', () => {
    const savedTab = localStorage.getItem('activeTab') || 'personal';
    activateTab(savedTab);
});
</script>

<script>
const editPhotoBtn = document.getElementById('editPhotoBtn');
const photoInput = document.getElementById('photoInput');
const profilePhoto = document.getElementById('profilePhoto');

editPhotoBtn.addEventListener('click', () => {
    Swal.fire({
        title: 'Edit Profile Picture?',
        text: "Do you want to change your profile picture?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if(result.isConfirmed){
            photoInput.click();
        }
    });
});

photoInput.addEventListener('change', () => {
    if(photoInput.files.length > 0){
        const file = photoInput.files[0];

        // Preview immediately
        const reader = new FileReader();
        reader.onload = e => {
            if(profilePhoto.tagName === 'IMG'){
                profilePhoto.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.id = 'profilePhoto';
                img.src = e.target.result;
                img.className = 'w-full h-full object-cover rounded-full';
                profilePhoto.replaceWith(img);
            }
        };
        reader.readAsDataURL(file);

        // Send via AJAX to update only photo
        const formData = new FormData();
        formData.append('photo', file);

        fetch('<?= base_url('account/updatePhoto') ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                Swal.fire({
                    icon: 'success',
                    title: 'Profile Updated!',
                    text: 'Your profile photo has been updated.',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong.'
            });
            console.error(err);
        });
    }
});
</script>

<script>
const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');
const cancelBtn = document.getElementById('cancelBtn');

let originalData = {};

const sexOptions = ['Male','Female'];
const civilStatusOptions = ['Single','Married','Widowed','Divorced','Separated'];

// Make table cell editable
function makeEditable(td){
    const key = td.dataset.key;
    let value = td.textContent.trim();
    if(value === '-') value = '';

    originalData[key] = value;

    switch(key){
        case 'sex':
            td.innerHTML = `<select class="w-full px-1 text-xs">${sexOptions.map(s => `<option value="${s}" ${s===value?'selected':''}>${s}</option>`).join('')}</select>`;
            break;
        case 'civil_status':
            td.innerHTML = `<select class="w-full px-1 text-xs">${civilStatusOptions.map(s => `<option value="${s}" ${s===value?'selected':''}>${s}</option>`).join('')}</select>`;
            break;
        case 'date_of_birth':
            td.innerHTML = `<input type="date" value="${value}" class="w-full px-1 text-xs"/>`;
            break;
        case 'photo':
            td.innerHTML = `<input type="file" name="${key}" class="w-full text-xs"/>`;
            break;
        // Personal phone and family contacts
        case 'phone':
        case 'spouse_contact_no':
        case 'father_contact_no':
        case 'mother_contact_no':
            td.innerHTML = `<input type="text" value="${value}" class="w-full px-1 text-xs phone-number" maxlength="11"/>`;
            break;
        default:
            td.innerHTML = `<input type="text" value="${value}" class="w-full px-1 text-xs"/>`;
    }
}

// Restore original table cell value
function restoreOriginal(td){
    td.textContent = originalData[td.dataset.key] || '-';
}

// EDIT button
editBtn.addEventListener('click', () => {
    originalData = {};
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        table.querySelectorAll('td[data-key]').forEach(td => makeEditable(td));
    });

    editBtn.classList.add('hidden');
    saveBtn.classList.remove('hidden');
    cancelBtn.classList.remove('hidden');
});

// CANCEL button
cancelBtn.addEventListener('click', () => {
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        table.querySelectorAll('td[data-key]').forEach(td => restoreOriginal(td));
    });

    editBtn.classList.remove('hidden');
    saveBtn.classList.add('hidden');
    cancelBtn.classList.add('hidden');
});

// Restrict phone number inputs to digits only
document.addEventListener('input', function(e){
    if(e.target.classList.contains('phone-number')){
        e.target.value = e.target.value.replace(/\D/g,'').slice(0,11);
    }
});

// SAVE button
saveBtn.addEventListener('click', () => {
    const formData = new FormData();
    const tables = document.querySelectorAll('table');
    let validationError = false;

    tables.forEach(table => {
        table.querySelectorAll('td[data-key]').forEach(td => {
            const input = td.querySelector('input, select');
            if(input){
                let val = input.value.trim();

                // Validation for phone/contact numbers
                if(['phone','spouse_contact_no','father_contact_no','mother_contact_no'].includes(td.dataset.key)){
                    if(val !== '' && !/^\d{11}$/.test(val)){
                        validationError = true;
                        td.classList.add('border-red-500');
                    } else {
                        td.classList.remove('border-red-500');
                    }
                }

                formData.append(td.dataset.key, val);
            }
        });
    });

    if(validationError){
        Swal.fire('Error', 'Phone/Contact numbers must be 11 digits and numeric only.', 'error');
        return;
    }

    fetch('<?= site_url("account/update") ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if(res.success){
            Swal.fire({
                icon: 'success',
                title: 'Saved!',
                text: res.message,
                timer: 2000,
                showConfirmButton: false,
                willClose: () => window.location.reload()
            });
            editBtn.classList.remove('hidden');
            saveBtn.classList.add('hidden');
            cancelBtn.classList.add('hidden');
        } else {
            Swal.fire('Error', res.message || 'Something went wrong while saving.', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'Unable to update profile.', 'error'));
});
</script>
<script>
const editEduBtn = document.getElementById('editEducationBtn');
const saveEduBtn = document.getElementById('saveEducationBtn');
const cancelEduBtn = document.getElementById('cancelEducationBtn');
let originalEduData = [];

// Make a cell editable with numeric validation and instant feedback
function makeEduEditable(td){
    const key = td.dataset.key;
    let value = td.textContent.trim();
    if(value === '-') value = '';

    // Save original value
    td.dataset.original = value;

    // Numeric fields with instant 4-digit validation
    if(['period_from','period_to','year_graduated'].includes(key)){
        td.innerHTML = `<input type="text" class="w-full px-2 py-1 text-sm" value="${value}" maxlength="4"
            oninput="
                this.value = this.value.replace(/[^0-9]/g,'');
                if(this.value.length === 4){ this.classList.remove('border-red-500'); }
                else{ this.classList.add('border-red-500'); }
            "
        />`;
    } else {
        td.innerHTML = `<input type="text" class="w-full px-2 py-1 text-sm" value="${value}"/>`;
    }
}

// EDIT button
editEduBtn.addEventListener('click', () => {
    originalEduData = [];
    const rows = document.querySelectorAll('#table-education tbody tr');

    rows.forEach(row => {
        const rowData = {};
        row.querySelectorAll('td[data-key]').forEach(td => {
            rowData[td.dataset.key] = td.textContent.trim();
            makeEduEditable(td);
        });
        originalEduData.push(rowData);
    });

    editEduBtn.classList.add('hidden');
    saveEduBtn.classList.remove('hidden');
    cancelEduBtn.classList.remove('hidden');
});

// CANCEL button
cancelEduBtn.addEventListener('click', () => {
    const rows = document.querySelectorAll('#table-education tbody tr');
    rows.forEach((row, i) => {
        row.querySelectorAll('td[data-key]').forEach(td => {
            td.textContent = originalEduData[i][td.dataset.key] || '-';
        });
    });

    editEduBtn.classList.remove('hidden');
    saveEduBtn.classList.add('hidden');
    cancelEduBtn.classList.add('hidden');
});

// SAVE button
saveEduBtn.addEventListener('click', () => {
    const rows = document.querySelectorAll('#table-education tbody tr');
    const formData = new FormData();
    let validationError = false;

    rows.forEach((row, i) => {
        row.querySelectorAll('td[data-key]').forEach(td => {
            const input = td.querySelector('input');
            if(input){
                let val = input.value.trim();

                // Validate numeric fields
                if(['period_from','period_to','year_graduated'].includes(td.dataset.key)){
                    if(val !== '' && !/^\d{4}$/.test(val)){
                        validationError = true;
                        td.classList.add('border-red-500');
                    } else {
                        td.classList.remove('border-red-500');
                    }
                }

                formData.append(`education[${i}][${td.dataset.key}]`, val);
            }
        });
    });

    if(validationError){
        Swal.fire('Error', 'Period of Attendance and Year Graduated must be 4-digit numbers only.', 'error');
        return;
    }

    fetch('<?= base_url("account/updateEducation") ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if(res.success){
            Swal.fire({
                icon: 'success',
                title: 'Saved!',
                text: res.message,
                timer: 2000,
                showConfirmButton: false,
                willClose: () => window.location.reload()
            });
            editEduBtn.classList.remove('hidden');
            saveEduBtn.classList.add('hidden');
            cancelEduBtn.classList.add('hidden');
        } else {
            Swal.fire('Error', res.message || 'Unable to save education.', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'Something went wrong.', 'error'));
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('table-work');
    const editModal = document.getElementById('editWorkModal');
    const modalBox = editModal.querySelector('div');
    const cancelBtn = document.getElementById('cancelEditModal');
    const editForm = document.getElementById('editWorkForm');
    const addWorkBtn = document.getElementById('addWorkBtn');

    let currentEditingRow = null;
    let isAddingNew = false;

    // Open modal and fill data
    function openEditModal(row = null) {
        currentEditingRow = row;
        isAddingNew = row === null;

        if (row) {
            const govtText = row.querySelector('[data-key="govt_service"]').textContent.trim();
            const data = {
                position_title: row.querySelector('[data-key="position_title"]').textContent.trim(),
                office: row.querySelector('[data-key="office"]').textContent.trim(),
                date_from: row.querySelector('[data-key="date_from"]').textContent.split('/').reverse().join('-'),
                date_to: row.querySelector('[data-key="date_to"]').textContent.split('/').reverse().join('-'),
                status_of_appointment: row.querySelector('[data-key="status_of_appointment"]').textContent.trim(),
                govt_service: govtText !== '-' ? govtText : ''
            };

            editForm.position_title.value = data.position_title;
            editForm.office.value = data.office;
            editForm.date_from.value = data.date_from;
            editForm.date_to.value = data.date_to;
            editForm.status_of_appointment.value = data.status_of_appointment;
            editForm.govt_service.value = data.govt_service;
        } else {
            // Clear form for adding
            editForm.reset();
        }

        // Show modal
        editModal.classList.remove('pointer-events-none', 'opacity-0');
        setTimeout(() => {
            modalBox.classList.remove('scale-95', 'opacity-0');
            modalBox.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeEditModal() {
        modalBox.classList.add('scale-95', 'opacity-0');
        editModal.classList.add('opacity-0');
        setTimeout(() => editModal.classList.add('pointer-events-none'), 300);
    }

    cancelBtn.addEventListener('click', closeEditModal);
    editModal.addEventListener('click', e => { if(e.target === editModal) closeEditModal(); });

    // Edit existing row
    table.addEventListener('click', e => {
        const btn = e.target.closest('.editWorkBtn');
        if(!btn) return;
        const row = btn.closest('tr');
        openEditModal(row);
    });

    // Delete row
    table.addEventListener('click', e => {
        const btn = e.target.closest('.deleteWorkBtn');
        if(!btn) return;
        const row = btn.closest('tr');
        const rowId = row.dataset.id;

     Swal.fire({
    text: 'This will permanently delete this record!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
}).then(result => {
    if (result.isConfirmed) {
        fetch('<?= base_url("account/deleteWorkExperience") ?>/' + rowId, {
            method: 'DELETE',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); // 🔄 refresh page
                });
            } else {
                Swal.fire('Error', res.message || 'Unable to delete.', 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'Something went wrong.', 'error'));
    }
});

    });

    // Add new row
    addWorkBtn.addEventListener('click', () => openEditModal(null));

    // Form submit (edit or add)
    editForm.addEventListener('submit', e => {
        e.preventDefault();

        const formData = new FormData(editForm);
        if(!isAddingNew && currentEditingRow) formData.append('id', currentEditingRow.dataset.id);

        fetch('<?= base_url("account/updateWorkExperience") ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With':'XMLHttpRequest' }
        }).then(res => res.json())
          .then(res => {
              if(res.success){
                  const fromDate = editForm.date_from.value ? new Date(editForm.date_from.value).toLocaleDateString('en-GB') : '-';
                  const toDate = editForm.date_to.value ? new Date(editForm.date_to.value).toLocaleDateString('en-GB') : '-';

                  if(isAddingNew){
                      // Append new row to table
                      const newRow = table.querySelector('tbody').insertRow();
                      newRow.dataset.id = res.id || ''; // Optional: return new ID from server
                      newRow.innerHTML = `
                        <td class="px-1 py-1 border" data-key="position_title">${editForm.position_title.value || '-'}</td>
                        <td class="px-1 py-1 border" data-key="office">${editForm.office.value || '-'}</td>
                        <td class="px-1 py-1 border" data-key="date_from">${fromDate}</td>
                        <td class="px-1 py-1 border" data-key="date_to">${toDate}</td>
                        <td class="px-1 py-1 border" data-key="status_of_appointment">${editForm.status_of_appointment.value || '-'}</td>
                        <td class="px-1 py-1 border" data-key="govt_service">${editForm.govt_service.value}</td>
                        <td class="px-1 py-1 border text-center">
                            <button class="editWorkBtn text-blue-600 px-1"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="deleteWorkBtn text-red-600 px-1"><i class="fa-solid fa-trash"></i></button>
                        </td>
                      `;
                  } else {
                      // Update existing row
                      currentEditingRow.querySelector('[data-key="position_title"]').textContent = editForm.position_title.value || '-';
                      currentEditingRow.querySelector('[data-key="office"]').textContent = editForm.office.value || '-';
                      currentEditingRow.querySelector('[data-key="date_from"]').textContent = fromDate;
                      currentEditingRow.querySelector('[data-key="date_to"]').textContent = toDate;
                      currentEditingRow.querySelector('[data-key="status_of_appointment"]').textContent = editForm.status_of_appointment.value || '-';
                      currentEditingRow.querySelector('[data-key="govt_service"]').textContent = editForm.govt_service.value;
                  }

          Swal.fire({
    icon: 'success',
    title: 'Saved!',
    text: res.message,
    timer: 1200,
    showConfirmButton: false
}).then(() => {
    location.reload(); // 🔄 refresh page
});

              } else {
                  Swal.fire('Error', res.message || 'Unable to save work experience.', 'error');
              }
          }).catch(()=> Swal.fire('Error','Something went wrong.','error'));
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('table-civil');
    const editModal = document.getElementById('editCivilModal');
    const modalBox = editModal.querySelector('div');
    const cancelBtn = document.getElementById('cancelCivilModal');
    const editForm = document.getElementById('editCivilForm');
    const addCivilBtn = document.getElementById('addCivilBtn');

    let currentEditingRow = null;

    function openModal(row=null){
        currentEditingRow = row;

        if(row){
            editForm.id.value = row.dataset.id;
            editForm.eligibility.value = row.querySelector('[data-key="eligibility"]').textContent.trim();
            editForm.rating.value = row.querySelector('[data-key="rating"]').textContent.trim();
            editForm.date_of_exam.value =
                row.querySelector('[data-key="date_of_exam"]').textContent !== '-'
                ? row.querySelector('[data-key="date_of_exam"]').textContent.split('/').reverse().join('-')
                : '';
            editForm.license_valid_until.value =
                row.querySelector('[data-key="license_valid_until"]').textContent !== '-'
                ? row.querySelector('[data-key="license_valid_until"]').textContent.split('/').reverse().join('-')
                : '';
            editForm.place_of_exam.value = row.querySelector('[data-key="place_of_exam"]').textContent.trim();
            editForm.license_no.value = row.querySelector('[data-key="license_no"]').textContent.trim();
        } else {
            editForm.reset();
        }

        editModal.classList.remove('pointer-events-none','opacity-0');
        setTimeout(()=> modalBox.classList.remove('scale-95','opacity-0'),10);
    }

    function closeModal(){
        modalBox.classList.add('scale-95','opacity-0');
        editModal.classList.add('opacity-0');
        setTimeout(()=> editModal.classList.add('pointer-events-none'),300);
    }

    addCivilBtn.addEventListener('click', ()=>openModal());
    cancelBtn.addEventListener('click', closeModal);
    editModal.addEventListener('click', e => { if(e.target===editModal) closeModal(); });

    table.addEventListener('click', e=>{
        const editBtn = e.target.closest('.editCivilBtn');
        const deleteBtn = e.target.closest('.deleteCivilBtn');

        if(editBtn){
            openModal(editBtn.closest('tr'));
        }

        if(deleteBtn){
            const row = deleteBtn.closest('tr');
            const id = row.dataset.id;

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will permanently delete this record!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then(result=>{
                if(result.isConfirmed){
                    fetch('<?= base_url("account/deleteCivilService") ?>/'+id,{
                        method:'DELETE',
                        headers:{'X-Requested-With':'XMLHttpRequest'}
                    })
                    .then(r=>r.json())
                    .then(res=>{
                        if(res.success){
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message,
                                timer: 1200,
                                showConfirmButton: false
                            }).then(()=> location.reload());
                        } else {
                            Swal.fire('Error',res.message,'error');
                        }
                    });
                }
            });
        }
    });

    editForm.addEventListener('submit', e=>{
        e.preventDefault();

        const formData = new FormData(editForm);

        fetch('<?= base_url("account/updateCivilService") ?>',{
            method:'POST',
            body: formData,
            headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(r=>r.json())
        .then(res=>{
            if(res.success){
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                }).then(()=> location.reload());
            } else {
                Swal.fire('Error',res.message,'error');
            }
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('table-training');
    const editModal = document.getElementById('editTrainingModal');
    const modalBox = editModal.querySelector('div');
    const cancelBtn = document.getElementById('cancelTrainingModal');
    const editForm = document.getElementById('editTrainingForm');
    const addBtn = document.getElementById('addTrainingBtn');

    function openModal(row=null){
        editForm.reset();
        editForm.id.value = '';
        if(row){
            editForm.id.value = row.dataset.id;
            editForm.training_name.value = row.querySelector('[data-key="training_name"]').textContent.trim();
            editForm.training_facilitator.value = row.querySelector('[data-key="training_facilitator"]').textContent.trim();
            editForm.date_from.value = row.dataset.date_from || '';
            editForm.date_to.value = row.dataset.date_to || '';
            editForm.training_hours.value = row.querySelector('[data-key="training_hours"]').textContent.trim();
            editForm.training_sponsor.value = row.querySelector('[data-key="training_sponsor"]').textContent.trim();
            editForm.training_remarks.value = row.querySelector('[data-key="training_remarks"]').textContent.trim();

            const categoryText = row.querySelector('[data-key="training_category_id"]').textContent.trim();
            editForm.training_category_id.querySelectorAll('option').forEach(opt => {
                opt.selected = (opt.textContent.trim() === categoryText);
            });
        }
        editModal.classList.remove('pointer-events-none','opacity-0');
        setTimeout(()=> modalBox.classList.remove('scale-95','opacity-0'),10);
    }

    function closeModal(){
        modalBox.classList.add('scale-95','opacity-0');
        editModal.classList.add('opacity-0');
        setTimeout(()=> editModal.classList.add('pointer-events-none'),300);
    }

    addBtn.addEventListener('click', ()=>openModal());
    cancelBtn.addEventListener('click', closeModal);
    editModal.addEventListener('click', e => { if(e.target===editModal) closeModal(); });

    table.addEventListener('click', e=>{
        const editBtn = e.target.closest('.editTrainingBtn');
        const deleteBtn = e.target.closest('.deleteTrainingBtn');

        if(editBtn) openModal(editBtn.closest('tr'));

        if(deleteBtn){
            const row = deleteBtn.closest('tr');
            const id = row.dataset.id;
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will permanently delete this record!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then(result=>{
                if(result.isConfirmed){
                    fetch('<?= base_url("account/deleteTraining") ?>/'+id,{
                        method:'DELETE',
                        headers:{'X-Requested-With':'XMLHttpRequest'}
                    }).then(r=>r.json())
                      .then(res=>{
                          if(res.success){
                              Swal.fire({icon:'success',title:'Deleted!',text:res.message,timer:1200,showConfirmButton:false})
                                  .then(()=> location.reload());
                          } else {
                              Swal.fire('Error',res.message,'error');
                          }
                      });
                }
            });
        }
    });

    editForm.addEventListener('submit', e=>{
        e.preventDefault();
        const formData = new FormData(editForm);
        const id = editForm.id.value;

        const url = id
            ? '<?= base_url("account/updateTraining") ?>'
            : '<?= base_url("account/addApplicantTraining") ?>';

        fetch(url,{
            method:'POST',
            body: formData,
            headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(r=>r.json())
        .then(res=>{
            if(res.success){
                Swal.fire({
                    icon:'success',
                    title:'Saved!',
                    text:res.message,
                    timer:1200,
                    showConfirmButton:false
                }).then(()=> location.reload());
            } else {
                Swal.fire('Error', res.message ?? 'Failed to save', 'error');
            }
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const updateBtn = document.getElementById('updateFilesBtn');
    const saveBtn = document.getElementById('saveFilesBtn');
    const cancelBtn = document.getElementById('cancelFilesBtn');

    const viewEls = document.querySelectorAll('#tab-files .view-mode');
    const editEls = document.querySelectorAll('#tab-files .edit-mode');

    // Show file inputs
    updateBtn.addEventListener('click', () => {
        viewEls.forEach(el => el.classList.add('hidden'));
        editEls.forEach(el => el.classList.remove('hidden'));

        updateBtn.classList.add('hidden');
        saveBtn.classList.remove('hidden');
        cancelBtn.classList.remove('hidden');
    });

    // Cancel editing
    cancelBtn.addEventListener('click', () => {
        viewEls.forEach(el => el.classList.remove('hidden'));
        editEls.forEach(el => el.classList.add('hidden'));

        updateBtn.classList.remove('hidden');
        saveBtn.classList.add('hidden');
        cancelBtn.classList.add('hidden');
    });

    // Save files via AJAX
    saveBtn.addEventListener('click', async () => {
        const formData = new FormData();

        // Collect all file inputs dynamically
        editEls.forEach(td => {
            const input = td.querySelector('input[type="file"]');
            if (input && input.files[0]) {
                formData.append(input.name, input.files[0]);
            }
        });

        try {
            const res = await fetch('<?= base_url("account/updateFiles") ?>', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            if(data.status === 'success'){
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: data.message || 'Documents updated successfully!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Failed to update files', 'error');
            }
        } catch(err) {
            Swal.fire('Error', 'Something went wrong', 'error');
            console.error(err);
        }
    });
});
</script>

</body>
</html>
