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
                <span class="text-sm font-medium opacity-90">Human Resource Management Office</span>
            </div>
        </div>
        <div class="flex items-center gap-12">
            <nav class="hidden md:flex gap-6 font-semibold mt-7">
                <a href="<?= site_url('dashboard') ?>" class="hover:underline">Home</a>
        <a href="<?= site_url('account/personal') ?>"
   class="<?= service('uri')->getSegment(1) === 'account' && service('uri')->getSegment(2) === 'personal'
        ? 'text-clsuGold border-b-2 border-clsuGold pb-0.5'
        : 'hover:underline' ?>">
    Personal
</a>

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

<!-- Right Panel -->
<div class="right w-full flex-1 space-y-6">
  <div class="w-full bg-white shadow rounded-lg p-5 text-gray-700 text-sm">
    <h2 class="text-xl font-bold text-clsuGreen mb-2">Personal Information</h2>

    <!-- Name -->
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

    <!-- Basic Info -->
    <div class="overflow-x-auto mb-4">
      <table class="table-auto w-full text-left border-collapse text-xs" id="table-basic">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-2 py-1 border">Sex</th>
            <th class="px-2 py-1 border">Date of Birth</th>
            <th class="px-2 py-1 border">Place of Birth</th>
            <th class="px-2 py-1 border">Civil Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="px-2 py-1 border" data-key="sex"><?= esc($profile['sex'] ?? '-') ?></td>
            <td class="px-2 py-1 border" data-key="date_of_birth"><?= esc($profile['date_of_birth'] ?? '-') ?></td>
            <td class="px-2 py-1 border" data-key="place_of_birth"><?= esc($profile['place_of_birth'] ?? '-') ?></td>
            <td class="px-2 py-1 border" data-key="civil_status"><?= esc($profile['civil_status'] ?? '-') ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Contact Info -->
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

    <!-- Physical Info -->
    <div class="overflow-x-auto mb-4">
      <table class="table-auto w-full text-left border-collapse text-xs" id="table-physical">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-2 py-1 border">Height (cm)</th>
            <th class="px-2 py-1 border">Weight (kg)</th>
            <th class="px-2 py-1 border" colspan="2">Blood Type</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="px-2 py-1 border" data-key="height"><?= esc($profile['height'] ?? '-') ?></td>
            <td class="px-2 py-1 border" data-key="weight"><?= esc($profile['weight'] ?? '-') ?></td>
            <td class="px-2 py-1 border" colspan="2" data-key="blood_type"><?= esc($profile['blood_type'] ?? '-') ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Addresses -->
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
<!-- Resume & Photo -->
<div class="overflow-x-auto mb-3">
  <table class="table-auto w-full text-left border-collapse text-xs" id="table-resume">
    <thead>
      <tr class="bg-gray-100">
        <th class="px-2 py-1 border">Resume</th>
        <th class="px-2 py-1 border">Picture</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td class="px-2 py-1 border" data-key="resume">
    <!-- Resume -->
<?php if (!empty($profile['resume'])): ?>
<a href="<?= site_url('applications/viewResume/'.$profile['id']) ?>" target="_blank" class="text-blue-600 hover:underline">
    <?= esc($profile['resume']) ?>
</a>
<?php else: ?>
-
<?php endif; ?>

    </td>
    <!-- Photo -->
        <td class="px-2 py-1 border" data-key="photo">
          <?php if (!empty($profile['photo']) && file_exists(FCPATH.'uploads/'.$profile['photo'])): ?>
           <a href="<?= site_url('applications/viewPhoto/'.$user['id']) ?>" target="_blank" class="text-blue-600 hover:underline">View Photo</a>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<!-- Update & Cancel Buttons -->
<div class="flex justify-end -mt-1 gap-2">
  <button id="cancelBtn" class="bg-gray-400 px-4 py-1.5 rounded text-xs font-semibold hidden">Cancel</button>
  <button id="saveBtn" class="bg-clsuGreen px-4 py-1.5 rounded text-xs font-semibold hidden">Save</button>
  <button id="editBtn" class="bg-clsuGold px-4 py-1.5 rounded text-xs font-semibold">Update Profile</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');
const cancelBtn = document.getElementById('cancelBtn');

let originalData = {};

const sexOptions = ['Male','Female'];
const civilStatusOptions = ['Single','Married','Widowed','Divorced','Separated'];
const bloodTypeOptions = ['A+', 'A-','B+','B-','AB+','AB-','O+','O-'];

editBtn.addEventListener('click', () => {
    originalData = {};
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        table.querySelectorAll('td[data-key]').forEach(td => {
            originalData[td.dataset.key] = td.textContent.trim();
            let value = td.textContent.trim();
            if(value === '-') value = '';

            switch(td.dataset.key){
                case 'sex':
                    td.innerHTML = `<select class="w-full px-1 text-xs">${sexOptions.map(s => `<option value="${s}" ${s===value?'selected':''}>${s}</option>`).join('')}</select>`;
                    break;
                case 'civil_status':
                    td.innerHTML = `<select class="w-full px-1 text-xs">${civilStatusOptions.map(s => `<option value="${s}" ${s===value?'selected':''}>${s}</option>`).join('')}</select>`;
                    break;
                case 'date_of_birth':
                    td.innerHTML = `<input type="date" value="${value}" class="w-full px-1 text-xs"/>`;
                    break;
                case 'height':
                case 'weight':
                    td.innerHTML = `<input type="number" step="0.01" value="${value}" class="w-full px-1 text-xs"/>`;
                    break;
                case 'blood_type':
                    td.innerHTML = `<select class="w-full  px-1 text-xs">${bloodTypeOptions.map(b => `<option value="${b}" ${b===value?'selected':''}>${b}</option>`).join('')}</select>`;
                    break;
                case 'resume':
                case 'photo':
                    td.innerHTML = `<input type="file" name="${td.dataset.key}" class="w-full text-xs"/>`;
                    break;
                default:
                    td.innerHTML = `<input type="text" value="${value}" class="w-full px-1 text-xs"/>`;
            }
        });
    });

    editBtn.classList.add('hidden');
    saveBtn.classList.remove('hidden');
    cancelBtn.classList.remove('hidden');
});

cancelBtn.addEventListener('click', () => {
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        table.querySelectorAll('td[data-key]').forEach(td => {
            td.textContent = originalData[td.dataset.key] || '-';
        });
    });

    editBtn.classList.remove('hidden');
    saveBtn.classList.add('hidden');
    cancelBtn.classList.add('hidden');
});

saveBtn.addEventListener('click', () => {
    const tables = document.querySelectorAll('table');
    const formData = new FormData();

    tables.forEach(table => {
        table.querySelectorAll('td[data-key]').forEach(td => {
            const input = td.querySelector('input, select');
            if(input){
                if(input.type === 'file'){
                    if(input.files.length > 0) formData.append(td.dataset.key, input.files[0]);
                } else {
                    formData.append(td.dataset.key, input.value.trim());
                }
            }
        });
    });

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
            willClose: () => {
                // Refresh the page after SweetAlert closes
                window.location.reload();
            }
        });

        if(res.photo){
            const photoUrl = `<?= base_url('uploads') ?>/${res.photo}?t=${Date.now()}`;

            /* ===== HEADER AVATAR ===== */
            const accountMenu = document.querySelector('.account-menu button');
            let headerImg = accountMenu.querySelector('img');

            if(!headerImg){
                accountMenu.querySelector('div')?.remove(); // remove SVG wrapper
                headerImg = document.createElement('img');
                headerImg.className = 'w-8 h-8 rounded-full border-2 border-white object-cover';
                accountMenu.prepend(headerImg);
            }
            headerImg.src = photoUrl;

            /* ===== LEFT PANEL PHOTO ===== */
            const profilePic = document.querySelector('.profile-pic');
            let leftImg = profilePic.querySelector('img');

            if(!leftImg){
                profilePic.innerHTML = '';
                leftImg = document.createElement('img');
                leftImg.className = 'w-full h-full object-cover rounded-full';
                profilePic.appendChild(leftImg);
            }
            leftImg.src = photoUrl;
        }

        // Optional: hide buttons immediately (SweetAlert will show)
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

</body>
</html>
