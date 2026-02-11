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

<!-- Edit Personal Info Button -->
<div class="flex justify-end mb-6">
  <button id="editPersonalBtn" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">Edit Personal Info</button>
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
document.addEventListener('DOMContentLoaded', () => {
    const personalModal = document.getElementById('personalModal');
    const editBtn = document.getElementById('editPersonalBtn');
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
    });

    // Cancel → reset
    cancelBtn.addEventListener('click', () => {
        for (let id in originalValues) {
            document.getElementById(id).value = originalValues[id];
        }
        personalModal.classList.add('opacity-0','pointer-events-none');
        personalModal.querySelector('div').classList.add('scale-95','opacity-0');
    });

    // Click outside → close
    personalModal.addEventListener('click', e => {
        if (e.target === personalModal) {
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
                Swal.fire({icon:'success',title:'Saved!',text:data.message,timer:1500,showConfirmButton:false,willClose:()=>location.reload()});
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
<!-- Family Background -->
<h2 class="text-xl font-bold text-clsuGreen mb-2 mt-6">Family Background</h2>

<?php $relations = ['Spouse', 'Father', 'Mother']; ?>
<?php foreach ($relations as $rel): ?>
  <?php $key = strtolower($rel); ?>
  <div class="mb-4">
    <p class="font-semibold text-xs mb-1 text-clsuGreen">
      <?= $rel ?><?php if($rel==='Mother') echo ' (Maiden Name)'; ?>
    </p>

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
            <td class="px-2 py-1 border" data-key="<?= $key ?>_first_name"><?= esc($familyProfile[$key]['first_name']) ?></td>
            <td class="px-2 py-1 border" data-key="<?= $key ?>_middle_name"><?= esc($familyProfile[$key]['middle_name']) ?></td>
            <td class="px-2 py-1 border" data-key="<?= $key ?>_last_name"><?= esc($familyProfile[$key]['last_name']) ?></td>
            <?php if($rel !== 'Mother'): ?>
              <td class="px-2 py-1 border" data-key="<?= $key ?>_extension"><?= esc($familyProfile[$key]['extension']) ?></td>
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
            <td class="px-2 py-1 border" data-key="<?= $key ?>_contact_no"><?= esc($familyProfile[$key]['contact_no']) ?></td>
            <td class="px-2 py-1 border" data-key="<?= $key ?>_occupation"><?= esc($familyProfile[$key]['occupation']) ?></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
<?php endforeach; ?>

<!-- Edit Family Background Button -->
<div class="flex justify-end mb-4">
  <button id="editFamilyBtn" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">Edit Family Background</button>
</div>

<!-- Family Modal -->
<div id="familyModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 z-50">
  <div class="bg-white rounded-lg w-3/4 max-w-3xl p-4 transform scale-95 opacity-0 transition-all duration-300">

    <h3 class="text-lg font-bold text-clsuGreen mb-3">Edit Family Background</h3>

    <form id="familyForm" class="grid gap-3">
      <?php foreach ($relations as $rel): ?>
        <?php $key = strtolower($rel); ?>
        <div class="border-b pb-3">

          <!-- Relation Label -->
          <p class="font-semibold text-xs mb-1 text-clsuGreen">
            <?= $rel ?><?php if($rel==='Mother') echo ' (Maiden Name)'; ?>
          </p>

          <!-- Row 1: Names -->
          <div class="grid gap-3 mb-1" style="grid-template-columns: <?= $rel==='Mother' ? '1fr 1fr 1fr' : 'repeat(4,1fr)' ?>;">
            <div>
              <label class="text-xs font-semibold">First Name <?php if($rel !== 'Spouse') echo '<span class="text-red-500">*</span>'; ?></label>
              <input type="text" class="w-full text-xs px-2 py-1 border rounded"
                     placeholder="Enter First Name" maxlength="50"
                     id="<?= $key ?>_first_name" name="<?= $key ?>_first_name"
                     value="<?= esc($familyProfile[$key]['first_name'] ?? '') ?>"
                     <?php if($rel !== 'Spouse') echo 'required'; ?>>
            </div>
            <div>
              <label class="text-xs font-semibold">Middle Name</label>
              <input type="text" class="w-full text-xs px-2 py-1 border rounded"
                     placeholder="Enter Middle Name" maxlength="50"
                     id="<?= $key ?>_middle_name" name="<?= $key ?>_middle_name"
                     value="<?= esc($familyProfile[$key]['middle_name'] ?? '') ?>">
            </div>
            <div>
              <label class="text-xs font-semibold">Last Name <?php if($rel !== 'Spouse') echo '<span class="text-red-500">*</span>'; ?></label>
              <input type="text" class="w-full text-xs px-2 py-1 border rounded"
                     placeholder="Enter Last Name" maxlength="50"
                     id="<?= $key ?>_last_name" name="<?= $key ?>_last_name"
                     value="<?= esc($familyProfile[$key]['last_name'] ?? '') ?>"
                     <?php if($rel !== 'Spouse') echo 'required'; ?>>
            </div>
            <?php if($rel !== 'Mother'): ?>
            <div>
              <label class="text-xs font-semibold">Extension</label>
              <input type="text" class="w-full text-xs px-2 py-1 border rounded"
                     placeholder="Enter Extension" maxlength="10"
                     id="<?= $key ?>_extension" name="<?= $key ?>_extension"
                     value="<?= esc($familyProfile[$key]['extension'] ?? '') ?>">
            </div>
            <?php endif; ?>
          </div>

          <!-- Row 2: Contact / Occupation -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-xs font-semibold">Contact No. <?php if($rel !== 'Spouse') echo '<span class="text-red-500">*</span>'; ?></label>
              <input type="text" class="w-full text-xs px-2 py-1 border rounded contact-number"
                     placeholder="Enter 11-digit Contact No." maxlength="11"
                     id="<?= $key ?>_contact_no" name="<?= $key ?>_contact_no"
                     value="<?= esc($familyProfile[$key]['contact_no'] ?? '') ?>"
                     <?php if($rel !== 'Spouse') echo 'required'; ?>>
            </div>
            <div>
              <label class="text-xs font-semibold">Occupation <?php if($rel !== 'Spouse') echo '<span class="text-red-500">*</span>'; ?></label>
              <input type="text" class="w-full text-xs px-2 py-1 border rounded"
                     placeholder="Enter Occupation" maxlength="50"
                     id="<?= $key ?>_occupation" name="<?= $key ?>_occupation"
                     value="<?= esc($familyProfile[$key]['occupation'] ?? '') ?>"
                     <?php if($rel !== 'Spouse') echo 'required'; ?>>
            </div>
          </div>

        </div>
      <?php endforeach; ?>

      <!-- Buttons -->
      <div class="flex justify-end gap-2 mt-3">
        <button type="button" id="cancelFamily" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition-colors">Cancel</button>
        <button type="submit" id="saveFamily" class="bg-clsuGreen hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition-colors">Save</button>
      </div>
    </form>

  </div>
</div>

<script>
// Modal open/close
const familyModal = document.getElementById('familyModal');
const editFamilyBtn = document.getElementById('editFamilyBtn');
const cancelFamilyBtn = document.getElementById('cancelFamily');
const saveFamilyBtn = document.getElementById('saveFamily');

// Store DB values (original values)
function getFamilyOriginalValues() {
    const values = {};
    <?php foreach ($relations as $rel): ?>
    <?php $key = strtolower($rel); ?>
    values['<?= $key ?>'] = {
        first_name: document.querySelector('[data-key="<?= $key ?>_first_name"]').innerText.trim() === '-' ? '' : document.querySelector('[data-key="<?= $key ?>_first_name"]').innerText.trim(),
        middle_name: document.querySelector('[data-key="<?= $key ?>_middle_name"]').innerText.trim() === '-' ? '' : document.querySelector('[data-key="<?= $key ?>_middle_name"]').innerText.trim(),
        last_name: document.querySelector('[data-key="<?= $key ?>_last_name"]').innerText.trim() === '-' ? '' : document.querySelector('[data-key="<?= $key ?>_last_name"]').innerText.trim(),
        extension: document.querySelector('[data-key="<?= $key ?>_extension"]') ? (document.querySelector('[data-key="<?= $key ?>_extension"]').innerText.trim() === '-' ? '' : document.querySelector('[data-key="<?= $key ?>_extension"]').innerText.trim()) : '',
        contact_no: document.querySelector('[data-key="<?= $key ?>_contact_no"]').innerText.trim() === '-' ? '' : document.querySelector('[data-key="<?= $key ?>_contact_no"]').innerText.trim(),
        occupation: document.querySelector('[data-key="<?= $key ?>_occupation"]').innerText.trim() === '-' ? '' : document.querySelector('[data-key="<?= $key ?>_occupation"]').innerText.trim()
    };
    <?php endforeach; ?>
    return values;
}

let familyOriginalValues = getFamilyOriginalValues();
let familyTypedValues = null;

function prefillModal(values) {
    <?php foreach ($relations as $rel): ?>
    <?php $key = strtolower($rel); ?>
    ['first_name','middle_name','last_name','extension','contact_no','occupation'].forEach(id => {
        const el = document.getElementById('<?= $key ?>_' + id);
        if(el) el.value = values['<?= $key ?>'][id];
    });
    <?php endforeach; ?>
}

// Edit button
editFamilyBtn.addEventListener('click', () => {
    prefillModal(familyTypedValues || familyOriginalValues);
    familyModal.classList.remove('opacity-0','pointer-events-none');
    familyModal.querySelector('div').classList.remove('scale-95','opacity-0');
});

// Cancel button
cancelFamilyBtn.addEventListener('click', () => {
    prefillModal(familyOriginalValues);
    familyTypedValues = null;
    familyModal.classList.add('opacity-0','pointer-events-none');
    familyModal.querySelector('div').classList.add('scale-95','opacity-0');
});

// Close modal by clicking overlay
familyModal.addEventListener('click', e => {
    if(e.target === familyModal){
        familyTypedValues = {};
        <?php foreach ($relations as $rel): ?>
        <?php $key = strtolower($rel); ?>
        familyTypedValues['<?= $key ?>'] = {};
        ['first_name','middle_name','last_name','extension','contact_no','occupation'].forEach(id => {
            const el = document.getElementById('<?= $key ?>_' + id);
            familyTypedValues['<?= $key ?>'][id] = el ? el.value : '';
        });
        <?php endforeach; ?>
        familyModal.classList.add('opacity-0','pointer-events-none');
        familyModal.querySelector('div').classList.add('scale-95','opacity-0');
    }
});

// Save family (AJAX) - removed the SweetAlert for required fields
saveFamilyBtn.addEventListener('click', async () => {
    const form = document.getElementById('familyForm');

    // Let browser handle required validation, so remove the previous SweetAlert check
    if(!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);

    // Validate contact numbers
    for(let [key,value] of formData.entries()){
        if(key.endsWith('contact_no') && value && !/^\d{11}$/.test(value)){
            Swal.fire({ icon:'warning', title:'Invalid Contact Number', text:'Contact number must be exactly 11 digits.', timer:2500, showConfirmButton:false });
            return;
        }
    }

    try {
        const res = await fetch('<?= site_url("account/updateFamily") ?>', { method:'POST', body:formData });
        const data = await res.json();
        if(data.success){
            Swal.fire({ icon:'success', title:'Saved!', text:data.message, timer:1500, showConfirmButton:false, willClose:()=>location.reload() });
        } else {
            Swal.fire({ icon:'error', title:'Error', text:data.message, timer:2500, showConfirmButton:false });
        }
    } catch(err){
        console.error(err);
        Swal.fire({ icon:'error', title:'Error', text:'Failed to save family background.', timer:2500, showConfirmButton:false });
    }
});

// Contact number formatting
document.querySelectorAll('.contact-number').forEach(input => {
  input.addEventListener('input', e => {
    let value = e.target.value.replace(/\D/g,'');
    if(value.length>11) value = value.slice(0,11);
    e.target.value = value;
  });
});
</script>
</div>
  
<!-- === EDUCATION TAB === -->
<div class="tab-content" id="tab-education">
    <h2 class="text-lg font-bold text-clsuGreen mb-3">Educational Background</h2>

    <div class="overflow-x-auto mb-5">
        <table class="table-auto w-full text-left border-collapse text-xs" id="table-education">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-2 py-2 border w-20">Level</th>
                    <th class="px-2 py-2 border w-40">Name of School</th>
                    <th class="px-2 py-2 border w-40">Degree / Course</th>
                    <th class="px-2 py-2 border w-14">From</th>
                    <th class="px-2 py-2 border w-14">To</th>
                    <th class="px-2 py-2 border w-28">Highest Level / Units Earned</th>
                    <th class="px-2 py-2 border w-20">Year Graduated</th>
                    <th class="px-2 py-2 border w-36">Scholarship / Academic Honors</th>
                    <th class="px-2 py-2 border w-36">Actions</th>
                </tr>
            </thead>
<tbody>
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
        <td class="px-2 py-2 border text-center italic text-gray-500" colspan="9">
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
            <tr class="hover:bg-gray-50" data-level="<?= esc($levelName) ?>" data-id="<?= esc($edu['id']) ?>">
                <td class="px-2 py-2 border font-semibold"><?= $firstRow ? esc($levelName) : '' ?></td>
                <td class="px-2 py-2 border" data-key="school_name"><?= esc($edu['school_name']) ?></td>
                <td class="px-2 py-2 border" data-key="degree_course" data-degree-id="<?= esc($edu['degree_id']) ?>"><?= esc($edu['degree_course']) ?></td>
                <td class="px-2 py-2 border" data-key="period_from"><?= esc($edu['period_from']) ?></td>
                <td class="px-2 py-2 border" data-key="period_to"><?= esc($edu['period_to']) ?></td>
                <td class="px-2 py-2 border" data-key="highest_level_units"><?= esc($edu['highest_level_units']) ?></td>
                <td class="px-2 py-2 border" data-key="year_graduated"><?= esc($edu['year_graduated']) ?></td>
                <td class="px-2 py-2 border" data-key="awards"><?= esc($edu['awards']) ?></td>

                <?php if($edu['id']): ?>
                   <td class="px-1 py-1 border text-center">
        <button class="editWorkBtn text-blue-600 px-1"><i class="fa-solid fa-pen-to-square"></i></button>
        <button class="deleteWorkBtn text-red-600 px-1"><i class="fa-solid fa-trash"></i></button>
    </td>
                <?php else: ?>
                <td class="px-2 py-2 border text-center text-gray-300">-</td>
                <?php endif; ?>
            </tr>
        <?php $firstRow = false; endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>
</tbody>

        </table>
    </div>

    <!-- Bottom Buttons -->
    <div class="flex justify-end gap-2">
        <button id="addEducationBtn" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">Add Education</button>
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
                <label class="text-xs font-semibold">Degree / Course<span class="text-red-500">*</span></label>
                <select name="degree_id" class="w-full border px-2 py-1 rounded text-xs" required>
                    <option value="" disabled selected>Select Degree</option>
                </select>
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

            <div>
                <label class="text-xs font-semibold">Highest Level / Units Earned<span class="text-red-500">*</span></label>
                <input type="text" name="highest_level_units" class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter units or level" required>
            </div>

            <div>
                <label class="text-xs font-semibold">Year Graduated<span class="text-red-500">*</span></label>
                <input type="number" name="year_graduated" class="w-full border px-2 py-1 rounded text-xs" min="1900" max="2100" placeholder="YYYY" required>
            </div>

            <div>
                <label class="text-xs font-semibold">Scholarship / Academic Honors<span class="text-red-500">*</span></label>
                <input type="text" name="awards" class="w-full border px-2 py-1 rounded text-xs" placeholder="Enter awards" required>
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

        form.school_name.value         = tempEditData.school_name ?? data.school_name ?? '';
        form.period_from.value         = tempEditData.period_from ?? data.period_from ?? '';
        form.period_to.value           = tempEditData.period_to ?? data.period_to ?? '';
        form.highest_level_units.value = tempEditData.highest_level_units ?? data.highest_level_units ?? '';
        form.year_graduated.value      = tempEditData.year_graduated ?? data.year_graduated ?? '';
        form.awards.value              = tempEditData.awards ?? data.awards ?? '';
    } else {
        // Add mode
        levelSelect.value = tempAddData.degree_level_id ?? '';
        updateDegreeOptions(levelSelect.value, tempAddData.degree_id ?? null);

        form.school_name.value         = tempAddData.school_name ?? '';
        form.period_from.value         = tempAddData.period_from ?? '';
        form.period_to.value           = tempAddData.period_to ?? '';
        form.highest_level_units.value = tempAddData.highest_level_units ?? '';
        form.year_graduated.value      = tempAddData.year_graduated ?? '';
        form.awards.value              = tempAddData.awards ?? '';
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
                                timer: 1500
                            });
                        }
                    } catch(err){
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete: ' + err.message,
                            showConfirmButton: false,
                            timer: 1500
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
        'period_to',
        'highest_level_units',
        'year_graduated',
        'awards'
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
                timer: 1500
            });
        }

    } catch(err){
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to save education data: ' + err.message,
            showConfirmButton: false,
            timer: 1500
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
        <th rowspan="2" class="px-1 py-1 border w-20">Total Days</th>
        <th rowspan="2" class="px-1 py-1 border w-20">Actions</th>
    </tr>
    <tr class="bg-gray-100">
        <th class="px-1 py-1 border w-14">From</th>
        <th class="px-1 py-1 border w-14">To</th>
    </tr>
</thead>
<tbody id="workBody">
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

    $totalDays = 0;
    if(!empty($workRecords)):
        foreach($workRecords as $work):
            foreach (['position_title','office','status_of_appointment','govt_service'] as $key) {
                if (empty($work[$key])) $work[$key] = '-';
            }
            $work['date_from_ts'] = (!empty($work['date_from']) && $work['date_from'] !== '0000-00-00') ? strtotime($work['date_from']) : null;
            $work['date_to_ts'] = (!empty($work['date_to']) && $work['date_to'] !== '0000-00-00') ? strtotime($work['date_to']) : null;
            $daysText = ($work['date_from_ts'] && $work['date_to_ts']) ? calculate_duration($work['date_from_ts'], $work['date_to_ts']) : '-';
            $totalDays += ($work['date_from_ts'] && $work['date_to_ts']) ? ($work['date_to_ts'] - $work['date_from_ts'])/86400 + 1 : 0;
    ?>
    <tr data-id="<?= esc($work['id']) ?>">
        <td class="px-1 py-1 border" data-key="position_title"><?= esc($work['position_title']) ?></td>
        <td class="px-1 py-1 border" data-key="office"><?= esc($work['office']) ?></td>
        <td class="px-1 py-1 border" data-key="date_from" data-value="<?= $work['date_from'] ?>"><?= !empty($work['date_from']) ? date('F j, Y', $work['date_from_ts']) : '-' ?></td>
        <td class="px-1 py-1 border" data-key="date_to" data-value="<?= $work['date_to'] ?>"><?= !empty($work['date_to']) ? date('F j, Y', $work['date_to_ts']) : '-' ?></td>
        <td class="px-1 py-1 border" data-key="status_of_appointment"><?= esc($work['status_of_appointment']) ?></td>
        <td class="px-1 py-1 border" data-key="govt_service"><?= esc($work['govt_service']) ?></td>
        <td class="px-1 py-1 border text-center" data-key="total_days"><?= $daysText ?></td>
        <td class="px-1 py-1 border text-center">
            <button class="editWorkBtn text-blue-600 px-1"><i class="fa-solid fa-pen-to-square"></i></button>
            <button class="deleteWorkBtn text-red-600 px-1"><i class="fa-solid fa-trash"></i></button>
        </td>
    </tr>
    <?php
        endforeach;
    else:
    ?>
  <tr>
     <td class="px-2 py-2 border text-center italic text-gray-500" colspan="9">No work experience found for this applicant.</td>
    </tr>
    <?php endif; ?>
</tbody>
<!-- TOTAL ROW -->
<?php if(!empty($workRecords)): ?>
<tfoot>
    <tr class="bg-gray-100 font-bold">
        <td colspan="6" class="px-1 py-1 border text-right">Total Work Duration:</td>
        <td class="px-1 py-1 border text-center"><?= $totalDays > 0 ? $totalDays : '-' ?></td>
        <td class="px-1 py-1 border"></td>
    </tr>
</tfoot>
<?php endif; ?>

        </table>
    </div>

    <div class="flex justify-end gap-2 -mt-1" id="workButtons">
        <button id="addWorkBtn" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">Add Work Experience</button>
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
        // Add modal
        form.position_title.value = tempAddData.position_title ?? '';
        form.office.value = tempAddData.office ?? '';
        form.date_from.value = tempAddData.date_from ?? '';
        form.date_to.value = tempAddData.date_to ?? '';
        form.status_of_appointment.value = tempAddData.status_of_appointment ?? '';
        form.govt_service.value = tempAddData.govt_service ?? '';
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
            if(isAddingNew){
                tempAddData = {}; 
            } else {
                tempEditData = {}; 
            }
            form.reset();
            currentEditingRow = null;
        }
    }

    cancelBtn.addEventListener('click', ()=> closeModal(true));
    modal.addEventListener('click', e => { if(e.target===modal) closeModal(false); });

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
                    <th class="px-1 py-1 border w-24">Certificate</th>
                    <th class="px-1 py-1 border w-20">Actions</th>
                </tr>
            </thead>

<tbody>
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
    // ✅ Safe date formatting
    $civil['date_of_exam'] =
        (!empty($civil['date_of_exam']) && $civil['date_of_exam'] !== '0000-00-00' && strtotime($civil['date_of_exam']))
            ? date('F j, Y', strtotime($civil['date_of_exam']))
            : 'N/A';

    $civil['license_valid_until'] =
        (!empty($civil['license_valid_until']) && $civil['license_valid_until'] !== '0000-00-00' && strtotime($civil['license_valid_until']))
            ? date('F j, Y', strtotime($civil['license_valid_until']))
            : 'N/A';

    $civil['eligibility']     = !empty($civil['eligibility']) ? $civil['eligibility'] : 'N/A';
    $civil['rating']          = !empty($civil['rating']) ? $civil['rating'] : 'N/A';
    $civil['place_of_exam']   = !empty($civil['place_of_exam']) ? $civil['place_of_exam'] : 'N/A';
    $civil['license_no']      = !empty($civil['license_no']) ? $civil['license_no'] : 'N/A';
    // ✅ Do not overwrite certificate, keep NULL if none
    ?>

    <tr data-id="<?= esc($civil['id']) ?>">
        <td class="px-1 py-1 border" data-key="eligibility"><?= esc($civil['eligibility']) ?></td>
        <td class="px-1 py-1 border" data-key="rating"><?= esc($civil['rating']) ?></td>
        <td class="px-1 py-1 border" data-key="date_of_exam"><?= esc($civil['date_of_exam']) ?></td>
        <td class="px-1 py-1 border" data-key="place_of_exam"><?= esc($civil['place_of_exam']) ?></td>
        <td class="px-1 py-1 border" data-key="license_no"><?= esc($civil['license_no']) ?></td>
        <td class="px-1 py-1 border" data-key="license_valid_until"><?= esc($civil['license_valid_until']) ?></td>
       <td class="px-1 py-1 border text-center" data-key="certificate">
    <?php if (!empty($civil['certificate'])): ?>
        <button class="viewCertificateBtn text-blue-600 hover:underline px-1"
            data-file="<?= esc($civil['certificate']) ?>">
            View
        </button>
    <?php else: ?>
        N/A
    <?php endif; ?>
</td>

        <td class="px-1 py-1 border text-center">
            <button class="editCivilBtn text-blue-600 px-1">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <button class="deleteCivilBtn text-red-600 px-1">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
<?php endforeach; ?>

<?php else: ?>
    <tr id="noCivilRow">
        <td class="px-2 py-2 border text-center italic text-gray-500" colspan="8">
            No civil service found for this applicant.
        </td>
    </tr>
<?php endif; ?>
</tbody>

        </table>
    </div>

    <div class="flex justify-end gap-2 -mt-1">
        <button id="addCivilBtn"
            class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">
            Add Civil Service
        </button>
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
                <input type="text" name="place_of_exam" required class="w-full border px-2 py-1 rounded text-xs">
            </div>

            <div>
                <label class="text-xs font-semibold">License / PRC No. <span class="text-red-500">*</span></label>
                <input type="text" name="license_no" required class="w-full border px-2 py-1 rounded text-xs">
            </div>

            <div>
                <label class="text-xs font-semibold">Certificate (Upload PDF or Image) <span class="text-red-500">*</span></label>
                <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png" class="w-full border px-2 py-1 rounded text-xs">
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
    function openCertViewer(fileName){
        if(!fileName) return;
        viewerFrame.src = '<?= base_url("account/viewCivilCertificate/") ?>' + encodeURIComponent(fileName);
        certViewer.classList.remove('hidden');
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
        if (!displayDate || displayDate === 'N/A') return '';
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

        if(isEdit){
            const rowId = row.dataset.id;
            currentEditingRowId = rowId;
            certificateInput.removeAttribute('required'); // optional

            originalEditData = {
                id: rowId,
                eligibility: row.querySelector('[data-key="eligibility"]').textContent.trim(),
                rating: row.querySelector('[data-key="rating"]').textContent.trim(),
                date_of_exam: row.querySelector('[data-key="date_of_exam"]').textContent,
                license_valid_until: row.querySelector('[data-key="license_valid_until"]').textContent,
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
            editForm.date_of_exam.value = tempEditData.date_of_exam ?? (originalEditData.date_of_exam !== 'N/A' ? formatToInputDate(originalEditData.date_of_exam) : '');
            editForm.license_valid_until.value = tempEditData.license_valid_until ?? (originalEditData.license_valid_until !== 'N/A' ? formatToInputDate(originalEditData.license_valid_until) : '');
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

        if(isAddMode && !certificateInput.value){
            Swal.fire({icon:'warning',title:'Required Field',text:'Please upload a certificate.'});
            certificateInput.focus();
            return;
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
                Swal.fire('Error',res.message,'error');
            }
        });
    });
});
</script>

<div class="tab-content hidden" id="tab-training">
    <h2 class="text-lg font-bold text-clsuGreen mb-2">Trainings</h2>

    <div class="overflow-x-auto mb-4">
     <table class="table-auto w-full border border-gray-300 text-xs" id="table-training">
    <thead>
        <tr class="bg-gray-100">
            <th class="px-1 py-1 border border-gray-300 w-36">Training Name</th>
            <th class="px-1 py-1 border border-gray-300 w-28">Category</th>
            <th class="px-1 py-1 border border-gray-300 w-28">Venue</th>
            <th class="px-1 py-1 border border-gray-300 w-20">Training From</th>
            <th class="px-1 py-1 border border-gray-300 w-20">Training To</th>
            <th class="px-1 py-1 border border-gray-300 w-28">Facilitator</th>
            <th class="px-1 py-1 border border-gray-300 w-24">Sponsor</th>
            <th class="px-1 py-1 border border-gray-300 w-14">Hours</th>
            <th class="px-1 py-1 border border-gray-300 w-28">Remarks</th>
            <th class="px-1 py-1 border border-gray-300 w-20">Certificate</th>
            <th class="px-1 py-1 border border-gray-300 w-20">TrainingDuration</th>
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
                <td class="px-1 py-1 border" data-key="training_name"><?= esc($training['training_name']) ?></td>
                <td class="px-1 py-1 border" data-key="training_category_id"><?= esc($training['training_category_name']) ?></td>
                <td class="px-1 py-1 border" data-key="training_venue"><?= esc($training['training_venue']) ?></td>
                <td class="px-1 py-1 border" data-key="date_from"><?= esc($training['date_from_formatted']) ?></td>
                <td class="px-1 py-1 border" data-key="date_to"><?= esc($training['date_to_formatted']) ?></td>
                <td class="px-1 py-1 border" data-key="training_facilitator"><?= esc($training['training_facilitator']) ?></td>
                <td class="px-1 py-1 border" data-key="training_sponsor"><?= esc($training['training_sponsor']) ?></td>
                <td class="px-1 py-1 border" data-key="training_hours"><?= esc($training['training_hours']) ?></td>
                <td class="px-1 py-1 border" data-key="training_remarks"><?= esc($training['training_remarks']) ?></td>
<td class="px-1 py-1 border text-center">
    <?php if(!empty($training['certificate_file'])): ?>
        <button class="viewCertificateBtn text-blue-600 hover:underline text-xs" 
                data-file="<?= esc($training['certificate_file']) ?>">
            View
        </button>
    <?php else: ?> - <?php endif; ?>
</td>


                <td class="px-1 py-1 border text-center" data-key="training_duration">
                    <?= esc($training['training_duration'] ?? '-') ?>
                </td>
                <td class="px-1 py-1 border text-center">
                    <button class="editTrainingBtn text-blue-600 px-1"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button class="deleteTrainingBtn text-red-600 px-1"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>

            <!-- Total Duration Row -->
            <tr class="bg-gray-100 font-semibold">
                <td class="px-1 py-1 border text-right" colspan="10">Total Duration:</td>
                <td class="px-1 py-1 border text-center" colspan="2"><?= esc($totalTrainingDuration ?? '-') ?></td>
            </tr>

        <?php else: ?>
            <tr>
                <td class="px-2 py-2 border text-center italic text-gray-500" colspan="12">No trainings found for this applicant.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

    </div>

    <div class="flex justify-end gap-2 -mt-1">
        <button id="addTrainingBtn" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">Add Trainings</button>
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
                <label class="text-xs font-semibold">Certificate File <span class="text-red-500">*</span></label>
                <input type="file" name="training_certificate_file" required class="w-full border px-2 py-1 rounded text-xs">
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
    <div class="bg-white rounded-xl w-full max-w-6xl h-full relative flex flex-col shadow-lg">
        <iframe id="certificateFrame" src="" class="flex-1 w-full h-full border-none"></iframe>
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

        if(viewBtn){
            const fileName = viewBtn.dataset.file;
            certificateFrame.src = '<?= base_url("account/viewTrainingCertificate/") ?>' + encodeURIComponent(fileName);
            certificateModal.classList.remove('hidden');
        }
    });

    // ===== Certificate Modal =====
    function closeCertificateModalFunc() {
        certificateFrame.src = '';
        certificateModal.classList.add('hidden');
    }
    certificateModal.addEventListener('click', e => { if(e.target === certificateModal) closeCertificateModalFunc(); });
    document.addEventListener('keydown', e => { if(e.key === 'Escape' && !certificateModal.classList.contains('hidden')) closeCertificateModalFunc(); });

    // ===== Form Submit =====
    editForm.addEventListener('submit', e => {
        e.preventDefault();

        const dateFrom = new Date(editForm.date_from.value);
        const dateTo = new Date(editForm.date_to.value);
        const today = new Date(); today.setHours(0,0,0,0);

        if(dateFrom > today || dateTo < dateFrom){
            Swal.fire('Invalid Date','Check the date range!','error');
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
    <h2 class="text-xl font-bold text-clsuGreen mb-2">Files</h2>

    <div id="filesContent">
        <div class="overflow-x-auto mb-2">
            <table class="table-auto w-full border-collapse text-xs">

                <!-- PS / Note at the top -->
                <thead>
                    <tr>
                        <th colspan="3" class="px-2 py-1 text-red-600 text-sm border-b text-left">
                            PS: Upload PDF files only, not exceeding 5 MB.
                        </th>
                    </tr>
                </thead>

                <!-- Table Head -->
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 border text-left">Files</th>
                        <th class="px-2 py-1 border text-left w-1/2">Uploads</th>
                        <th class="px-2 py-1 border text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- 1. PDS -->
                    <tr>
                        <th class="px-2 py-1 border text-left">
                            1. Fully accomplished Personal Data Sheet (PDS) with recent passport-sized picture (CS Form No. 212, Revised 2017)
                        </th>
                        <td class="px-2 py-1 border view-mode">
                            <?php if (!empty($fileRecords['pds'])): ?>
                                <button class="viewFileBtn text-blue-600 hover:underline"
                                        data-file="<?= esc($fileRecords['pds']) ?>">
                                    <?= esc($fileRecords['pds']) ?>
                                </button>
                            <?php else: ?>
                                <span class="text-gray-500 italic">No file available</span>
                            <?php endif; ?>
                            <input type="file" name="pds" accept=".pdf" class="w-full text-xs edit-mode hidden">
                        </td>
                     <td class="px-1 py-1 border text-center">
                                <button class="edit-file text-blue-600 px-1"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="delete-file text-red-600 px-1"><i class="fas fa-trash"></i></button>
                        
                        </td>
                    </tr>

                    <!-- 2. Performance Rating -->
                    <tr>
                        <th class="px-2 py-1 border text-left">
                            2. Performance rating in the present position for the last rating period
                        </th>
                        <td class="px-2 py-1 border view-mode">
                            <?php if (!empty($fileRecords['performance_rating'])): ?>
                                <button class="viewFileBtn text-blue-600 hover:underline"
                                        data-file="<?= esc($fileRecords['performance_rating']) ?>">
                                    <?= esc($fileRecords['performance_rating']) ?>
                                </button>
                            <?php else: ?>
                                <span class="text-gray-500 italic">No file available</span>
                            <?php endif; ?>
                            <input type="file" name="performance_rating" accept=".pdf" class="w-full text-xs edit-mode hidden">
                        </td>
                       <td class="px-1 py-1 border text-center">
                                <button class="edit-file text-blue-600 px-1"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="delete-file text-red-600 px-1"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- 3. Resume -->
                    <tr>
                        <th class="px-2 py-1 border text-left">3. Resume</th>
                        <td class="px-2 py-1 border view-mode">
                            <?php if (!empty($fileRecords['resume'])): ?>
                                <button class="viewFileBtn text-blue-600 hover:underline"
                                        data-file="<?= esc($fileRecords['resume']) ?>">
                                    <?= esc($fileRecords['resume']) ?>
                                </button>
                            <?php else: ?>
                                <span class="text-gray-500 italic">No file available</span>
                            <?php endif; ?>
                            <input type="file" name="resume" accept=".pdf" class="w-full text-xs edit-mode hidden">
                        </td>
                    <td class="px-1 py-1 border text-center">
                                <button class="edit-file text-blue-600 px-1"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="delete-file text-red-600 px-1"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- 4. TOR -->
                    <tr>
                        <th class="px-2 py-1 border text-left">4. Transcript of Records</th>
                        <td class="px-2 py-1 border view-mode">
                            <?php if (!empty($fileRecords['tor'])): ?>
                                <button class="viewFileBtn text-blue-600 hover:underline"
                                        data-file="<?= esc($fileRecords['tor']) ?>">
                                    <?= esc($fileRecords['tor']) ?>
                                </button>
                            <?php else: ?>
                                <span class="text-gray-500 italic">No file available</span>
                            <?php endif; ?>
                            <input type="file" name="tor" accept=".pdf" class="w-full text-xs edit-mode hidden">
                        </td>
                        <td class="px-1 py-1 border text-center">
                                <button class="edit-file text-blue-600 px-1"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="delete-file text-red-600 px-1"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- 5. Diploma -->
                    <tr>
                        <th class="px-2 py-1 border text-left">5. Diploma</th>
                        <td class="px-2 py-1 border view-mode">
                            <?php if (!empty($fileRecords['diploma'])): ?>
                                <button class="viewFileBtn text-blue-600 hover:underline"
                                        data-file="<?= esc($fileRecords['diploma']) ?>">
                                    <?= esc($fileRecords['diploma']) ?>
                                </button>
                            <?php else: ?>
                                <span class="text-gray-500 italic">No file available</span>
                            <?php endif; ?>
                            <input type="file" name="diploma" accept=".pdf" class="w-full text-xs edit-mode hidden">
                        </td>
                         <td class="px-1 py-1 border text-center">
                                <button class="edit-file text-blue-600 px-1"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="delete-file text-red-600 px-1"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

   <p class="text-xs text-gray-500 mb-2 view-mode">
    <?php
    if (!empty($fileRecords['uploaded_at']) && $fileRecords['uploaded_at'] != '0000-00-00 00:00:00') {
        $dt = new DateTime($fileRecords['uploaded_at'], new DateTimeZone('Asia/Manila'));
        echo 'Uploaded on: ' . $dt->format('F j, Y h:i A');
    } else {
        echo 'Upload date not available';
    }
    ?>
</p>

    </div>
</div>

<div id="fileViewerModal"
     class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-6xl h-full flex flex-col shadow-lg">
        <iframe id="fileViewerFrame"
                class="flex-1 w-full h-full border-none"></iframe>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const table = document.querySelector('#tab-files table tbody');
    const modal = document.getElementById('fileViewerModal');
    const iframe = document.getElementById('fileViewerFrame');

    if (!table) return;

    table.addEventListener('click', async (e) => {

 const viewBtn = e.target.closest('.viewFileBtn');
if (viewBtn) {
    const filename = viewBtn.dataset.file;
    if (!filename) {
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
        const res = await fetch(`<?= base_url('account/viewFile/') ?>${filename}`);

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

        // File exists → show in modal
        iframe.src = `<?= base_url('account/viewFile/') ?>${filename}`;
        modal.classList.remove('hidden');

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

    return;
}


        // ===== EDIT FILE =====
        const editBtn = e.target.closest('.edit-file');
        if (editBtn) {
            const row = editBtn.closest('tr');
            const fileInput = row.querySelector('input[type="file"]');
            fileInput.click();

            fileInput.addEventListener('change', async () => {
                if (!fileInput.files[0]) return;

                const formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('file_field', fileInput.name);

                try {
                    const res = await fetch('<?= base_url("account/updateFile") ?>', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();

                    if(data.status==='success'){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
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
                            timer: 1500
                        });
                    }
                } catch(err){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong',
                        showConfirmButton: false,
                        timer: 1500
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
            const fileField = fileInput.name;
            const fileLink = row.querySelector('.view-mode .viewFileBtn');
            const fileName = fileLink?.textContent;

            if(!fileName){
                Swal.fire({
                    icon: 'info',
                    title: 'No File',
                    text: 'No file available to delete.',
                    showConfirmButton: false,
                    timer: 1500
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
                formData.append('file_field', fileField);

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
                        timer: 1500
                    });
                }
            } catch(err){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong',
                    showConfirmButton: false,
                    timer: 1500
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

</body>
</html>
