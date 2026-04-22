<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<head>
    <title>Applicant Dashboard | CLSU Online Job Application System</title>
    <link rel="icon" type="image/x-icon" href="/HRMO/public/assets/images/favicon.ico">
</head>

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
                    // Check if photo is from Google Drive or local
                    if (!empty($profile['photo']) && preg_match('/^[a-zA-Z0-9_-]{20,}$/', $profile['photo']) && !preg_match('/^\d{10}_/', $profile['photo'])): ?>
                        <!-- Google Drive Photo -->
                        <img src="<?= base_url('account/getProfilePhoto') ?>" class="w-8 h-8 rounded-full border-2 border-white object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center border-2 border-white hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12 2a5 5 0 100 10 5 5 0 000-10zm-7 18a7 7 0 0114 0H5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    <?php elseif(!empty($profile['photo']) && file_exists(FCPATH . 'uploads/' . $profile['photo'])): ?>
                        <!-- Local Photo -->
                        <img src="<?= base_url('uploads/' . $profile['photo']) ?>" class="w-8 h-8 rounded-full border-2 border-white object-cover">
                    <?php else: ?>
                        <!-- No Photo -->
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
                            <?= esc($user['first_name'] ?? $profile['first_name'] ?? '') ?> 
                            <?= !empty($user['middle_name'] ?? $profile['middle_name'] ?? '') ? esc(substr($user['middle_name'] ?? $profile['middle_name'],0,1)) . '. ' : '' ?>
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
<div class="profile-pic relative w-32 h-32 mx-auto rounded-full bg-gray-200 overflow-visible flex items-center justify-center mb-4">
    <?php
    // Check if photo is from Google Drive or local
    if (!empty($profile['photo']) && preg_match('/^[a-zA-Z0-9_-]{20,}$/', $profile['photo']) && !preg_match('/^\d{10}_/', $profile['photo'])): ?>
        <!-- Google Drive Photo -->
        <img id="profilePhoto" src="<?= base_url('account/getProfilePhoto') ?>" class="w-full h-full object-cover rounded-full" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <svg id="profilePhotoPlaceholder" xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-500 hidden" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M12 2a5 5 0 100 10 5 5 0 000-10zm-7 18a7 7 0 0114 0H5z" clip-rule="evenodd"/>
        </svg>
    <?php elseif (!empty($profile['photo']) && file_exists(FCPATH . 'uploads/' . $profile['photo'])): ?>
        <!-- Local Photo -->
        <img id="profilePhoto" src="<?= base_url('uploads/' . esc($profile['photo'])) ?>" class="w-full h-full object-cover rounded-full">
    <?php else: ?>
        <!-- No Photo -->
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

<!-- === Personal Information Section === -->
<div class="flex justify-between items-center mb-3">
    <h2 class="text-lg font-bold text-clsuGreen">Personal Information</h2>
    <button id="editPersonalInfoBtn" class="bg-clsuGreen text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-green-800 transition-colors duration-200 flex items-center">
        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        Edit Personal Information
    </button>
</div>

<!-- Flat Compact Layout -->
<div class="space-y-3">
  
<!-- Personal Details (Balanced Grid Layout) -->
<div class="pb-2 border-b border-gray-200">

  <h3 class="text-xs font-semibold text-gray-700 mb-1.5 flex items-center">
    <svg class="w-3.5 h-3.5 mr-1 text-clsuGreen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
    </svg>
    Personal Details
  </h3>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-2">

    <!-- Full Name -->
    <div>
      <p class="text-[9px] font-medium text-gray-500 uppercase tracking-wide">Full Name</p>
      <p class="text-xs font-medium text-gray-800 mt-0.5">
        <?php
          $fullName = trim(
            ($profile['first_name'] ?? '') . ' ' .
            ($profile['middle_name'] ?? '') . ' ' .
            ($profile['last_name'] ?? '') . ' ' .
            ($profile['suffix'] ?? '')
          );
          echo $fullName !== '' ? esc($fullName) : 'N/A';
        ?>
      </p>
    </div>

    <!-- Sex -->
    <div>
      <p class="text-[9px] font-medium text-gray-500 uppercase tracking-wide">Sex</p>
      <p class="text-xs font-medium text-gray-800 mt-0.5">
        <?= esc($profile['sex'] ?? 'N/A') ?>
      </p>
    </div>

    <!-- Date of Birth -->
    <div>
      <p class="text-[9px] font-medium text-gray-500 uppercase tracking-wide">Date of Birth</p>
      <p class="text-xs font-medium text-gray-800 mt-0.5">
        <?= isset($profile['date_of_birth']) && $profile['date_of_birth'] != ''
            ? date('F j, Y', strtotime($profile['date_of_birth']))
            : 'N/A' ?>
      </p>
    </div>

    <!-- Civil Status -->
    <div>
      <p class="text-[9px] font-medium text-gray-500 uppercase tracking-wide">Civil Status</p>
      <p class="text-xs font-medium text-gray-800 mt-0.5">
        <?= esc($profile['civil_status'] ?? 'N/A') ?>
      </p>
    </div>

  </div>
</div>


  <!-- Contact Information -->
  <div class="pb-2 border-b border-gray-200">
    <h3 class="text-xs font-semibold text-gray-700 mb-1.5 flex items-center">
      <svg class="w-3.5 h-3.5 mr-1 text-clsuGreen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
      </svg>
      Contact Information
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
      <div>
        <p class="text-[9px] font-medium text-gray-500 uppercase tracking-wide">Email</p>
        <p class="text-xs font-medium text-gray-800 mt-0.5 break-words"><?= esc($profile['email'] ?? 'N/A') ?></p>
      </div>
      <div>
        <p class="text-[9px] font-medium text-gray-500 uppercase tracking-wide">Phone Number</p>
        <p class="text-xs font-medium text-gray-800 mt-0.5"><?= esc($profile['phone'] ?? 'N/A') ?></p>
      </div>
      <div>
        <p class="text-[9px] font-medium text-gray-500 uppercase tracking-wide">Citizenship</p>
        <p class="text-xs font-medium text-gray-800 mt-0.5"><?= esc($profile['citizenship'] ?? 'N/A') ?></p>
      </div>
    </div>
  </div>

  <!-- Addresses -->
  <div>
    <h3 class="text-xs font-semibold text-gray-700 mb-1.5 flex items-center">
      <svg class="w-3.5 h-3.5 mr-1 text-clsuGreen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
      </svg>
      Addresses
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
      <div>
        <p class="text-[9px] font-medium text-gray-500 uppercase tracking-wide">Residential Address</p>
        <p class="text-xs font-medium text-gray-800 mt-0.5"><?= esc($profile['residential_address'] ?? 'N/A') ?></p>
      </div>
      <div>
        <p class="text-[9px] font-medium text-gray-500 uppercase tracking-wide">Permanent Address</p>
        <p class="text-xs font-medium text-gray-800 mt-0.5"><?= esc($profile['permanent_address'] ?? 'N/A') ?></p>
      </div>
    </div>
  </div>
</div>

<!-- Edit Personal Info Modal -->
<div id="personalModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 z-50">
  <div class="bg-white rounded-lg w-3/4 max-w-2xl p-6 transform scale-95 opacity-0 transition-all duration-300">

    <!-- Header -->
    <h3 class="text-lg font-bold text-clsuGreen mb-1">Edit Personal Information</h3>
    <p class="text-[11px] text-gray-500 mb-3">
        All fields with <span class="text-red-500">*</span> are required. Enter <b>N/A</b> if not applicable.
    </p>

    <!-- Form Grid: 5 rows -->
    <form id="personalForm">
      <div class="grid grid-cols-6 gap-4 mb-4">

        <!-- Row 1 -->
        <div class="col-span-3">
          <label class="text-xs font-semibold">First Name <span class="text-red-500">*</span></label>
          <input type="text" id="first_name" name="first_name"
                 value="<?= esc($profile['first_name']) ?>"
                 class="w-full text-xs px-2 py-1 border rounded"
                 required>
        </div>
        <div class="col-span-3">
          <label class="text-xs font-semibold">Middle Name</label>
          <input type="text" id="middle_name" name="middle_name"
                 value="<?= esc($profile['middle_name']) ?>"
                 class="w-full text-xs px-2 py-1 border rounded">
        </div>

        <!-- Row 2 -->
        <div class="col-span-3">
          <label class="text-xs font-semibold">Last Name <span class="text-red-500">*</span></label>
          <input type="text" id="last_name" name="last_name"
                 value="<?= esc($profile['last_name']) ?>"
                 class="w-full text-xs px-2 py-1 border rounded"
                 required>
        </div>
        <div class="col-span-3">
          <label class="text-xs font-semibold">Suffix</label>
          <input type="text" id="suffix" name="suffix"
                 value="<?= esc($profile['suffix']) ?>"
                 class="w-full text-xs px-2 py-1 border rounded">
        </div>

        <!-- Row 3 -->
        <div class="col-span-2">
          <label class="text-xs font-semibold">Sex <span class="text-red-500">*</span></label>
          <select id="sex" name="sex" class="w-full text-xs px-2 py-1 border rounded" required>
            <option value="">Select Sex</option>
            <option value="Male" <?= $profile['sex']=='Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $profile['sex']=='Female' ? 'selected' : '' ?>>Female</option>
          </select>
        </div>
        <div class="col-span-2">
          <label class="text-xs font-semibold">Date of Birth <span class="text-red-500">*</span></label>
          <input type="date" id="dob" name="dob"
                 value="<?= !empty($profile['date_of_birth']) ? date('Y-m-d', strtotime($profile['date_of_birth'])) : '' ?>"
                 class="w-full text-xs px-2 py-1 border rounded"
                 required>
        </div>
        <div class="col-span-2">
          <label class="text-xs font-semibold">Civil Status <span class="text-red-500">*</span></label>
          <select id="civil_status" name="civil_status" class="w-full text-xs px-2 py-1 border rounded" required>
            <option value="">Select Civil Status</option>
            <?php
              $statuses = ['Single','Married','Widowed','Separated','Divorced'];
              foreach($statuses as $status){
                $selected = $profile['civil_status']==$status ? 'selected' : '';
                echo "<option value='$status' $selected>$status</option>";
              }
            ?>
          </select>
        </div>

        <!-- Row 4 -->
        <div class="col-span-2">
          <label class="text-xs font-semibold">Email <span class="text-red-500">*</span></label>
          <input type="email" id="email" name="email"
                 value="<?= esc($profile['email']) ?>"
                 class="w-full text-xs px-2 py-1 border rounded"
                 required>
        </div>
        <div class="col-span-2">
          <label class="text-xs font-semibold">Phone Number <span class="text-red-500">*</span></label>
          <input type="text" id="phone" name="phone"
                 value="<?= esc($profile['phone']) ?>"
                 class="w-full text-xs px-2 py-1 border rounded"
                 required>
        </div>
        <div class="col-span-2">
          <label class="text-xs font-semibold">Citizenship <span class="text-red-500">*</span></label>
          <input type="text" id="citizenship" name="citizenship"
                 value="<?= esc($profile['citizenship']) ?>"
                 class="w-full text-xs px-2 py-1 border rounded"
                 required>
        </div>

        <!-- Row 5 -->
        <div class="col-span-3">
          <label class="text-xs font-semibold">Residential Address <span class="text-red-500">*</span></label>
          <input type="text" id="residential_address" name="residential_address"
                 value="<?= esc($profile['residential_address']) ?>"
                 class="w-full text-xs px-2 py-1 border rounded"
                 required>
        </div>
        <div class="col-span-3">
          <label class="text-xs font-semibold">Permanent Address <span class="text-red-500">*</span></label>
          <input type="text" id="permanent_address" name="permanent_address"
                 value="<?= esc($profile['permanent_address']) ?>"
                 class="w-full text-xs px-2 py-1 border rounded"
                 required>
        </div>

      </div>

      <!-- Modal Buttons -->
      <div class="flex justify-end gap-2">
        <button type="button" id="cancelPersonal" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition-colors">Cancel</button>
        <button type="submit" class="bg-clsuGreen hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition-colors">Save</button>
      </div>
    </form>

  </div>
</div>
<script>
// Flag to track if user is forced to fill profile
let forceProfileFill = false;

// Function to open personal modal programmatically
function openPersonalModal() {
    const personalModal = document.getElementById('personalModal');
    personalModal.classList.remove('opacity-0','pointer-events-none');
    personalModal.querySelector('div').classList.remove('scale-95','opacity-0');
    
    // Set flag to prevent closing
    forceProfileFill = true;
}

document.addEventListener('DOMContentLoaded', () => {
    const personalModal = document.getElementById('personalModal');
    const editBtn = document.getElementById('editPersonalInfoBtn');
    const cancelBtn = document.getElementById('cancelPersonal');
    const form = document.getElementById('personalForm');

    // Store original values
    const originalValues = {};
    ['first_name','middle_name','last_name','suffix','sex','dob','civil_status','email','phone','citizenship','residential_address','permanent_address']
    .forEach(id => originalValues[id] = document.getElementById(id).value);

    // Open modal
    editBtn.addEventListener('click', () => {
        personalModal.classList.remove('opacity-0','pointer-events-none');
        personalModal.querySelector('div').classList.remove('scale-95','opacity-0');
        forceProfileFill = false; // User opened it manually, allow closing
    });

    // Cancel → reset (only if not forced)
    cancelBtn.addEventListener('click', () => {
        if (forceProfileFill) {
            Swal.fire({
                icon: 'warning',
                title: 'Required!',
                text: 'You must complete your profile before continuing.',
                confirmButtonColor: '#0B6B3A',
                allowOutsideClick: false,
                allowEscapeKey: false
            });
            return;
        }
        
        for (let id in originalValues) {
            document.getElementById(id).value = originalValues[id];
        }
        personalModal.classList.add('opacity-0','pointer-events-none');
        personalModal.querySelector('div').classList.add('scale-95','opacity-0');
    });

    // Click outside → close (only if not forced)
    personalModal.addEventListener('click', e => {
        if (e.target === personalModal) {
            if (forceProfileFill) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Required!',
                    text: 'You must complete your profile before continuing.',
                    confirmButtonColor: '#0B6B3A',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                return;
            }
            personalModal.classList.add('opacity-0','pointer-events-none');
            personalModal.querySelector('div').classList.add('scale-95','opacity-0');
        }
    });

    // Form submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Phone validation
        const phone = document.getElementById('phone').value.trim();
        if (!/^\d{11}$/.test(phone)) {
            Swal.fire({icon:'warning',title:'Invalid Phone Number',text:'Phone number must be exactly 11 digits.',timer:2500,showConfirmButton:false});
            return;
        }

        // DOB validation
        const dob = document.getElementById('dob').value;
        if (new Date(dob) > new Date()) {
            Swal.fire({icon:'warning',title:'Invalid Date',text:'Date of Birth cannot be in the future.',timer:2500,showConfirmButton:false});
            return;
        }

        // Submit form
        const formData = new FormData(form);
        try {
            const res = await fetch('<?= site_url("account/update") ?>', { method:'POST', body:formData });
            const data = await res.json();

            if (data.success) {
                // Reset force flag - user can now navigate freely
                forceProfileFill = false;
                
                // Re-enable all navigation
                const navLinks = document.querySelectorAll('nav a');
                navLinks.forEach(link => {
                    link.style.pointerEvents = 'auto';
                    link.style.opacity = '1';
                    link.style.cursor = 'pointer';
                    link.removeAttribute('title');
                });
                
                const dropdownItems = document.querySelectorAll('#accountDropdown a');
                dropdownItems.forEach(item => {
                    item.style.pointerEvents = 'auto';
                    item.style.opacity = '1';
                    item.style.cursor = 'pointer';
                });
                
                // Close modal first
                personalModal.classList.add('opacity-0','pointer-events-none');
                personalModal.querySelector('div').classList.add('scale-95','opacity-0');
                
                if (data.redirect_url) {
                    // Show success message first, then redirect after a delay
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: data.message,
                        timer: 2000,  // Increased timer to ensure message is visible
                        showConfirmButton: false,
                        confirmButtonColor: '#0B6B3A'
                    });
                    // Redirect after the timer completes automatically
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 2000);
                } else {
                    // No redirect, just show success
                    Swal.fire({
                        icon:'success',
                        title:'Saved!',
                        text:data.message,
                        timer:1500,
                        showConfirmButton:true,
                        confirmButtonColor: '#0B6B3A'
                    }).then(() => {
                        location.reload();
                    });
                }
            } else {
                Swal.fire({icon:'error',title:'Error',text:data.message,timer:2500,showConfirmButton:false});
            }
        } catch(err) {
            console.error(err);
            Swal.fire({icon:'error',title:'Error',text:'Failed to save profile.',timer:2500,showConfirmButton:false});
        }
    });
});
</script>
</div>
  
<!-- === EDUCATION TAB === -->
<div class="tab-content" id="tab-education">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-bold text-clsuGreen">Educational Background</h2>
        <button id="addEducationBtn" class="bg-clsuGreen text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-green-800 transition-colors duration-200 flex items-center">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Education
        </button>
    </div>

    <!-- Professional Compact Educational Background Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="table-education">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Level</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Name of School</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Degree / Course</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">From</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">To</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Highest Level / Units Earned</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Year Graduated</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Scholarship / Academic Honors</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $hasAnyEducation = false;
                    foreach ($educationRecords ?? [] as $edu) {
                        if (!empty($edu['school_name']) && $edu['school_name'] !== '-') {
                            $hasAnyEducation = true;
                            break;
                        }
                    }

                    if (!$hasAnyEducation): ?>
                        <tr>
                            <td class="px-3 py-4 text-center text-gray-500 italic" colspan="9">
                                No educational background found for this applicant.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($libDegreeLevels as $levelObj):
                            $levelName = $levelObj['degree_level_name'];
                            $levelRecords = array_filter($educationRecords ?? [], fn($r) => $r['degree_level_id'] == $levelObj['id_degree_level']);

                            if (empty($levelRecords)){
                                $levelRecords[] = [
                                    'id' => null,
                                    'school_name' => '-',
                                    'degree_course' => '-',
                                    'period_from' => '-',
                                    'period_to' => '-',
                                    'highest_level_units' => '-',
                                    'year_graduated' => '-',
                                    'awards' => '-',
                                    'degree_id' => null
                                ];
                            }

                            $firstRow = true;
                            foreach($levelRecords as $edu): ?>
                                <tr class="hover:bg-gray-50 transition-colors" data-level="<?= esc($levelName) ?>" data-id="<?= esc($edu['id']) ?>">
                                    <td class="px-3 py-2 border-b font-semibold text-gray-800"><?= $firstRow ? esc($levelName) : '' ?></td>
                                    <td class="px-3 py-2 border-b text-gray-700" data-key="school_name"><?= esc($edu['school_name']) ?></td>
                                    <td class="px-3 py-2 border-b text-gray-700" data-key="degree_course" data-degree-id="<?= esc($edu['degree_id']) ?>"><?= esc(($edu['degree_course'] ?? '') . (($edu['course'] ?? '') ? ' - ' . $edu['course'] : '')) ?: '-' ?></td>
                                    <td class="px-3 py-2 border-b text-gray-700 text-center" data-key="period_from"><?= esc($edu['period_from']) ?></td>
                                    <td class="px-3 py-2 border-b text-gray-700 text-center" data-key="period_to"><?= esc($edu['period_to']) ?></td>
                                    <td class="px-3 py-2 border-b text-gray-700" data-key="highest_level_units"><?= esc($edu['highest_level_units']) ?></td>
                                    <td class="px-3 py-2 border-b text-gray-700 text-center" data-key="year_graduated"><?= esc($edu['year_graduated']) ?></td>
                                    <td class="px-3 py-2 border-b text-gray-700" data-key="awards"><?= esc($edu['awards']) ?></td>

                                    <?php if($edu['id']): ?>
                                        <td class="px-3 py-2 border-b text-center">
                                            <div class="flex justify-center gap-1">
                                                <button class="editWorkBtn inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </button>
                                                <button class="deleteWorkBtn inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    <?php else: ?>
                                        <td class="px-3 py-2 border-b text-center text-gray-400">-</td>
                                    <?php endif; ?>
                                </tr>
                            <?php $firstRow = false; endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- === MODAL === -->
<div id="educationModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 z-[9999]">
    <div class="bg-white rounded-2xl w-11/12 max-w-lg p-6 transform scale-95 opacity-0 transition-all duration-300">
        <h3 class="text-lg font-bold text-clsuGreen mb-1" id="educationModalTitle">Edit Education</h3>
        <p class="text-[11px] text-gray-500 mb-3">
            All fields with <span class="text-red-500">*</span> are required. Enter <b>N/A</b> if not applicable.
        </p>
        <form id="educationForm" class="space-y-3">
            <div>
                <label class="text-xs font-semibold">Level<span class="text-red-500">*</span></label>
                <select name="degree_level_id" class="w-full border px-2 py-1 rounded text-xs" required>
                    <option value="" disabled selected>Select Level</option>
                    <?php foreach($libDegreeLevels as $l): ?>
                        <option value="<?= $l['id_degree_level'] ?>"><?= $l['degree_level_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="text-xs font-semibold">Name of School<span class="text-red-500">*</span></label>
                <input type="text" name="school_name" class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter school name" required>
            </div>

            <div>
                <label class="text-xs font-semibold">Degree<span class="text-red-500">*</span></label>
                <select name="degree_id" class="w-full border px-2 py-1 rounded text-xs" required>
                    <option value="" disabled selected>Select Degree</option>
                </select>
            </div>

            <!-- Course Field - Visible for vocational through doctorate -->
            <div id="courseField" class="hidden">
                <label class="text-xs font-semibold">Course<span class="text-red-500">*</span></label>
                <input type="text" name="course_name" class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter course name">
            </div>

            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="text-xs font-semibold">From<span class="text-red-500">*</span></label>
                    <input type="number" name="period_from" class="w-full border px-2 py-1 rounded text-xs" min="1900" max="2100" placeholder="YYYY" required>
                </div>
                <div class="flex-1">
                    <label class="text-xs font-semibold">To<span class="text-red-500">*</span></label>
                    <input type="number" name="period_to" class="w-full border px-2 py-1 rounded text-xs" min="1900" max="2100" placeholder="YYYY" required>
                </div>
            </div>

            <!-- Reordered and optional fields -->
            <div>
                <label class="text-xs font-semibold">Year Graduated</label>
                <input type="number" name="year_graduated" class="w-full border px-2 py-1 rounded text-xs" min="1900" max="2100" placeholder="YYYY">
            </div>

            <div>
                <label class="text-xs font-semibold">Highest Level / Units Earned</label>
                <input type="text" name="highest_level_units" class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter units or level">
            </div>

            <div>
                <label class="text-xs font-semibold">Scholarship / Academic Honors</label>
                <input type="text" name="awards" class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter awards">
            </div>

            <div class="flex justify-end gap-2 mt-3">
                <button type="button" id="cancelEducationModal" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition-colors">Cancel</button>
                <button type="submit" class="bg-clsuGreen hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition-colors">Save</button>
            </div>
        </form>
    </div>
</div>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/* ===== FIX: FORCE SWEETALERT ABOVE MODAL ===== */
if (!document.getElementById('swal-z-fix')) {
    const style = document.createElement('style');
    style.id = 'swal-z-fix';
    style.innerHTML = `
        .swal2-container {
            z-index: 9999 !important;
        }
    `;
    document.head.appendChild(style);
}

const libDegrees = <?= json_encode($libDegrees) ?>;
const libDegreeLevels = <?= json_encode($libDegreeLevels) ?>;

// Elements
const modal = document.getElementById('educationModal');
const modalTitle = document.getElementById('educationModalTitle');
const form = document.getElementById('educationForm');

const levelSelect = form.degree_level_id;
const degreeSelect = form.degree_id;

// ===== TEMP DATA FOR ADD / EDIT SEPARATELY =====
let tempAddData = {};   // unsaved data in Add modal
let tempEditData = {};  // unsaved data in Edit modal
let editingRow = null;
let isEditing = false;

// ===== YEAR FIELDS (allow YYYY or N/A) =====
const yearFields = ['period_from','period_to','year_graduated'];
yearFields.forEach(name => {
    const input = form[name];
    input.addEventListener('input', () => {
        let val = input.value.toUpperCase();
        if(val === 'N/A'){
            input.value = 'N/A';
        } else {
            input.value = val.replace(/\D/g,'').slice(0,4);
        }
        if(isEditing){
            tempEditData[name] = input.value;
        } else {
            tempAddData[name] = input.value;
        }
    });
});

// Auto-fix lowercase n/a on blur
form.querySelectorAll('input').forEach(input => {
    input.addEventListener('blur', () => {
        if(input.value.trim().toLowerCase() === 'n/a'){
            input.value = 'N/A';
        }
    });
});

// ===== UPDATE DEGREE DROPDOWN =====
function updateDegreeOptions(levelId, selectedDegreeId = null){
    degreeSelect.innerHTML = '<option value="" disabled selected>Select Degree</option>';
    libDegrees
        .filter(d => d.degree_level == levelId)
        .forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id_degree;
            opt.text = d.degree_name;
            if(selectedDegreeId && selectedDegreeId == d.id_degree) opt.selected = true;
            degreeSelect.appendChild(opt);
        });
}

// ===== DYNAMIC COURSE FIELD VISIBILITY =====
function toggleCourseField(levelId) {
    const courseField = document.getElementById('courseField');
    const courseInput = form.course_name;
    
    // Levels that should show course field (Vocational/Trade, College, Graduate Studies, Doctorate)
    const courseLevels = ['3', '4', '5', '6']; // Assuming these are the IDs
    
    if (courseLevels.includes(levelId)) {
        // Show course field
        courseField.classList.remove('hidden');
        courseInput.setAttribute('required', 'required');
    } else {
        // Hide course field
        courseField.classList.add('hidden');
        courseInput.removeAttribute('required');
        courseInput.value = ''; // Clear course field when switching away
    }
}

function openModal(title, data = {}) {
    modalTitle.innerText = title;
    const newEditingRow = data.id ? document.querySelector(`tr[data-id="${data.id}"]`) : null;
    
    // If switching rows, clear tempEditData
    if (isEditing && editingRow !== newEditingRow) {
        tempEditData = {};
    }

    isEditing = Boolean(data.id); // true if editing
    editingRow = newEditingRow;

    if (isEditing) {
        // Edit mode: use tempEditData if exists, otherwise row data
        levelSelect.value = tempEditData.degree_level_id ?? data.degree_level_id ?? '';
        updateDegreeOptions(levelSelect.value, tempEditData.degree_id ?? data.degree_id ?? null);
        toggleCourseField(levelSelect.value);

        form.school_name.value         = tempEditData.school_name ?? data.school_name ?? '';
        form.period_from.value         = tempEditData.period_from ?? data.period_from ?? '';
        form.period_to.value           = tempEditData.period_to ?? data.period_to ?? '';
        form.highest_level_units.value = tempEditData.highest_level_units ?? data.highest_level_units ?? '';
        form.year_graduated.value      = tempEditData.year_graduated ?? data.year_graduated ?? '';
        form.awards.value              = tempEditData.awards ?? data.awards ?? '';
        
        // Handle course field for edit mode
        if (!document.getElementById('courseField').classList.contains('hidden')) {
            form.course_name.value = tempEditData.course_name ?? data.course_name ?? '';
        }
    } else {
        // Add mode
        levelSelect.value = tempAddData.degree_level_id ?? '';
        updateDegreeOptions(levelSelect.value, tempAddData.degree_id ?? null);
        toggleCourseField(levelSelect.value);

        form.school_name.value         = tempAddData.school_name ?? '';
        form.period_from.value         = tempAddData.period_from ?? '';
        form.period_to.value           = tempAddData.period_to ?? '';
        form.highest_level_units.value = tempAddData.highest_level_units ?? '';
        form.year_graduated.value      = tempAddData.year_graduated ?? '';
        form.awards.value              = tempAddData.awards ?? '';
        
        // Handle course field for add mode
        if (!document.getElementById('courseField').classList.contains('hidden')) {
            form.course_name.value = tempAddData.course_name ?? '';
        }
    }

    modal.classList.remove('opacity-0', 'pointer-events-none');
    modal.querySelector('div').classList.remove('scale-95', 'opacity-0');
}

// ===== CLOSE MODAL =====
function closeModal(clearTemp = false) {
    modal.classList.add('opacity-0', 'pointer-events-none');
    modal.querySelector('div').classList.add('scale-95', 'opacity-0');

    if (clearTemp) {
        if (isEditing) {
            tempEditData = {};
        } else {
            tempAddData = {};
        }
        form.reset();
        editingRow = null;
    }
}

// Clicking outside the modal keeps edits
modal.addEventListener('click', e => {
    if (e.target === modal) closeModal(false);
});

// ===== DEGREE LEVEL CHANGE =====
levelSelect.addEventListener('change', e => {
    updateDegreeOptions(e.target.value);
    toggleCourseField(e.target.value);
    if (isEditing) {
        tempEditData.degree_level_id = e.target.value;
    } else {
        tempAddData.degree_level_id = e.target.value;
    }
});

// ===== TRACK CHANGES TO FORM FIELDS =====
form.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('input', e => {
        if (isEditing) {
            tempEditData[e.target.name] = e.target.value;
        } else {
            tempAddData[e.target.name] = e.target.value;
        }
    });
});

// Open Add Education modal
document.getElementById('addEducationBtn').addEventListener('click', () => openModal('Add Education'));

// Cancel button resets the form to original data
document.getElementById('cancelEducationModal').addEventListener('click', () => closeModal(true));

// ===== EDIT BUTTONS =====
function attachRowEditButtons(){
    document.querySelectorAll('.editWorkBtn').forEach(btn => {
        btn.addEventListener('click', e => {
            const row = e.target.closest('tr');
            const data = {
                id: row.dataset.id,
                degree_level_id: libDegreeLevels.find(l => l.degree_level_name === row.dataset.level)?.id_degree_level,
                degree_id: row.querySelector('[data-key="degree_course"]').dataset.degreeId || null,
                school_name: row.querySelector('[data-key="school_name"]').innerText,
                course_name: row.querySelector('[data-key="degree_course"]').innerText.split(' - ')[1] || '',
                degree_course: row.querySelector('[data-key="degree_course"]').innerText.split(' - ')[0] || '',
                period_from: row.querySelector('[data-key="period_from"]').innerText,
                period_to: row.querySelector('[data-key="period_to"]').innerText,
                highest_level_units: row.querySelector('[data-key="highest_level_units"]').innerText,
                year_graduated: row.querySelector('[data-key="year_graduated"]').innerText,
                awards: row.querySelector('[data-key="awards"]').innerText
            };
            openModal('Edit Education', data);
        });
    });
}

// ===== DELETE BUTTONS =====
function attachRowDeleteButtons(){
    document.querySelectorAll('.deleteWorkBtn').forEach(btn => {
        btn.addEventListener('click', e => {
            const row = e.target.closest('tr');
            const eduId = row.dataset.id;
            if(!eduId) return;

            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete this education record!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(async (result) => {
                if(result.isConfirmed){
                    try {
                        const res = await fetch(`<?= base_url('account/deleteEducation') ?>/${eduId}`, {
                            method: 'DELETE',
                            headers: {'X-Requested-With': 'XMLHttpRequest'}
                        });
                        const resultJson = await res.json();

                        if(resultJson.success){
                            closeModal(true); 
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Education deleted succesfully!',
                                showConfirmButton: false,
                                timer: 1000
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: resultJson.message,
                                showConfirmButton: false,
                                timer: 1000
                            });
                        }
                    } catch(err){
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete: ' + err.message,
                            showConfirmButton: false,
                            timer: 1000
                        });
                        console.error('Delete Error:', err);
                    }
                }
            });
        });
    });
}
// ===== SAVE EDUCATION WITH VALIDATION =====
form.addEventListener('submit', async e => {
    e.preventDefault();

    const requiredFields = [
        'degree_level_id',
        'school_name',
        'degree_id',
        'period_from',
        'period_to'
    ];

    // Check required fields — remove SweetAlert, just focus first empty field
    for(const name of requiredFields){
        const el = form[name];
        if(!el || el.value.trim() === ''){
            el.focus();
            return;
        }
    }

    // Year validation
    for(const name of yearFields){
        const val = form[name].value.trim().toUpperCase();
        if(val !== 'N/A' && !/^\d{4}$/.test(val)){
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Year',
                text: 'Year fields must be exactly 4 digits or N/A.',
                showConfirmButton: false,
                timer: 2000
            });
            form[name].focus();
            return;
        }
    }

    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries());
    if(editingRow) payload.id = editingRow.dataset.id;

    try {
        const res = await fetch('<?= base_url("account/updateEducation") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ education: [payload] })
        });

        const result = await res.json();

        if(result.success){
            closeModal(true);
            Swal.fire({
                icon: 'success',
                title: 'Saved!',
                text: isEditing
                    ? 'Education updated successfully!'
                    : 'Education added successfully!',
                showConfirmButton: false,
                timer: 1000
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message,
                showConfirmButton: false,
                timer: 1000
            });
        }

    } catch(err){
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to save education data: ' + err.message,
            showConfirmButton: false,
            timer: 1000
        });
        console.error('Education Save Error:', err);
    }
});


// ===== INITIAL ATTACH =====
attachRowEditButtons();
attachRowDeleteButtons();
</script>


<!-- === WORK EXPERIENCE TAB === -->
<div class="tab-content hidden" id="tab-work">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-bold text-clsuGreen">Work Experience</h2>
        <button id="addWorkBtn" class="bg-clsuGreen text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-green-800 transition-colors duration-200 flex items-center">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Work Experience
        </button>
    </div>

    <?php
    // Helper function to calculate formatted duration
    function calculate_duration($from_ts, $to_ts){
        $diffDays = ($to_ts - $from_ts)/86400 + 1;
        if ($diffDays < 30) return $diffDays . ' day' . ($diffDays > 1 ? 's' : '');
        $totalMonths = floor($diffDays/30);
        $yrs = floor($totalMonths/12);
        $mos = $totalMonths % 12;
        $days = $diffDays - ($yrs*365 + $mos*30);
        $text = '';
        if($yrs>0) $text .= $yrs.' yr'.($yrs>1?'s':'').' ';
        if($mos>0) $text .= $mos.' mo';
        if($days>0) $text .= ' '.$days.' day'.($days>1?'s':'');
        return trim($text);
    }
    ?>

    <!-- Professional Compact Work Experience Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="table-work">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Position Title</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Office / Company</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">From</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">To</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Status</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Govt Service</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Duration</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(!empty($workRecords)): ?>
                        <?php 
                        $totalDays = 0;
                        foreach($workRecords as $work): 
                            // Process work data
                            foreach (['position_title','office','status_of_appointment','govt_service'] as $key) {
                                if (empty($work[$key])) $work[$key] = '-';
                            }
                            $work['date_from_ts'] = (!empty($work['date_from']) && $work['date_from'] !== '0000-00-00') ? strtotime($work['date_from']) : null;
                            $work['date_to_ts'] = (!empty($work['date_to']) && $work['date_to'] !== '0000-00-00') ? strtotime($work['date_to']) : null;
                            $daysText = ($work['date_from_ts'] && $work['date_to_ts']) ? calculate_duration($work['date_from_ts'], $work['date_to_ts']) : '-';
                            
                            // Calculate total days for summary
                            $totalDays += ($work['date_from_ts'] && $work['date_to_ts']) ? ($work['date_to_ts'] - $work['date_from_ts'])/86400 + 1 : 0;
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors" data-id="<?= esc($work['id']) ?>">
                            <td class="px-3 py-2 border-b text-gray-800 font-medium" data-key="position_title"><?= esc($work['position_title']) ?></td>
                            <td class="px-3 py-2 border-b text-gray-700" data-key="office"><?= esc($work['office']) ?></td>
                            <td class="px-3 py-2 border-b text-gray-700 text-center" data-key="date_from" data-value="<?= $work['date_from'] ?>">
                                <?= !empty($work['date_from']) ? date('M j, Y', $work['date_from_ts']) : '-' ?>
                            </td>
                            <td class="px-3 py-2 border-b text-gray-700 text-center" data-key="date_to" data-value="<?= $work['date_to'] ?>">
                                <?= !empty($work['date_to']) ? date('M j, Y', $work['date_to_ts']) : '-' ?>
                            </td>
                            <td class="px-3 py-2 border-b text-gray-700" data-key="status_of_appointment"><?= esc($work['status_of_appointment']) ?></td>
                            <td class="px-3 py-2 border-b text-gray-700 text-center" data-key="govt_service"><?= esc($work['govt_service']) ?></td>
                            <td class="px-3 py-2 border-b text-gray-700 text-center font-medium" data-key="total_days"><?= $daysText ?></td>
                            <td class="px-3 py-2 border-b text-center">
                                <div class="flex justify-center gap-1">
                                    <button class="editWorkBtn inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors" data-id="<?= esc($work['id']) ?>">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button class="deleteWorkBtn inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors" data-id="<?= esc($work['id']) ?>">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <!-- Total Summary Row -->
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="6" class="px-3 py-2 border-b text-right text-gray-700">Total Work Duration:</td>
                            <td class="px-3 py-2 border-b text-center text-clsuGreen" data-key="total_duration">
                                <?php 
                                if ($totalDays > 0) {
                                    // Convert total days to formatted duration
                                    $totalText = '';
                                    $years = floor($totalDays / 365);
                                    $remainingDays = $totalDays % 365;
                                    $months = floor($remainingDays / 30);
                                    $days = $remainingDays % 30;
                                    
                                    if ($years > 0) $totalText .= $years . ' yr' . ($years > 1 ? 's' : '') . ' ';
                                    if ($months > 0) $totalText .= $months . ' mo' . ($months > 1 ? 's' : '') . ' ';
                                    if ($days > 0) $totalText .= $days . ' day' . ($days > 1 ? 's' : '');
                                    
                                    echo trim($totalText) ?: '0 days';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="px-3 py-2 border-b"></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-3 py-4 text-center text-gray-500 italic">No work experience found for this applicant.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- === Work Experience Modal === -->
<div id="editWorkModal"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 z-50">
    <div
        class="bg-white rounded-2xl w-11/12 max-w-md p-6 transform scale-95 opacity-0 transition-all duration-300">
         <h3 class="text-lg font-bold text-clsuGreen mb-1">Edit Work Experience</h3>
    <p class="text-[11px] text-gray-500 mb-3">
    All fields with <span class="text-red-500">*</span> are required. Enter <b>N/A</b> if not applicable.
</p>
   <form id="editWorkForm" class="space-y-3">
            <div>
                <label class="text-xs font-semibold">Position Title <span class="text-red-600">*</span></label>
                <input type="text" name="position_title" class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter position title" required>
            </div>
            <div>
                <label class="text-xs font-semibold">Office / Company <span class="text-red-600">*</span></label>
                <input type="text" name="office" class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter office or company" required>
            </div>
            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="text-xs font-semibold">From <span class="text-red-600">*</span></label>
                    <input type="date" name="date_from" class="w-full border px-2 py-1 rounded text-xs" required>
                </div>
                <div class="flex-1">
                    <label class="text-xs font-semibold">To <span class="text-red-600">*</span></label>
                    <input type="date" name="date_to" class="w-full border px-2 py-1 rounded text-xs" required>
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold">Status of Appointment <span class="text-red-600">*</span></label>
                <input type="text" name="status_of_appointment" class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter status" required>
            </div>
            <div>
                <label class="text-xs font-semibold">Government Service <span class="text-red-600">*</span></label>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('table-work');
    const tbody = table.querySelector('tbody'); 
    const modal = document.getElementById('editWorkModal');
    const modalBox = modal.querySelector('div');
    const cancelBtn = document.getElementById('cancelEditModal');
    const form = document.getElementById('editWorkForm');
    const addWorkBtn = document.getElementById('addWorkBtn');

    let tempAddData = {};     // Add modal typed data
    let tempEditData = {};    // Edit modal typed data
    let currentEditingRow = null;
    let isAddingNew = false;
    let resetNextAdd = true;
    const requiredFields = ['position_title','office','date_from','date_to','status_of_appointment','govt_service'];

function openModal(row = null) {
    const isEdit = !!row;
    isAddingNew = !isEdit;

    // Update modal title based on action
    const modalTitle = document.querySelector('#editWorkModal h3');
    modalTitle.textContent = isAddingNew ? 'Add Work Experience' : 'Edit Work Experience';

    // === CLEAR tempEditData if switching to a different row ===
    if (isEdit) {
        if (currentEditingRow !== row) {
            tempEditData = {}; // clear previous typed edit data
        }
    }

    currentEditingRow = row; // set AFTER clearing tempEditData

    if (isEdit) {
        // Edit row: get data from row
        const data = {
            position_title: row.querySelector('[data-key="position_title"]').textContent.trim(),
            office: row.querySelector('[data-key="office"]').textContent.trim(),
            date_from: row.querySelector('[data-key="date_from"]').dataset.value || row.querySelector('[data-key="date_from"]').textContent.trim(),
            date_to: row.querySelector('[data-key="date_to"]').dataset.value || row.querySelector('[data-key="date_to"]').textContent.trim(),
            status_of_appointment: row.querySelector('[data-key="status_of_appointment"]').textContent.trim(),
            govt_service: row.querySelector('[data-key="govt_service"]').textContent.trim()
        };

        // Use tempEditData if typed in this row, otherwise row data
        form.position_title.value = tempEditData.position_title ?? data.position_title;
        form.office.value = tempEditData.office ?? data.office;
        form.date_from.value = tempEditData.date_from ?? formatDateForInput(data.date_from);
        form.date_to.value = tempEditData.date_to ?? formatDateForInput(data.date_to);
        form.status_of_appointment.value = tempEditData.status_of_appointment ?? data.status_of_appointment;
        form.govt_service.value = tempEditData.govt_service ?? (data.govt_service !== '-' ? data.govt_service : '');
    } else {
        // Add modal - restore previously typed data or clear if none
        if (Object.keys(tempAddData).length > 0) {
            // Restore previously typed add data
            form.position_title.value = tempAddData.position_title ?? '';
            form.office.value = tempAddData.office ?? '';
            form.date_from.value = tempAddData.date_from ?? '';
            form.date_to.value = tempAddData.date_to ?? '';
            form.status_of_appointment.value = tempAddData.status_of_appointment ?? '';
            form.govt_service.value = tempAddData.govt_service ?? '';
        } else {
            // First time opening add modal - clear form
            form.reset();
        }
    }

    modal.classList.remove('opacity-0','pointer-events-none');
    setTimeout(()=>{ 
        modalBox.classList.remove('scale-95','opacity-0'); 
        modalBox.classList.add('scale-100','opacity-100'); 
    }, 10);
}

    function closeModal(clearTemp=false){ 
        modalBox.classList.add('scale-95','opacity-0'); 
        modal.classList.add('opacity-0'); 
        setTimeout(()=> modal.classList.add('pointer-events-none'), 300); 
        if(clearTemp){
            // Only clear data when Cancel button is clicked
            if(isAddingNew){
                tempAddData = {}; 
            } else {
                tempEditData = {}; 
            }
            form.reset();
            currentEditingRow = null;
        }
        // Don't clear data on accidental close - preserve typed information
    }

    // Cancel button - explicitly clear data
    cancelBtn.addEventListener('click', ()=> closeModal(true));
    
    // Accidental close (clicking overlay) - preserve data
    modal.addEventListener('click', e => { 
        if(e.target===modal) closeModal(false); 
    });
    
    // ESC key - preserve data (accidental close)
    document.addEventListener('keydown', e => {
        if(e.key === 'Escape' && !modal.classList.contains('opacity-0')) {
            closeModal(false);
        }
    });

    // Track input changes separately for Add and Edit
    form.querySelectorAll('input, select').forEach(el=>{
        el.addEventListener('input', e=>{
            if(isAddingNew){
                tempAddData[e.target.name] = e.target.value;
            } else {
                tempEditData[e.target.name] = e.target.value;
            }
        });
    });

    addWorkBtn.addEventListener('click', ()=> openModal());

    function formatDateForInput(dateStr){
        if(!dateStr||dateStr==='-') return '';
        const d = new Date(dateStr); if(isNaN(d)) return '';
        const m=('0'+(d.getMonth()+1)).slice(-2), day=('0'+d.getDate()).slice(-2);
        return `${d.getFullYear()}-${m}-${day}`;
    }

    
    function calculateDuration(from,to){
        if(!from||!to) return '-';
        const s=new Date(from), e=new Date(to); if(isNaN(s)||isNaN(e)||e<s) return '-';
        const diffDays=Math.floor((e-s)/(1000*60*60*24)) + 1;
        if(diffDays<30) return diffDays+' day'+(diffDays>1?'s':'');
        let totalMonths=Math.floor(diffDays/30);
        const yrs=Math.floor(totalMonths/12), mos=totalMonths%12;
        const days=diffDays - (yrs*365 + mos*30);
        let text='';
        if(yrs>0) text+= yrs+' yr'+(yrs>1?'s':'')+' ';
        if(mos>0) text+= mos+' mo';
        if(days>0) text+= ' '+days+' day'+(days>1?'s':'');
        return text.trim();
    }

    function updateTotalDuration(){
        // ===== SORT ROWS BY DATE_FROM DESCENDING (newest on top) =====
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a,b)=>{
            const dateA = new Date(a.querySelector('[data-key="date_from"]').dataset.value || a.querySelector('[data-key="date_from"]').textContent.trim());
            const dateB = new Date(b.querySelector('[data-key="date_from"]').dataset.value || b.querySelector('[data-key="date_from"]').textContent.trim());
            return dateB - dateA;
        });
        rows.forEach(r => tbody.appendChild(r));

        // ===== CALCULATE TOTAL =====
        if(rows.length === 0 || (rows.length === 1 && rows[0].querySelector('td').colSpan === 9)) {
            const tfoot = table.querySelector('tfoot');
            if(tfoot) tfoot.remove();
            return;
        }

        let totalDays = 0;
        rows.forEach(row => {
            const f = row.querySelector('[data-key="date_from"]').dataset.value || row.querySelector('[data-key="date_from"]').textContent.trim();
            const t = row.querySelector('[data-key="date_to"]').dataset.value || row.querySelector('[data-key="date_to"]').textContent.trim();
            const start = new Date(f), end = new Date(t);
            if(!isNaN(start) && !isNaN(end) && end >= start){
                totalDays += Math.floor((end - start)/(1000*60*60*24)) + 1;
            }
        });

        const yrs = Math.floor(totalDays / 365);
        const remDaysAfterYears = totalDays - (yrs * 365);
        const months = Math.floor(remDaysAfterYears / 30);
        const days = remDaysAfterYears - (months * 30);

        const parts = [];
        if(yrs > 0) parts.push(yrs + ' yr' + (yrs > 1 ? 's' : ''));
        if(months > 0) parts.push(months + ' month' + (months > 1 ? 's' : ''));
        if(days > 0) parts.push(days + ' day' + (days > 1 ? 's' : ''));
        const totalText = parts.join(' ');

        let tfoot = table.querySelector('tfoot');
        if(!tfoot){ tfoot = document.createElement('tfoot'); table.appendChild(tfoot); }
        tfoot.innerHTML = `
            <tr class="bg-gray-100 font-bold">
                <td colspan="6" class="px-1 py-1 border text-right">Total Work Duration:</td>
                <td class="px-1 py-1 border text-center" data-key="total_duration">${totalText || '-'}</td>
                <td class="px-1 py-1 border"></td>
            </tr>`;
    }

    // Update row total_days dynamically when editing modal
    form.addEventListener('input', ()=>{
        if(currentEditingRow){
            const f = form.date_from.value;
            const t = form.date_to.value;
            const duration = calculateDuration(f,t);
            currentEditingRow.querySelector('[data-key="total_days"]').textContent = duration;
            updateTotalDuration();
        }
    });

    // ===== EVENT DELEGATION FOR EDIT/DELETE =====
    table.addEventListener('click', async e=>{
        const editBtn = e.target.closest('.editWorkBtn');
        if(editBtn){ openModal(editBtn.closest('tr')); return; }

        const deleteBtn = e.target.closest('.deleteWorkBtn');
        if(deleteBtn){
            const row = deleteBtn.closest('tr'); 
            const id = row.dataset.id;
            if(!id) return;

            const result = await Swal.fire({
                title:'Are you sure?', 
                text:'This will permanently delete this work experience record!', 
                icon:'warning',
                showCancelButton:true, 
                confirmButtonColor:'#3085d6', 
                cancelButtonColor:'#d33', 
                confirmButtonText:'Yes, delete it!'
            });

            if(result.isConfirmed){
                try{
                    const res = await fetch(`<?= base_url('account/deleteWorkExperience') ?>/${id}`,{
                        method:'DELETE', 
                        headers:{'X-Requested-With':'XMLHttpRequest'}
                    });
                    const j = await res.json();
                    if(j.success){
                        Swal.fire({icon:'success',title:'Deleted!',text:j.message,showConfirmButton:false,timer:1000})
                        .then(()=> location.reload());
                    }
                    else Swal.fire('Error',j.message||'Unable to delete','error');
                }catch(err){ Swal.fire('Error','Failed: '+err.message,'error'); }
            }
        }
    });

    // ===== FORM SUBMIT WITH DATE VALIDATION =====
    form.addEventListener('submit', async e=>{
        e.preventDefault();

        const dateFrom = new Date(form.date_from.value);
        const dateTo = new Date(form.date_to.value);
        const today = new Date();

        if(isNaN(dateFrom) || isNaN(dateTo)){
            Swal.fire({icon:'error',title:'Invalid Date',text:'Please enter valid dates.',showConfirmButton:true});
            return;
        }
        if(dateTo < dateFrom){
            Swal.fire({icon:'error',title:'Invalid Date Range',text:'Check the date range!',showConfirmButton:true});
            return;
        }
        if(dateFrom > today || dateTo > today){
            Swal.fire({icon:'error',title:'Future Date Not Allowed',text:'Check the date range!',showConfirmButton:true});
            return;
        }

        const fd = new FormData(form);
        if(!isAddingNew && currentEditingRow) fd.append('id', currentEditingRow.dataset.id);

        try{
            const res = await fetch('<?= base_url("account/updateWorkExperience") ?>',{
                method:'POST',
                body:fd,
                headers:{'X-Requested-With':'XMLHttpRequest'}
            });
            const j = await res.json();
            if(j.success){
                Swal.fire({icon:'success',title:'Saved!',text:j.message,showConfirmButton:false,timer:1200})
                .then(()=> location.reload()); // table updates after save
            } else Swal.fire('Error',j.message||'Unable to save','error');
        }catch(err){
            Swal.fire('Error','Something went wrong: '+err.message,'error');
        }
    });

    // ===== INITIAL TOTAL DURATION =====
    updateTotalDuration();
});
</script>

<div class="tab-content hidden" id="tab-civil">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-bold text-clsuGreen">Civil Service Eligibility</h2>
        <button id="addCivilBtn" class="bg-clsuGreen text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-green-800 transition-colors duration-200 flex items-center">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Civil Service
        </button>
    </div>

    <!-- Professional Compact Civil Service Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="table-civil">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Eligibility</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Rating</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Exam Date</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Place of Exam</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">License No.</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Valid Until</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Certificate</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($civilRecords)): ?>
                        <?php
                        // ✅ SORT: newest date_of_exam FIRST
                        usort($civilRecords, function ($a, $b) {
                            $dateA = (!empty($a['date_of_exam']) && $a['date_of_exam'] !== '0000-00-00')
                                ? strtotime($a['date_of_exam'])
                                : -INF;
                        
                            $dateB = (!empty($b['date_of_exam']) && $b['date_of_exam'] !== '0000-00-00')
                                ? strtotime($b['date_of_exam'])
                                : -INF;
                        
                            return $dateB <=> $dateA; // DESC
                        });
                        ?>
                        
                        <?php foreach ($civilRecords as $civil): ?>
                            <?php
                            // Store raw dates for modal use
                            $civil['date_of_exam_raw'] = $civil['date_of_exam'];
                            $civil['license_valid_until_raw'] = $civil['license_valid_until'];
                            
                            // ✅ Safe date formatting
                            $civil['date_of_exam'] =
                                (!empty($civil['date_of_exam']) && $civil['date_of_exam'] !== '0000-00-00' && strtotime($civil['date_of_exam']))
                                    ? date('M j, Y', strtotime($civil['date_of_exam']))
                                    : '-';
                        
                            $civil['license_valid_until'] =
                                (!empty($civil['license_valid_until']) && $civil['license_valid_until'] !== '0000-00-00' && strtotime($civil['license_valid_until']))
                                    ? date('M j, Y', strtotime($civil['license_valid_until']))
                                    : '-';
                        
                            $civil['eligibility']     = !empty($civil['eligibility']) ? $civil['eligibility'] : '-';
                            $civil['rating']          = !empty($civil['rating']) ? $civil['rating'] : '-';
                            $civil['place_of_exam']   = !empty($civil['place_of_exam']) ? $civil['place_of_exam'] : '-';
                            $civil['license_no']      = !empty($civil['license_no']) ? $civil['license_no'] : '-';
                            // ✅ Do not overwrite certificate, keep NULL if none
                            ?>
                        
                            <tr class="hover:bg-gray-50 transition-colors" data-id="<?= esc($civil['id']) ?>">
                                <td class="px-3 py-2 border-b text-gray-800 font-medium" data-key="eligibility"><?= esc($civil['eligibility']) ?></td>
                                <td class="px-3 py-2 border-b text-gray-700 text-center" data-key="rating"><?= esc($civil['rating']) ?></td>
                                <td class="px-3 py-2 border-b text-gray-700 text-center" data-key="date_of_exam" data-value="<?= esc($civil['date_of_exam_raw']) ?>"><?= esc($civil['date_of_exam']) ?></td>
                                <td class="px-3 py-2 border-b text-gray-700" data-key="place_of_exam"><?= esc($civil['place_of_exam']) ?></td>
                                <td class="px-3 py-2 border-b text-gray-700 text-center" data-key="license_no"><?= esc($civil['license_no']) ?></td>
                                <td class="px-3 py-2 border-b text-gray-700 text-center" data-key="license_valid_until" data-value="<?= esc($civil['license_valid_until_raw']) ?>"><?= esc($civil['license_valid_until']) ?></td>
                                <td class="px-3 py-2 border-b text-center" data-key="certificate">
                                    <?php if (!empty($civil['certificate'])): ?>
                                        <button class="viewCertificateBtn inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                            data-file="<?= esc($civil['certificate']) ?>">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </button>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 py-2 border-b text-center">
                                    <div class="flex justify-center gap-1">
                                        <button class="editCivilBtn inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button class="deleteCivilBtn inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="noCivilRow">
                            <td class="px-3 py-4 text-center text-gray-500 italic" colspan="8">
                                No civil service found for this applicant.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="editCivilModal"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 z-50">
    <div class="bg-white rounded-2xl w-11/12 max-w-md p-6 transform scale-95 opacity-0 transition-all duration-300">
        <h3 class="text-lg font-bold text-clsuGreen mb-1">Civil Service</h3>
        <p class="text-[11px] text-gray-500 mb-3">
            All fields with <span class="text-red-500">*</span> are required. Enter <b>N/A</b> if not applicable.
        </p>
        <form id="editCivilForm" class="space-y-3" enctype="multipart/form-data">
            <input type="hidden" name="id" value="">

            <div>
                <label class="text-xs font-semibold">Eligibility <span class="text-red-500">*</span></label>
                <input type="text" name="eligibility" placeholder="Enter eligibility" required class="w-full border px-2 py-1 rounded text-xs">
            </div>

            <div>
                <label class="text-xs font-semibold">Rating / Exam <span class="text-red-500">*</span></label>
                <input type="text" name="rating" placeholder="Enter rating or exam" required class="w-full border px-2 py-1 rounded text-xs">
            </div>

            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="text-xs font-semibold">Date of Examination <span class="text-red-500">*</span></label>
                    <input type="date" name="date_of_exam" required class="w-full border px-2 py-1 rounded text-xs">
                </div>
                <div class="flex-1">
                    <label class="text-xs font-semibold">Valid Until (Optional)</label>
                    <input type="date" name="license_valid_until" class="w-full border px-2 py-1 rounded text-xs">
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold">Place of Examination <span class="text-red-500">*</span></label>
                <input type="text" name="place_of_exam" required class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter place of examination">
            </div>

            <div>
                <label class="text-xs font-semibold">License / PRC No. <span class="text-red-500">*</span></label>
                <input type="text" name="license_no" required class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter license or PRC number">
            </div>

            <div>
                <label class="text-xs font-semibold">Certificate (Upload PDF files only) <span class="text-red-500">*</span></label>
           <input type="file" name="certificate" accept=".pdf" class="w-full border px-2 py-1 rounded text-xs">
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('table-civil');
    const editModal = document.getElementById('editCivilModal');
    const modalBox = editModal.querySelector('div');
    const cancelBtn = document.getElementById('cancelCivilModal');
    const editForm = document.getElementById('editCivilForm');
    const addCivilBtn = document.getElementById('addCivilBtn');
    const certificateInput = editForm.querySelector('input[name="certificate"]');

    // ===== Certificate Viewer =====
    const certViewer = document.createElement('div');
    certViewer.id = 'certificateViewer';
    certViewer.className = 'hidden fixed inset-0 bg-gray-800 bg-opacity-90 z-50 flex items-center justify-center p-4';
    certViewer.innerHTML = `
        <div class="bg-white rounded-xl w-full max-w-6xl h-full relative flex flex-col shadow-lg">
            <iframe id="viewerFrame" src="" class="flex-1 w-full h-full border-none"></iframe>
        </div>
    `;
    document.body.appendChild(certViewer);
    const viewerFrame = document.getElementById('viewerFrame');
    
function openCertViewer(fileName) {
    if (!fileName || fileName.trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'No Certificate Available',
            text: 'No civil service certificate has been uploaded for this record.',
            showConfirmButton: false,
            timer: 1000
        });
        return;
    }

    // Show loading alert first
    Swal.fire({
        title: 'Loading...',
        text: 'Please wait while the certificate loads.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();

            // Slight delay before fetching
            setTimeout(() => {
                fetch('<?= base_url("account/viewCivilCertificate/") ?>' + encodeURIComponent(fileName), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    // Check if JSON returned → file missing or deleted
                    const contentType = response.headers.get('content-type') || '';
                    if (contentType.includes('application/json')) {
                        const data = await response.json();
                        Swal.close();
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Certificate Available',
                            text: data.message || 'No civil service certificate has been uploaded for this record.',
                            showConfirmButton: false,
                            timer: 1000
                        });
                        throw new Error('File not available');
                    }
                    return response.blob(); // File exists
                })
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    viewerFrame.src = url;
                    certViewer.classList.remove('hidden');
                    Swal.close(); // close loading alert
                })
                .catch(err => {
                    console.warn(err);
                });
            }, 500); // delay
        }
    });
}
    function closeCertViewer(){
        viewerFrame.src = '';
        certViewer.classList.add('hidden');
    }
    certViewer.addEventListener('click', e => { if(e.target === certViewer) closeCertViewer(); });

    // ===== Modal State =====
    let currentEditingRowId = null;
    let isAddMode = false;
    let tempAddData = {};            // Typed data in Add modal
    let tempEditDataById = {};       // Typed data in Edit modal by row id
    let originalEditData = null;

    function formatToInputDate(displayDate) {
        if (!displayDate || displayDate === '-' || displayDate === 'N/A') return '';
        
        // Handle database format (YYYY-MM-DD)
        if (displayDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
            return displayDate; // Already in correct format
        }
        
        // Handle display format (Month DD, YYYY)
        const parts = displayDate.split(' ');
        if (parts.length !== 3) return '';
        const monthNames = {
            January: '01', February: '02', March: '03', April: '04',
            May: '05', June: '06', July: '07', August: '08',
            September: '09', October: '10', November: '11', December: '12'
        };
        const month = monthNames[parts[0]];
        const day = parts[1].replace(',', '').padStart(2,'0');
        const year = parts[2];
        return `${year}-${month}-${day}`;
    }

    function openModal(row = null){
        const isEdit = !!row;
        isAddMode = !isEdit;
    
        // Update modal title based on action
        const modalTitle = document.querySelector('#editCivilModal h3');
        modalTitle.textContent = isEdit ? 'Edit Civil Service' : 'Add Civil Service';
    
        if(isEdit){
            const rowId = row.dataset.id;
            currentEditingRowId = rowId;
            certificateInput.removeAttribute('required'); // optional
    
            originalEditData = {
                id: rowId,
                eligibility: row.querySelector('[data-key="eligibility"]').textContent.trim(),
                rating: row.querySelector('[data-key="rating"]').textContent.trim(),
                date_of_exam: row.querySelector('[data-key="date_of_exam"]').dataset.value || row.querySelector('[data-key="date_of_exam"]').textContent,
                license_valid_until: row.querySelector('[data-key="license_valid_until"]').dataset.value || row.querySelector('[data-key="license_valid_until"]').textContent,
                place_of_exam: row.querySelector('[data-key="place_of_exam"]').textContent.trim(),
                license_no: row.querySelector('[data-key="license_no"]').textContent.trim()
            };
    
            // Initialize tempEditData for this row if not exists
            if(!tempEditDataById[rowId]) tempEditDataById[rowId] = {};
    
            const tempEditData = tempEditDataById[rowId];
    
            // Prefill form: tempEditData if exists, otherwise original
            editForm.id.value = originalEditData.id;
            editForm.eligibility.value = tempEditData.eligibility ?? originalEditData.eligibility;
            editForm.rating.value = tempEditData.rating ?? originalEditData.rating;
            editForm.date_of_exam.value = tempEditData.date_of_exam ?? (originalEditData.date_of_exam !== '-' ? formatToInputDate(originalEditData.date_of_exam) : '');
            editForm.license_valid_until.value = tempEditData.license_valid_until ?? (originalEditData.license_valid_until !== '-' ? formatToInputDate(originalEditData.license_valid_until) : '');
            editForm.place_of_exam.value = tempEditData.place_of_exam ?? originalEditData.place_of_exam;
            editForm.license_no.value = tempEditData.license_no ?? originalEditData.license_no;
    
        } else {
            // === Add mode ===
            currentEditingRowId = null;
            editForm.reset();
            editForm.id.value = '';
            certificateInput.setAttribute('required','required');
    
            editForm.eligibility.value = tempAddData.eligibility ?? '';
            editForm.rating.value = tempAddData.rating ?? '';
            editForm.date_of_exam.value = tempAddData.date_of_exam ?? '';
            editForm.license_valid_until.value = tempAddData.license_valid_until ?? '';
            editForm.place_of_exam.value = tempAddData.place_of_exam ?? '';
            editForm.license_no.value = tempAddData.license_no ?? '';
        }
    
        editModal.classList.remove('pointer-events-none','opacity-0');
        setTimeout(()=> modalBox.classList.remove('scale-95','opacity-0'),10);
    }

    function closeModal(){
        modalBox.classList.add('scale-95','opacity-0');
        editModal.classList.add('opacity-0');
        setTimeout(()=> editModal.classList.add('pointer-events-none'),300);
        currentEditingRowId = null;
    }

    // ===== Track typed data =====
    editForm.querySelectorAll('input, select').forEach(el=>{
        el.addEventListener('input', e=>{
            if(isAddMode){
                tempAddData[e.target.name] = e.target.value;
            } else if(currentEditingRowId){
                if(!tempEditDataById[currentEditingRowId]) tempEditDataById[currentEditingRowId] = {};
                tempEditDataById[currentEditingRowId][e.target.name] = e.target.value;
            }
        });
    });

    addCivilBtn.addEventListener('click', ()=>openModal());

    cancelBtn.addEventListener('click', ()=>{
        if(isAddMode){
            editForm.reset();
        } else if(currentEditingRowId && originalEditData){
            // Restore original data
            editForm.id.value = originalEditData.id;
            editForm.eligibility.value = originalEditData.eligibility;
            editForm.rating.value = originalEditData.rating;
            editForm.date_of_exam.value = originalEditData.date_of_exam !== 'N/A' ? formatToInputDate(originalEditData.date_of_exam) : '';
            editForm.license_valid_until.value = originalEditData.license_valid_until !== 'N/A' ? formatToInputDate(originalEditData.license_valid_until) : '';
            editForm.place_of_exam.value = originalEditData.place_of_exam;
            editForm.license_no.value = originalEditData.license_no;

            // Clear tempEditData for this row
            if(tempEditDataById[currentEditingRowId]) tempEditDataById[currentEditingRowId] = {};
        }
        closeModal();
    });

    editModal.addEventListener('click', e => { if(e.target===editModal) closeModal(); });

    // ===== Table Actions =====
    table.addEventListener('click', e=>{
        const editBtn = e.target.closest('.editCivilBtn');
        const deleteBtn = e.target.closest('.deleteCivilBtn');
        const viewBtn = e.target.closest('.viewCertificateBtn');

        if(editBtn) openModal(editBtn.closest('tr'));
        if(viewBtn) openCertViewer(viewBtn.dataset.file);

        if(deleteBtn){
            const row = deleteBtn.closest('tr');
            const id = row.dataset.id;
            Swal.fire({
                title:'Are you sure?',
                text:'This will permanently delete this civil service record!',
                icon:'warning',
                showCancelButton:true,
                confirmButtonColor:'#d33',
                cancelButtonColor:'#3085d6',
                confirmButtonText:'Yes, delete it!'
            }).then(result=>{
                if(result.isConfirmed){
                    fetch('<?= base_url("account/deleteCivilService") ?>/'+id,{
                        method:'DELETE',
                        headers:{'X-Requested-With':'XMLHttpRequest'}
                    }).then(r=>r.json())
                      .then(res=>{
                          if(res.success){
                              Swal.fire({icon:'success',title:'Deleted!',text:res.message,timer:1200,showConfirmButton:false})
                              .then(()=> location.reload());
                          }else{
                              Swal.fire('Error',res.message,'error');
                          }
                      });
                }
            });
        }
    });

    // ===== Form Submit =====
    editForm.addEventListener('submit', e=>{
        e.preventDefault();

        const requiredFields = ['eligibility','rating','date_of_exam','place_of_exam','license_no'];
        for(const field of requiredFields){
            if(!editForm[field].value.trim()){
                Swal.fire({icon:'warning',title:'Required Field',text:'Please fill out all required fields (*)'});
                editForm[field].focus();
                return;
            }
        }

        // Validate Date of Examination - must not be future date
        const examDate = new Date(editForm.date_of_exam.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Reset time portion for accurate comparison
        
        if (examDate > today) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Date',
                text: 'Date of Examination cannot be in the future.'
            });
            editForm.date_of_exam.focus();
            return;
        }

        if(isAddMode && !certificateInput.value){
            Swal.fire({icon:'warning',title:'Required Field',text:'Please upload a certificate.'});
            certificateInput.focus();
            return;
        }

        // ===== Validate PDF only =====
const file = certificateInput.files[0];

if (file) {
    const allowedTypes = ['application/pdf'];
    const fileExtension = file.name.split('.').pop().toLowerCase();

    if (!allowedTypes.includes(file.type) || fileExtension !== 'pdf') {
        Swal.fire({
            icon: 'error',
            title: 'Invalid File',
            text: 'Only PDF files are allowed.'
        });
        certificateInput.value = '';
        return;
    }

    // Optional: Limit file size (e.g., 5MB)
    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        Swal.fire({
            icon: 'error',
            title: 'File Too Large',
            text: 'PDF must be 5MB or less.'
        });
        certificateInput.value = '';
        return;
    }
}

        const formData = new FormData(editForm);
        fetch('<?= base_url("account/updateCivilService") ?>',{
            method:'POST',
            body:formData,
            headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(r=>r.json())
        .then(res=>{
            if(res.success){
                Swal.fire({icon:'success',title:'Saved!',text:res.message,timer:1200,showConfirmButton:false})
                .then(()=> location.reload());
            }else{
                // Check if authentication is required
                if(res.auth_required && res.auth_url){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Google Drive Authentication Required',
                        text: res.message,
                        showCancelButton: true,
                        confirmButtonText: 'Connect Google Drive',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to Google Drive authentication
                            window.location.href = res.auth_url;
                        }
                    });
                } else {
                    Swal.fire('Error',res.message,'error');
                }
            }
        });
    });
});
</script>

<div class="tab-content hidden" id="tab-training">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-bold text-clsuGreen">Trainings</h2>
        <button id="addTrainingBtn" class="bg-clsuGreen text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-green-800 transition-colors duration-200 flex items-center">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Trainings
        </button>
    </div>

    <!-- Professional Compact Trainings Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="table-training">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Training Name</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Category</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Venue</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">From - To</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Facilitator</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Sponsor</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Hours</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Remarks</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Certificate</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Training Duration</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(!empty($trainingRecords)): ?>
                        <?php foreach($trainingRecords as $training): ?>
                        <tr class="hover:bg-gray-50 transition-colors"
                            data-id="<?= esc($training['id_applicant_training']) ?>"
                            data-date_from="<?= esc($training['date_from']) ?>"
                            data-date_to="<?= esc($training['date_to']) ?>"
                        >
                            <td class="px-3 py-2 border-b text-gray-800 font-medium" data-key="training_name"><?= esc($training['training_name']) ?></td>
                            <td class="px-3 py-2 border-b text-gray-700" data-key="training_category_id"><?= esc($training['training_category_name']) ?></td>
                            <td class="px-3 py-2 border-b text-gray-700" data-key="training_venue"><?= esc($training['training_venue']) ?></td>
                            <td class="px-3 py-2 border-b text-gray-700 text-center" data-key="date_range">
                                <?= esc($training['date_from_formatted'] ?? 'N/A') ?> - <?= esc($training['date_to_formatted'] ?? 'N/A') ?>
                            </td>
                            <td class="px-3 py-2 border-b text-gray-700" data-key="training_facilitator"><?= esc($training['training_facilitator']) ?></td>
                            <td class="px-3 py-2 border-b text-gray-700" data-key="training_sponsor"><?= esc($training['training_sponsor']) ?></td>
                            <td class="px-3 py-2 border-b text-gray-700 text-center" data-key="training_hours"><?= esc($training['training_hours']) ?></td>
                            <td class="px-3 py-2 border-b text-gray-700" data-key="training_remarks"><?= esc($training['training_remarks']) ?></td>
                            <td class="px-3 py-2 border-b text-center">
                                <?php if(!empty($training['certificate_file'])): ?>
                                    <button class="viewCertificateBtn inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors" 
                                            data-file="<?= esc($training['certificate_file']) ?>">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-2 border-b text-center text-gray-700" data-key="training_duration">
                                <?= esc($training['training_duration'] ?? '-') ?>
                            </td>
                            <td class="px-3 py-2 border-b text-center">
                                <div class="flex justify-center gap-1">
                                    <button class="editTrainingBtn inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button class="deleteTrainingBtn inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- Total Duration Row -->
                        <tr class="bg-gray-50 font-semibold">
                            <td class="px-3 py-2 border-b text-right text-gray-700" colspan="9">Total Duration:</td>
                            <td class="px-3 py-2 border-b text-center text-clsuGreen font-bold" colspan="2"><?= esc($totalTrainingDuration ?? '-') ?></td>
                        </tr>

                    <?php else: ?>
                        <tr>
                            <td class="px-3 py-4 text-center text-gray-500 italic" colspan="11">
                                No trainings found for this applicant.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="editTrainingModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 z-50">
    <div class="bg-white rounded-2xl w-11/12 max-w-2xl p-6 transform scale-95 opacity-0 transition-all duration-300">
        <h3 class="text-lg font-bold text-clsuGreen mb-1">Training</h3>
        <p class="text-[11px] text-gray-500 mb-3">
            All fields with <span class="text-red-500">*</span> are required. Enter <b>N/A</b> if not applicable.
        </p>
        <form id="editTrainingForm" class="space-y-3" enctype="multipart/form-data">
            <input type="hidden" name="id" value="">

            <!-- Row 1: Category & Training Name -->
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="text-xs font-semibold">Category <span class="text-red-500">*</span></label>
                    <select name="training_category_id" required class="w-full border px-2 py-1 rounded text-xs">
                        <option value="">Select category</option>
                        <?php foreach($trainingCategories as $cat): ?>
                            <option value="<?= esc($cat['id_training_category']) ?>"><?= esc($cat['training_category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold">Training Name <span class="text-red-500">*</span></label>
                    <input type="text" name="training_name" placeholder="Enter training name" required class="w-full border px-2 py-1 rounded text-xs">
                </div>
            </div>

            <!-- Row 1.5: Training Venue -->
            <div class="mb-3">
                <label class="text-xs font-semibold">Venue <span class="text-red-500">*</span></label>
                <input type="text" name="training_venue" placeholder="Enter venue" required class="w-full border px-2 py-1 rounded text-xs">
            </div>

            <!-- Row 2: Facilitator & Sponsor -->
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="text-xs font-semibold">Facilitator <span class="text-red-500">*</span></label>
                    <input type="text" name="training_facilitator" placeholder="Enter facilitator" required class="w-full border px-2 py-1 rounded text-xs">
                </div>
                <div>
                    <label class="text-xs font-semibold">Sponsor <span class="text-red-500">*</span></label>
                    <input type="text" name="training_sponsor" placeholder="Enter sponsor" required class="w-full border px-2 py-1 rounded text-xs">
                </div>
            </div>

            <!-- Row 3: Training From | Training To | Hours -->
            <div class="grid grid-cols-3 gap-4 mb-3">
                <div>
                    <label class="text-xs font-semibold">Training From <span class="text-red-500">*</span></label>
                    <input type="date" name="date_from" placeholder="dd/mm/yyyy" required class="w-full border px-2 py-1 rounded text-xs">
                </div>
                <div>
                    <label class="text-xs font-semibold">Training To <span class="text-red-500">*</span></label>
                    <input type="date" name="date_to" placeholder="dd/mm/yyyy" required class="w-full border px-2 py-1 rounded text-xs">
                </div>
                <div>
                    <label class="text-xs font-semibold">Hours <span class="text-red-500">*</span></label>
                    <input type="number" name="training_hours" placeholder="Enter hours" required class="w-full border px-2 py-1 rounded text-xs">
                </div>
            </div>

            <!-- Row 4: Remarks -->
            <div class="mb-3">
                <label class="text-xs font-semibold">Remarks <span class="text-red-500">*</span></label>
                <input type="text" name="training_remarks" placeholder="Enter remarks" required class="w-full border px-2 py-1 rounded text-xs">
            </div>

            <!-- Row 5: Certificate File -->
            <div class="mb-3">
                <label class="text-xs font-semibold">Certificate File  (Upload PDF files only)<span class="text-red-500">*</span></label>
       <input type="file"
       name="training_certificate_file"accept=".pdf" required class="w-full border px-2 py-1 rounded text-xs">
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-3">
                <button type="button" id="cancelTrainingModal" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition-colors">Cancel</button>
                <button type="submit" class="bg-clsuGreen hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition-colors">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Certificate Modal -->
<div id="certificateModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-90 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-6xl h-[90vh] relative flex flex-col shadow-lg">
        <!-- Toolbar -->
        <div class="flex items-center justify-between px-4 py-2 border-b bg-gray-50">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-semibold text-gray-700">Training Certificate</h3>
            </div>
            <div class="flex items-center gap-2">
                <button id="certZoomOut" class="px-2 py-1 text-xs bg-white border border-gray-300 rounded hover:bg-gray-100 transition-colors" title="Zoom Out">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                    </svg>
                </button>
                <button id="certZoomIn" class="px-2 py-1 text-xs bg-white border border-gray-300 rounded hover:bg-gray-100 transition-colors" title="Zoom In">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m4.5-3H13.5m6.5 0a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z"></path>
                    </svg>
                </button>
                <button id="certZoomReset" class="px-2 py-1 text-xs bg-white border border-gray-300 rounded hover:bg-gray-100 transition-colors" title="Reset Zoom">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                </button>
                <span id="certZoomLevel" class="text-xs text-gray-600 min-w-[40px]">100%</span>
            </div>
            <div class="flex items-center gap-2">
                <button id="certDownload" class="px-2 py-1 text-xs bg-clsuGreen text-white rounded hover:bg-green-800 transition-colors" title="Download">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </button>
                <button id="certPrint" class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors" title="Print">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                </button>
                <button id="certFullscreen" class="px-2 py-1 text-xs bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors" title="Fullscreen">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                </button>
                <button id="certCloseModal" class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition-colors" title="Close">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <!-- PDF Viewer Container -->
        <div id="certPdfContainer" class="flex-1 overflow-auto bg-gray-200">
            <iframe id="certificateFrame" src="" class="w-full h-full border-none"></iframe>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('table-training');
    const editModal = document.getElementById('editTrainingModal');
    const modalBox = editModal.querySelector('div');
    const cancelBtn = document.getElementById('cancelTrainingModal');
    const editForm = document.getElementById('editTrainingForm');
    const addBtn = document.getElementById('addTrainingBtn');
    const certificateInput = editForm.querySelector('input[name="training_certificate_file"]');

    const certificateModal = document.getElementById('certificateModal');
    const certificateFrame = document.getElementById('certificateFrame');
    const certPdfContainer = document.getElementById('certPdfContainer');
    
    // Certificate viewer state
    let certCurrentZoom = 100;
    let certCurrentFileUrl = '';
    let certCurrentFileName = '';

    // Toolbar button handlers
    const certZoomInBtn = document.getElementById('certZoomIn');
    const certZoomOutBtn = document.getElementById('certZoomOut');
    const certZoomResetBtn = document.getElementById('certZoomReset');
    const certDownloadBtn = document.getElementById('certDownload');
    const certPrintBtn = document.getElementById('certPrint');
    const certFullscreenBtn = document.getElementById('certFullscreen');
    const certCloseModalBtn = document.getElementById('certCloseModal');
    const certZoomLevelSpan = document.getElementById('certZoomLevel');

    if (certZoomInBtn) {
        certZoomInBtn.addEventListener('click', () => {
            certCurrentZoom = Math.min(certCurrentZoom + 25, 200);
            certPdfContainer.style.transform = `scale(${certCurrentZoom / 100})`;
            certPdfContainer.style.transformOrigin = 'top center';
            certZoomLevelSpan.textContent = `${certCurrentZoom}%`;
        });
    }

    if (certZoomOutBtn) {
        certZoomOutBtn.addEventListener('click', () => {
            certCurrentZoom = Math.max(certCurrentZoom - 25, 50);
            certPdfContainer.style.transform = `scale(${certCurrentZoom / 100})`;
            certPdfContainer.style.transformOrigin = 'top center';
            certZoomLevelSpan.textContent = `${certCurrentZoom}%`;
        });
    }

    if (certZoomResetBtn) {
        certZoomResetBtn.addEventListener('click', () => {
            certCurrentZoom = 100;
            certPdfContainer.style.transform = 'scale(1)';
            certPdfContainer.style.transformOrigin = 'top center';
            certZoomLevelSpan.textContent = '100%';
        });
    }

    if (certDownloadBtn) {
        certDownloadBtn.addEventListener('click', () => {
            if (certCurrentFileUrl) {
                const a = document.createElement('a');
                a.href = certCurrentFileUrl;
                a.download = certCurrentFileName || 'training_certificate.pdf';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }
        });
    }

    if (certPrintBtn) {
        certPrintBtn.addEventListener('click', () => {
            if (certificateFrame.src) {
                certificateFrame.contentWindow.print();
            }
        });
    }

    if (certFullscreenBtn) {
        certFullscreenBtn.addEventListener('click', () => {
            const modal = certificateModal;
            if (!document.fullscreenElement) {
                if (modal.requestFullscreen) {
                    modal.requestFullscreen();
                } else if (modal.webkitRequestFullscreen) {
                    modal.webkitRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        });
    }

    if (certCloseModalBtn) {
        certCloseModalBtn.addEventListener('click', () => {
            certificateFrame.src = '';
            certificateModal.classList.add('hidden');
            certCurrentZoom = 100;
            certPdfContainer.style.transform = 'scale(1)';
            certZoomLevelSpan.textContent = '100%';
        });
    }

    let tempAddData = {};              // typed Add modal data
    let tempEditDataById = {};         // typed Edit modal data per row id
    let currentEditingRowId = null;
    let isAddMode = false;
    let originalEditData = null;

    // ===== Track typed data =====
    editForm.querySelectorAll('input, select').forEach(el => {
        el.addEventListener('input', e => {
            if(isAddMode){
                tempAddData[e.target.name] = e.target.value;
            } else if(currentEditingRowId){
                if(!tempEditDataById[currentEditingRowId]) tempEditDataById[currentEditingRowId] = {};
                tempEditDataById[currentEditingRowId][e.target.name] = e.target.value;
            }
        });
    });

    // ===== Open Add/Edit Modal =====
    function openModal(row = null) {
        currentEditingRowId = row ? row.dataset.id : null;
        isAddMode = !row;

        // Update modal title based on action
        const modalTitle = document.querySelector('#editTrainingModal h3');
        modalTitle.textContent = row ? 'Edit Trainings' : 'Add Trainings';

        if(row){ // Edit mode
            certificateInput.removeAttribute('required');

            originalEditData = {
                id: row.dataset.id,
                training_name: row.querySelector('[data-key="training_name"]').textContent.trim(),
                training_venue: row.querySelector('[data-key="training_venue"]').textContent.trim(),
                training_facilitator: row.querySelector('[data-key="training_facilitator"]').textContent.trim(),
                date_from: row.dataset.date_from || '',
                date_to: row.dataset.date_to || '',
                training_hours: row.querySelector('[data-key="training_hours"]').textContent.trim(),
                training_sponsor: row.querySelector('[data-key="training_sponsor"]').textContent.trim(),
                training_remarks: row.querySelector('[data-key="training_remarks"]').textContent.trim(),
                training_category_id: row.querySelector('[data-key="training_category_id"]').textContent.trim()
            };

            const tempData = tempEditDataById[currentEditingRowId] || {};

            editForm.id.value = originalEditData.id;
            editForm.training_name.value = tempData.training_name ?? originalEditData.training_name;
            editForm.training_venue.value = tempData.training_venue ?? originalEditData.training_venue;
            editForm.training_facilitator.value = tempData.training_facilitator ?? originalEditData.training_facilitator;
            editForm.date_from.value = tempData.date_from ?? originalEditData.date_from;
            editForm.date_to.value = tempData.date_to ?? originalEditData.date_to;
            editForm.training_hours.value = tempData.training_hours ?? originalEditData.training_hours;
            editForm.training_sponsor.value = tempData.training_sponsor ?? originalEditData.training_sponsor;
            editForm.training_remarks.value = tempData.training_remarks ?? originalEditData.training_remarks;

            editForm.training_category_id.querySelectorAll('option').forEach(opt => {
                opt.selected = (opt.textContent.trim() === (tempData.training_category_id ?? originalEditData.training_category_id));
            });

        } else { // Add mode
            editForm.reset();
            editForm.id.value = '';
            certificateInput.setAttribute('required','required');

            // Prefill with tempAddData if any
            editForm.training_name.value = tempAddData.training_name ?? '';
            editForm.training_venue.value = tempAddData.training_venue ?? '';
            editForm.training_facilitator.value = tempAddData.training_facilitator ?? '';
            editForm.date_from.value = tempAddData.date_from ?? '';
            editForm.date_to.value = tempAddData.date_to ?? '';
            editForm.training_hours.value = tempAddData.training_hours ?? '';
            editForm.training_sponsor.value = tempAddData.training_sponsor ?? '';
            editForm.training_remarks.value = tempAddData.training_remarks ?? '';
            editForm.training_category_id.querySelectorAll('option').forEach(opt => {
                opt.selected = (opt.textContent.trim() === (tempAddData.training_category_id ?? ''));
            });
        }

        editModal.classList.remove('pointer-events-none','opacity-0');
        setTimeout(() => modalBox.classList.remove('scale-95','opacity-0'), 10);
    }

    // ===== Close Modal =====
    function closeModal(discardTyped = false) {
        // Only remove typed data if closing via Cancel button
        if(discardTyped){
            if(isAddMode) tempAddData = {};
            else if(currentEditingRowId && tempEditDataById[currentEditingRowId]){
                delete tempEditDataById[currentEditingRowId];
            }
        }

        currentEditingRowId = null;
        originalEditData = null;
        isAddMode = false;

        modalBox.classList.add('scale-95','opacity-0');
        editModal.classList.add('opacity-0');
        setTimeout(() => editModal.classList.add('pointer-events-none'), 300);
    }

    addBtn.addEventListener('click', () => openModal());

    // Cancel button click → discard typed data
    cancelBtn.addEventListener('click', () => closeModal(true));

    // Clicking outside modal → just close, don't discard typed data
    editModal.addEventListener('click', e => { if(e.target === editModal) closeModal(false); });

    // ===== Table Actions =====
    table.addEventListener('click', e => {
        const editBtn = e.target.closest('.editTrainingBtn');
        const deleteBtn = e.target.closest('.deleteTrainingBtn');
        const viewBtn = e.target.closest('.viewCertificateBtn');

        if(editBtn) openModal(editBtn.closest('tr'));

        if(deleteBtn){
            const row = deleteBtn.closest('tr');
            const id = row.dataset.id;
            Swal.fire({
                title:'Are you sure?',
                text:'This will permanently delete this training record!',
                icon:'warning',
                showCancelButton:true,
                confirmButtonColor:'#d33',
                cancelButtonColor:'#3085d6',
                confirmButtonText:'Yes, delete it!'
            }).then(result=>{
                if(result.isConfirmed){
                    fetch('<?= base_url("account/deleteTraining") ?>/'+id,{
                        method:'DELETE',
                        headers:{'X-Requested-With':'XMLHttpRequest'}
                    }).then(r=>r.json()).then(res=>{
                        if(res.success){
                            Swal.fire({icon:'success',title:'Deleted!',text:res.message,timer:1200,showConfirmButton:false})
                            .then(()=> location.reload());
                        }else{
                            Swal.fire('Error',res.message,'error');
                        }
                    });
                }
            });
        }
if (viewBtn) {
    const fileName = viewBtn.dataset.file;
    
    // Debug: Log the file name/ID
    console.log('Training certificate file:', fileName);

    // No file → show warning instead of error
    if (!fileName || fileName.trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'No Certificate Available',
            text: 'No training certificate has been uploaded for this record.',
            showConfirmButton: false,
            timer: 1000
        });
        return;
    }

    // Show loading alert
    Swal.fire({
        title: 'Loading...',
        text: 'Please wait while the certificate loads.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();

            setTimeout(() => {
                // Use backend endpoint to get file (handles both local and Google Drive)
                const url = '<?= base_url("account/viewTrainingCertificate/") ?>' + encodeURIComponent(fileName);
                console.log('Fetching certificate from:', url);
                
                fetch(url)
                    .then(async res => {
                        console.log('Response status:', res.status);
                        const contentType = res.headers.get('content-type') || '';
                        console.log('Response content-type:', contentType);

                        // If JSON returned → file missing
                        if (contentType.includes('application/json')) {
                            const data = await res.json();
                            console.log('Error response:', data);
                            Swal.close();
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Certificate Available',
                                text: data.message || 'No training certificate has been uploaded for this record.',
                                showConfirmButton: false,
                                timer: 1000
                            });
                            throw new Error('File not available');
                        }

                        return res.blob(); // File exists
                    })
                    .then(blob => {
                        console.log('Blob received, size:', blob.size);
                        // Create object URL from blob
                        const blobUrl = URL.createObjectURL(blob);
                        
                        // Store for download functionality
                        certCurrentFileUrl = blobUrl;
                        certCurrentFileName = fileName;
                        
                        // Reset zoom
                        certCurrentZoom = 100;
                        certPdfContainer.style.transform = 'scale(1)';
                        certZoomLevelSpan.textContent = '100%';
                        
                        // Display in iframe
                        certificateFrame.src = blobUrl;
                        certificateModal.classList.remove('hidden');
                        certificateModal.classList.add('flex');
                        Swal.close(); // close loading
                    })
                    .catch(err => {
                        console.error('Error loading certificate:', err);
                    });
            }, 500); // delay before fetch
        }
    });
}

    });

    // ===== Certificate Modal =====
    function closeCertificateModalFunc() {
        certificateFrame.src = '';
        certificateModal.classList.add('hidden');
        certificateModal.classList.remove('flex');
        certCurrentZoom = 100;
        certPdfContainer.style.transform = 'scale(1)';
        certZoomLevelSpan.textContent = '100%';
        certCurrentFileUrl = '';
        certCurrentFileName = '';
    }
    certificateModal.addEventListener('click', e => { if(e.target === certificateModal) closeCertificateModalFunc(); });
    document.addEventListener('keydown', e => { if(e.key === 'Escape' && !certificateModal.classList.contains('hidden')) closeCertificateModalFunc(); });

    // ===== Form Submit =====
    editForm.addEventListener('submit', e => {
        e.preventDefault();

        const dateFrom = new Date(editForm.date_from.value);
        const dateTo = new Date(editForm.date_to.value);
        const today = new Date(); 
        today.setHours(0, 0, 0, 0); // Reset time portion for accurate comparison

        // Validate dates
        if (dateFrom > today) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Date',
                text: 'Check the date range!'
            });
            editForm.date_from.focus();
            return;
        }

        if (dateTo > today) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Date',
                text: 'Check the date range!.'
            });
            editForm.date_to.focus();
            return;
        }

        if (dateFrom > dateTo) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Date Range',
                text: 'Check the date range!.'
            });
            editForm.date_from.focus();
            return;
        }

        if(isAddMode && !certificateInput.value){
            Swal.fire({icon:'warning',title:'Required Field',text:'Please upload a certificate.'});
            certificateInput.focus();
            return;
        }

        const formData = new FormData(editForm);
        const id = editForm.id.value;
        const url = id
            ? '<?= base_url("account/updateTraining") ?>'
            : '<?= base_url("account/addApplicantTraining") ?>';

        fetch(url,{
            method:'POST',
            body:formData,
            headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(r => r.json())
        .then(res => {
            if(res.success){
                if(isAddMode) tempAddData = {};                   // clear Add temp on save
                else if(currentEditingRowId) tempEditDataById[currentEditingRowId] = {}; // clear Edit temp on save
                Swal.fire({icon:'success',title:'Saved!',text:res.message,timer:1200,showConfirmButton:false})
                .then(()=> location.reload());
            } else {
                Swal.fire('Error', res.message ?? 'Failed to save','error');
            }
        });
    });
});
</script>

<div class="tab-content hidden" id="tab-files">
    <h2 class="text-lg font-bold text-clsuGreen mb-3">Files</h2>

    <!-- PS Warning without background -->
    <div class="flex items-center mb-2 text-yellow-700 text-sm font-medium">
        <!-- Icon -->
        <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd">
            </path>
        </svg>
        <!-- Text -->
        <span>
            PS: Upload PDF files only, not exceeding 5 MB.
        </span>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="table-files">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Files</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700">Uploads</th>
                        <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $index = 1; ?>
                    <?php foreach ($requiredDocuments as $docType): ?>
                        <?php 
                        // Get document type ID and name
                        $docTypeId = isset($docType['id']) ? $docType['id'] : $docType['document_type_id'];
                        $docTypeName = isset($docType['document_type_name']) ? $docType['document_type_name'] : $docType['requirement_text'];
                        $filename = isset($fileRecords[$docTypeId]) ? $fileRecords[$docTypeId] : '';
                        
                        // Check if this is a certificate-related document type
                        $isCertificateType = false;
                        $certificateList = [];
                        
                        // Certificate of Eligibility / Rating / License (document type ID 3)
                        if ($docTypeId == 3) {
                            $isCertificateType = true;
                            $certificateList = $certificateInfo['civil_service_certificates'] ?? [];
                        }
                        // Certificate of Trainings and Seminars (document type ID 7)
                        elseif ($docTypeId == 7) {
                            $isCertificateType = true;
                            $certificateList = $certificateInfo['training_certificates'] ?? [];
                        }
                        
                        // Find matching Google Drive files for this document type
                        $googleDriveFileList = [];
                        if (!empty($googleDriveFiles)) {
                            // Map document type names to file name patterns
                            $fileNamePatterns = [
                                1 => ['personal_data_sheet', 'pds'],
                                2 => ['performance_rating'],
                                3 => ['certificate_of_eligibility', 'eligibility', 'rating', 'license'],
                                4 => ['transcript_of_records', 'tor'],
                                5 => ['diploma'],
                                6 => ['certificate_of_employment'],
                                7 => ['certificate_of_trainings', 'trainings', 'seminars']
                            ];
                            
                            $patterns = $fileNamePatterns[$docTypeId] ?? [];
                            
                            foreach ($googleDriveFiles as $gFile) {
                                $gFileName = strtolower($gFile['name']);
                                
                                // Check if filename matches any pattern for this document type
                                foreach ($patterns as $pattern) {
                                    if (strpos($gFileName, $pattern) !== false) {
                                        $googleDriveFileList[] = $gFile;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        // For document type 7, check if we should show combined view button
                        $showCombinedView = ($docTypeId == 7 && !empty($certificateList));
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 py-2 border-b text-gray-800 font-medium">
                                <?= $index ?>. <?= esc($docTypeName) ?>
                                <?php if ($isCertificateType && !empty($certificateList)): ?>
                                    <br><span class="text-xs text-gray-500 italic">(Includes <?= count($certificateList) ?> existing certificate<?= count($certificateList) > 1 ? 's' : '' ?> from your records)</span>
                                <?php endif; ?>
                                <?php if (!empty($googleDriveFileList)): ?>
                                    <br><span class="text-xs text-green-600 font-medium">(Found <?= count($googleDriveFileList) ?> file<?= count($googleDriveFileList) > 1 ? 's' : '' ?> in Google Drive)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-2 border-b text-gray-700 view-mode">
                                <?php if ($docTypeId == 7 && !empty($certificateList)): ?>
                                    <!-- Special handling for Certificate of Trainings and Seminars -->
                                    <button class="viewCombinedTrainingBtn inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View All Certificates (<?= count($certificateList) ?>)
                                    </button>
                                <?php elseif (!empty($filename)): ?>
                                    <button class="viewFileBtn inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                            data-file="<?= esc($filename) ?>">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Document
                                    </button>
                                <?php elseif ($isCertificateType && !empty($certificateList)): ?>
                                    <div class="text-xs">
                                        <span class="text-gray-600">Existing certificates:</span>
                                        <?php foreach ($certificateList as $cert): ?>
                                            <?php 
                                            $certName = '';
                                            if (isset($cert['certificate_file'])) {
                                                $certName = $cert['training_name'] ?? 'Training Certificate';
                                            } elseif (isset($cert['certificate'])) {
                                                $certName = $cert['eligibility'] ?? 'Civil Service Certificate';
                                            }
                                            $certFile = $cert['certificate_file'] ?? $cert['certificate'] ?? '';
                                            ?>
                                            <?php if (!empty($certFile)): ?>
                                                <div class="mt-1">
                                                    <button class="viewFileBtn inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                                            data-file="<?= esc($certFile) ?>">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        <?= esc($certName) ?>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">No file available</span>
                                <?php endif; ?>
                                <input type="file" name="document_<?= $docTypeId ?>" data-doc-type="<?= $docTypeId ?>" accept=".pdf" class="w-full text-xs edit-mode hidden">
                            </td>
                            <td class="px-3 py-2 border-b text-center">
                                <div class="flex justify-center gap-1">
                                    <button class="edit-file inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button class="delete-file inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors" data-doc-type="<?= $docTypeId ?>">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php $index++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Google Drive Files Section -->
    <?php if (!empty($googleDriveFiles)): ?>
    <div class="mt-4">
        <h3 class="text-sm font-bold text-clsuGreen mb-2">Files from Google Drive</h3>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 border-b font-semibold text-gray-700">File Name</th>
                            <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Type</th>
                            <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Uploaded</th>
                            <th class="px-3 py-2 border-b font-semibold text-gray-700 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($googleDriveFiles as $gFile): ?>
                            <?php 
                            // Extract user ID and timestamp from filename
                            $fileNameParts = explode('_', $gFile['name'], 2);
                            $fileUserId = $fileNameParts[0] ?? '';
                            
                            // Only show files for current user
                            if ($fileUserId != $userId) continue;
                            
                            // Format upload date
                            $uploadDate = isset($gFile['createdTime']) ? date('M j, Y', strtotime($gFile['createdTime'])) : 'N/A';
                            
                            // Determine file type icon
                            $mimeType = $gFile['mimeType'] ?? '';
                            $fileIcon = '📄';
                            if (strpos($mimeType, 'image') !== false) {
                                $fileIcon = '🖼️';
                            } elseif (strpos($mimeType, 'pdf') !== false) {
                                $fileIcon = '📕';
                            } elseif (strpos($mimeType, 'word') !== false || strpos($mimeType, 'document') !== false) {
                                $fileIcon = '📘';
                            }
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-2 border-b text-gray-800">
                                    <?= esc($gFile['name']) ?>
                                </td>
                                <td class="px-3 py-2 border-b text-gray-700 text-center">
                                    <?= $fileIcon ?>
                                </td>
                                <td class="px-3 py-2 border-b text-gray-700 text-center">
                                    <?= $uploadDate ?>
                                </td>
                                <td class="px-3 py-2 border-b text-center">
                                    <button class="viewGoogleDriveFile inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                            data-file-id="<?= esc($gFile['id']) ?>">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty(array_filter($googleDriveFiles, fn($f) => strpos($f['name'], $userId . '_') === 0))): ?>
                            <tr>
                                <td class="px-3 py-4 text-center text-gray-500 italic" colspan="4">
                                    No files found in Google Drive for your account
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <p class="text-xs text-gray-500 mt-2">
        <?php
        if (!empty($fileRecords['uploaded_at']) && $fileRecords['uploaded_at'] != '0000-00-00 00:00:00') {
            // Database stores in UTC, convert to Philippine Time
            $dt = new DateTime($fileRecords['uploaded_at'], new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone('Asia/Manila'));
            echo 'Updated on: ' . $dt->format('F j, Y g:i A');
        } else {
            echo 'Upload date not available';
        }
        ?>
    </p>
</div>

<!-- File Viewer Modal -->
<div id="fileViewerModal"
     class="hidden fixed inset-0 bg-gray-800 bg-opacity-90 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-6xl h-full relative flex flex-col shadow-lg">
        <iframe id="fileViewerFrame"
                src="" class="flex-1 w-full h-full border-none"></iframe>
    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Show message if user needs to fill out profile before applying
    <?php if(session()->getFlashdata('fill_details_required')): ?>
    // Disable ALL navigation buttons including Home and Profile
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.style.pointerEvents = 'none';
        link.style.opacity = '0.5';
        link.style.cursor = 'not-allowed';
        link.title = 'Complete your profile first';
    });
    
    // Also disable account dropdown menu items except Change Password
    const dropdownItems = document.querySelectorAll('#accountDropdown a');
    dropdownItems.forEach(item => {
        if (!item.href.includes('changePassword')) {
            item.style.pointerEvents = 'none';
            item.style.opacity = '0.5';
            item.style.cursor = 'not-allowed';
        }
    });
    
    // Show warning message first
    Swal.fire({
        icon: 'warning',
        title: 'Profile Incomplete!',
        text: 'You need to fill out everything in your Profile before applying for a job position.',
        confirmButtonColor: '#0B6B3A',
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then(() => {
        // Automatically open the Edit Personal Information modal
        openPersonalModal();
    });
    <?php endif; ?>

    // Show success message after updating profile
    <?php if(session()->getFlashdata('profile_updated')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Profile Updated!',
        text: '<?= session()->getFlashdata('profile_updated') ?>',
        confirmButtonColor: '#0B6B3A'
    }).then(() => {
        // Check if there's a pending redirect URL
        <?php if(session()->get('pending_redirect_url')): ?>
        // Ask user if they want to continue to application
        Swal.fire({
            icon: 'question',
            title: 'Continue Application?',
            text: 'Your profile is now complete. Would you like to continue with your job application?',
            showCancelButton: true,
            confirmButtonText: 'Yes, continue!',
            cancelButtonText: 'No, stay here',
            confirmButtonColor: '#0B6B3A',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to the stored URL
                window.location.href = '<?= session()->get('pending_redirect_url') ?>';
            }
        });
        <?php else: ?>
        // No pending redirect, just reload
        window.location.reload();
        <?php endif; ?>
    });
    <?php endif; ?>

    const table = document.querySelector('#tab-files table tbody');
    const modal = document.getElementById('fileViewerModal');
    const iframe = document.getElementById('fileViewerFrame');

    if (!table) return;

    table.addEventListener('click', async (e) => {

// ===== VIEW GOOGLE DRIVE FILE FROM FILES SECTION =====
const viewGoogleDriveBtn = e.target.closest('.viewGoogleDriveFile');
if (viewGoogleDriveBtn) {
    const fileId = viewGoogleDriveBtn.dataset.fileId;
    
    if (!fileId) {
        Swal.fire({
            icon: 'warning',
            title: 'No File ID',
            text: 'File ID is missing.',
            showConfirmButton: false,
            timer: 1000
        });
        return;
    }
    
    Swal.fire({
        title: 'Loading...',
        text: 'Please wait while the file loads.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    try {
        // Use backend endpoint to get Google Drive file
        const response = await fetch(`<?= base_url('account/viewFile/') ?>${encodeURIComponent(fileId)}`);
        const contentType = response.headers.get('content-type') || '';
        
        // Check if JSON error was returned
        if (contentType.includes('application/json')) {
            const data = await response.json();
            Swal.close();
            
            // Show generic error message
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to load file.',
                showConfirmButton: false,
                timer: 3000
            });
            return;
        }
        
        // Get file blob and display in modal
        const blob = await response.blob();
        const fileURL = URL.createObjectURL(blob);
        
        iframe.src = fileURL;
        modal.classList.remove('hidden');
        
        Swal.close();
        
    } catch (err) {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Unable to load file.',
            showConfirmButton: false,
            timer: 2000
        });
        console.error(err);
    }
    
    return;
}

// ===== VIEW COMBINED TRAINING CERTIFICATES (DOCUMENT TYPE 7) =====
const viewCombinedTrainingBtn = e.target.closest('.viewCombinedTrainingBtn');
if (viewCombinedTrainingBtn) {
    Swal.fire({
        title: 'Loading Certificates...',
        text: 'Please wait while we combine all your training certificates.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    // Get the certificate modal and iframe
    const modal = document.getElementById('certificateModal');
    const frame = document.getElementById('certificateFrame');
    
    if (!modal || !frame) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Unable to load certificate viewer.',
            timer: 2000
        });
        return;
    }
    
    setTimeout(() => {
        // Use the combined PDF endpoint that downloads from Google Drive and combines using FPDI
        frame.src = '<?= site_url('account/viewCombinedTrainingCertificates') ?>';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        Swal.close();
    }, 800);
    
    return;
}

// ===== VIEW FILE (EXISTING HANDLER) =====
const viewBtn = e.target.closest('.viewFileBtn');
if (viewBtn) {
    (async () => {
        const filename = viewBtn.dataset.file;

        // No file → show warning instead of error
        if (!filename || filename.trim() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'No File Available',
                text: 'No file has been uploaded for this document.',
                showConfirmButton: false,
                timer: 1000
            });
            return;
        }

        // Show loading alert
        Swal.fire({
            title: 'Loading...',
            text: 'Please wait while the file loads.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        // Check if this is a Google Drive file ID (28-33 characters, no timestamp prefix)
        const isGoogleDriveFile = /^[a-zA-Z0-9_-]{28,33}$/.test(filename) && !/^\d{10}_/.test(filename);

        if(isGoogleDriveFile) {
            // For Google Drive files, fetch as blob and show in modal (same as local files)
            Swal.fire({
                title: 'Loading...',
                text: 'Please wait while the file loads.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    setTimeout(() => {
                        // Use backend endpoint to get Google Drive file
                        fetch(`<?= base_url('account/viewFile/') ?>${encodeURIComponent(filename)}`)
                            .then(async res => {
                                const contentType = res.headers.get('content-type') || '';
                                
                                // JSON returned → file missing
                                if (contentType.includes('application/json')) {
                                    const data = await res.json();
                                    Swal.close();
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'No File Available',
                                        text: data.message || 'No file has been uploaded for this document.',
                                        showConfirmButton: false,
                                        timer: 1000
                                    });
                                    throw new Error('File not available');
                                }
                                
                                return res.blob(); // File exists
                            })
                            .then(blob => {
                                const url = URL.createObjectURL(blob);
                                iframe.src = url;
                                modal.classList.remove('hidden');
                                Swal.close(); // close loading
                            })
                            .catch(err => {
                                console.warn(err);
                            });
                    }, 1000); // delay before fetch
                }
            });
            return;
        }

        try {
            // Show loading first with setTimeout delay (same as training certificates)
            Swal.fire({
                title: 'Loading...',
                text: 'Please wait while the file loads.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    setTimeout(() => {
                        fetch(`<?= base_url('account/viewFile/') ?>${encodeURIComponent(filename)}`)
                            .then(async res => {
                                const contentType = res.headers.get('content-type') || '';
                                
                                // JSON returned → file missing
                                if (contentType.includes('application/json')) {
                                    const data = await res.json();
                                    Swal.close();
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'No File Available',
                                        text: data.message || 'No file has been uploaded for this document.',
                                        showConfirmButton: false,
                                        timer: 1000
                                    });
                                    throw new Error('File not available');
                                }
                                
                                return res.blob(); // File exists
                            })
                            .then(blob => {
                                const url = URL.createObjectURL(blob);
                                iframe.src = url;
                                modal.classList.remove('hidden');
                                Swal.close(); // close loading
                            })
                            .catch(err => {
                                console.warn(err);
                            });
                    }, 1000); // delay before fetch
                }
            });
            
        } catch (err) {
            Swal.close();
            Swal.fire({
                icon: 'warning',
                title: 'Unable to Open File',
                text: 'The file could not be loaded.',
                showConfirmButton: false,
                timer: 1000
            });
            console.error(err);
        }
    })();
    return; // Important to prevent other handlers from running
}
// ===== VIEW ALL CIVIL SERVICE ELIGIBILITY CERTIFICATES =====
const viewEligibilityBtn = e.target.closest('.viewEligibilityBtn');
if (viewEligibilityBtn) {

    Swal.fire({
        title: 'Loading Certificates...',
        text: 'Please wait while we prepare your documents.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        await new Promise(resolve => setTimeout(resolve, 1000));

        iframe.src = '<?= base_url("account/viewEligibilityCertificates") ?>';
        modal.classList.remove('hidden');

        Swal.close();

    } catch (err) {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Unable to load certificates.',
            showConfirmButton: false,
            timer: 1200
        });
        console.error(err);
    }

    return; // important so it doesn't continue to other handlers
}

// ===== VIEW ALL TRAINING CERTIFICATES =====
const viewTrainingBtn = e.target.closest('.viewTrainingBtn');
if (viewTrainingBtn) {

    Swal.fire({
        title: 'Loading Certificates...',
        text: 'Please wait while we prepare your documents.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        await new Promise(resolve => setTimeout(resolve, 1000));

        iframe.src = '<?= base_url("account/viewTrainingCertificates") ?>';
        modal.classList.remove('hidden');

        Swal.close();

    } catch (err) {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Unable to load certificates.',
            showConfirmButton: false,
            timer: 1200
        });
        console.error(err);
    }

    return; // important so it doesn't continue to other handlers
}

        // ===== EDIT FILE =====
        const editBtn = e.target.closest('.edit-file');
        if (editBtn) {
            const row = editBtn.closest('tr');
            const fileInput = row.querySelector('input[type="file"]');
            fileInput.click();

            fileInput.addEventListener('change', async () => {
                if (!fileInput.files[0]) return;

                // Check file size (5MB limit)
                const maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
                if (fileInput.files[0].size > maxFileSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'File size must not exceed 5 MB.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    return;
                }

                // Show loading message
                Swal.fire({
                    title: 'Loading...',
                    text: 'Please wait while the file loads.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('document_type_id', fileInput.dataset.docType);

                try {
                    const res = await fetch('<?= base_url("account/updateFile") ?>', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();

                    // Close loading first
                    Swal.close();

                    if(data.status==='success'){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1200
                        }).then(() => location.reload());
                    }else{
                        // Check if authentication is required
                        if(data.auth_required && data.auth_url){
                            Swal.fire({
                                icon: 'warning',
                                title: 'Google Drive Authentication Required',
                                text: data.message,
                                showCancelButton: true,
                                confirmButtonText: 'Connect Google Drive',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Redirect to Google Drive authentication
                                    window.location.href = data.auth_url;
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1000
                            });
                        }
                    }
                } catch(err){
                    // Close loading on error
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong',
                        showConfirmButton: false,
                        timer: 1000
                    });
                }
            }, { once:true });
            return;
        }

        // ===== DELETE FILE =====
        const deleteBtn = e.target.closest('.delete-file');
        if(deleteBtn){
            const row = deleteBtn.closest('tr');
            const fileInput = row.querySelector('input[type="file"]');
            const documentTypeId = deleteBtn.dataset.docType;
            const fileLink = row.querySelector('.view-mode .viewFileBtn');
            const fileName = fileLink?.dataset.file;

            if(!fileName){
                Swal.fire({
                    icon: 'info',
                    title: 'No File',
                    text: 'No file available to delete.',
                    showConfirmButton: false,
                    timer: 1000
                });
                return;
            }

            const confirmDelete = await Swal.fire({
                title: 'Delete file?',
                text: 'This will permanently delete this file record!',
                icon: 'warning',
                showCancelButton:true,
                confirmButtonColor:'#d33',
                cancelButtonColor:'#3085d6',
                confirmButtonText:'Yes, delete it'
            });
            if(!confirmDelete.isConfirmed) return;

            try{
                const formData = new FormData();
                formData.append('document_type_id', documentTypeId);

                const res = await fetch('<?= base_url("account/deleteFile") ?>',{
                    method:'POST',
                    body: formData
                });
                const data = await res.json();

                if(data.status==='success'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1200
                    }).then(() => location.reload());
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1000
                    });
                }
            } catch(err){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong',
                    showConfirmButton: false,
                    timer: 1000
                });
            }
        }
    });

    // ===== CLOSE MODAL =====
    modal.addEventListener('click',(e)=>{
        if(e.target===modal){
            iframe.src='';
            modal.classList.add('hidden');
        }
    });
});
</script>
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
    // Check if there's a hash in the URL (e.g., #education, #work, #civil, #training)
    const hash = window.location.hash.substring(1); // Remove the '#' character
    
    if (hash && ['personal', 'education', 'work', 'civil', 'training', 'files'].includes(hash)) {
        // Use the hash to determine which tab to show
        activateTab(hash);
    } else {
        // Otherwise, use localStorage or default to 'personal'
        const savedTab = localStorage.getItem('activeTab') || 'personal';
        activateTab(savedTab);
    }
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
                    timer: 1000,
                    showConfirmButton: false
                });
            } else {
                // Check if authentication is required
                if(data.auth_required && data.auth_url){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Google Drive Authentication Required',
                        text: data.message,
                        showCancelButton: true,
                        confirmButtonText: 'Connect Google Drive',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to Google Drive authentication
                            window.location.href = data.auth_url;
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message
                    });
                }
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


// ===== CLOSE DOCUMENT MODAL =====
document.getElementById('document-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        const frame = document.getElementById('document-frame');
        frame.src = '';
        this.classList.add('hidden');
        this.classList.remove('flex');
    }
});
</script>

</body>
</html>
