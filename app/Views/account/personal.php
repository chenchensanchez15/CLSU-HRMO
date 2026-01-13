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
.navbar {
    background: #0B6B3A;
    padding: 10px 30px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.nav-links a { color: #fff; margin-right: 20px; font-weight: bold; text-decoration: none; }

.account-menu { position: relative; display: inline-block; }
.account-btn { display: flex; align-items: center; gap: 8px; cursor: pointer; background: none; border: none; color: #fff; font-weight: bold; }
.account-btn img { width: 32px; height: 32px; border-radius: 50%; border: 1px solid #fff; }
.account-dropdown { display: none; position: absolute; right: 0; background: #fff; color: #000; min-width: 160px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 100; }
.account-dropdown a { display: block; padding: 10px; text-decoration: none; color: #0B6B3A; }
.account-dropdown a:hover { background: #f2f2f2; }

/* LAYOUT */
.container { display: flex; padding: 30px; gap: 30px; flex-wrap: wrap; }

/* LEFT PANEL */
.left {
    width: 30%;
    min-width: 250px;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}
.profile-pic { width: 120px; height: 120px; border-radius: 50%; background: #ccc; margin: 0 auto 15px; }
.left h3 { color: #0B6B3A; margin-bottom: 5px; }

/* RIGHT PANEL */
.right { width: 70%; min-width: 300px; }
.card { background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
.card h3 { color: #0B6B3A; margin-bottom: 15px; }

table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }

.status { padding: 5px 10px; border-radius: 5px; color: #fff; font-size: 13px; }
.Pending { background: orange; }
.Approved { background: green; }
.Rejected { background: red; }

@media (max-width: 768px) {
    .container { flex-direction: column; }
    .left, .right { width: 100%; }
}
</style>
<script>
function toggleDropdown() {
    const dropdown = document.getElementById('accountDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}
window.onclick = function(event) {
    if (!event.target.closest('.account-menu')) {
        const dropdown = document.getElementById('accountDropdown');
        if (dropdown) dropdown.style.display = 'none';
    }
}
</script>
</head>
<body>
    
<!-- NAVBAR -->
<header class="bg-clsuGreen text-white py-3 px-6 shadow">
    <div class="flex items-center justify-between max-w-7xl mx-auto">
        
        <!-- LEFT SIDE: Logo + Text -->
        <div class="flex items-center gap-4">
            <img
                src="/HRMO/public/assets/images/clsu-logo2.png"
                alt="CLSU Logo"
                class="w-12 h-auto"
            >
            <div class="flex flex-col leading-tight">
                <span class="text-xl font-bold">
                    CLSU Online Job Application
                </span>
                <span class="text-sm font-medium opacity-90">
                    Human Resource Management Office
                </span>
            </div>
        </div>

   <!-- RIGHT SIDE: Menu Links + Profile -->
<div class="flex items-center gap-12">
    <nav class="hidden md:flex gap-6 font-semibold mt-7"> <!-- added mt-2 -->
        <a href="<?= site_url('dashboard') ?>" class="hover:underline">Home</a>
        <a href="<?= site_url('account/personal') ?>" class="hover:underline">Personal</a>
        <a href="#" class="hover:underline">Trainings</a>
    </nav>
<!-- Profile Dropdown -->
<div class="account-menu relative mt-1">

    <button onclick="toggleDropdown()"
        class="flex items-center gap-1 leading-none focus:outline-none">

        <?php 
        // Use profile photo if exists, otherwise default small head icon
        $photoPath = FCPATH . 'uploads/' . ($profile['photo'] ?? '');
        if (!empty($profile['photo']) && file_exists($photoPath)): ?>
            <img src="<?= base_url('uploads/' . $profile['photo']) ?>" 
                 alt="Profile" 
                 class="w-8 h-8 rounded-full border-2 border-white object-cover">
        <?php else: ?>
            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center border-2 border-white">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="w-5 h-5 text-gray-500" 
                     fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" 
                          d="M12 2a5 5 0 100 10 5 5 0 000-10zm-7 18a7 7 0 0114 0H5z" 
                          clip-rule="evenodd"/>
                </svg>
            </div>
        <?php endif; ?>

        <!-- Dropdown Arrow -->
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-4 w-4 text-white"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7"/>
        </svg>

    </button>

    <!-- Dropdown Menu -->
    <div id="accountDropdown"
         class="account-dropdown absolute right-0 mt-2 hidden bg-white text-black min-w-[160px] rounded shadow-lg z-50">
       
           <!-- Account Link -->
    <a href="<?= site_url('account/changePassword') ?>" 
       class="block px-4 py-2 hover:bg-gray-100">
        Account
    </a>
      <a href="<?= site_url('logout') ?>" class="block px-4 py-2 hover:bg-gray-100">
            Logout
        </a>
    </div>

</div>


</div>
    </div>
</header>



<main class="max-w-5xl mx-auto bg-white my-10 p-8 rounded-lg shadow">

<div id="readView" class="space-y-8">
<div class="flex flex-col md:flex-row items-center md:items-start gap-6 border-b pb-6">

    <div class="w-36 h-36 rounded-full border-4 border-clsuGreen overflow-hidden bg-gray-200 flex items-center justify-center">
        <?php if (!empty($profile['photo'] ?? null) && file_exists(FCPATH.'uploads/'.$profile['photo'])): ?>
            <img src="<?= base_url('uploads/'.$profile['photo']) ?>" 
                 alt="Profile Photo"
                 class="w-full h-full object-cover">
        <?php else: ?>
            <!-- Default Head Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" 
                 class="w-16 h-16 text-gray-500" 
                 fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M12 2a5 5 0 100 10 5 5 0 000-10zm-7 18a7 7 0 0114 0H5z" clip-rule="evenodd"/>
            </svg>
        <?php endif; ?>
    </div>
<div class="text-center md:text-left">
    <!-- Name from users table -->
    <h2 class="text-2xl md:text-3xl font-bold uppercase text-clsuGreen leading-snug">
        <?= esc(
            trim(
                strtoupper($user['first_name'] ?? '') . ' ' .
                (!empty($user['middle_name'] ?? '') ? strtoupper(substr($user['middle_name'],0,1)) . '. ' : '') .
                strtoupper($user['last_name'] ?? '') . ' ' .
                strtoupper($user['suffix'] ?? '')
            )
        ) ?>
    </h2>

    <!-- Email from users table -->
    <p class="mt-1 text-base text-gray-700"><?= esc($user['email'] ?? '—') ?></p>

    <!-- Phone from applicant_profiles table -->
    <p class="mt-1 text-base text-gray-700"><?= esc($profile['phone'] ?? '—') ?></p>
</div>

</div>

<div>
<h3 class="font-semibold text-clsuGreen mb-2">I. Personal Information</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
<p><strong>Date of Birth:</strong> <?= esc($profile['date_of_birth'] ?? '—') ?></p>
<p><strong>Place of Birth:</strong> <?= esc($profile['place_of_birth'] ?? '—') ?></p>
<p><strong>Sex:</strong> <?= esc($profile['sex'] ?? '—') ?></p>
<p><strong>Civil Status:</strong> <?= esc($profile['civil_status'] ?? '—') ?></p>
<p><strong>Citizenship:</strong> <?= esc($profile['citizenship'] ?? '—') ?></p>
<p><strong>Height (m):</strong> <?= esc($profile['height'] ?? '—') ?></p>
<p><strong>Weight (kg):</strong> <?= esc($profile['weight'] ?? '—') ?></p>
<p><strong>Blood Type:</strong> <?= esc($profile['blood_type'] ?? '—') ?></p>
</div>
</div>

<div>
<h3 class="font-semibold text-clsuGreen mb-2">II. Contact Details</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
<p><strong>Mobile Number:</strong> <?= esc($profile['phone'] ?? '—') ?></p>
<p><strong>Email Address:</strong> <?= esc($profile['email'] ?? '—') ?></p>
<p><strong>Residential Address:</strong> <?= esc($profile['residential_address'] ?? '—') ?></p>
<p><strong>Permanent Address:</strong> <?= esc($profile['permanent_address'] ?? '—') ?></p>
</div>
</div>

<div>
<h3 class="font-semibold text-clsuGreen mb-2">III. Educational Background</h3>
<p class="text-sm whitespace-pre-line"><?= esc($profile['education'] ?? '—') ?></p>
</div>

<div>
<h3 class="font-semibold text-clsuGreen mb-2">IV. Training Programs Attended</h3>
<p class="text-sm whitespace-pre-line"><?= esc($profile['training'] ?? '—') ?></p>
</div>

<div>
<h3 class="font-semibold text-clsuGreen mb-2">V. Work Experience</h3>
<p class="text-sm whitespace-pre-line"><?= esc($profile['experience'] ?? '—') ?></p>
</div>

<div>
<h3 class="font-semibold text-clsuGreen mb-2">VI. Eligibility</h3>
<p class="text-sm"><?= esc($profile['eligibility'] ?? '—') ?></p>
</div>

<div>
<h3 class="font-semibold text-clsuGreen mb-2">VII. Core Competencies</h3>
<p class="text-sm"><?= esc($profile['competency'] ?? '—') ?></p>
</div>

<div>
<h3 class="font-semibold text-clsuGreen mb-2">VIII. Attachments</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
<p><strong>Resume:</strong> <?= !empty($profile['resume'] ?? null) ? '<span class="text-green-700 font-semibold">Uploaded</span>' : '—' ?></p>
<p><strong>Photo:</strong> <?= !empty($profile['photo'] ?? null) ? '<span class="text-green-700 font-semibold">Uploaded</span>' : '—' ?></p>
</div>
</div>

<div class="pt-6 flex justify-end">
<button onclick="showEdit()" class="bg-clsuGold px-6 py-2 rounded font-semibold">Update Profile</button>
</div>

</div>
<div id="editView" class="hidden">

<h2 class="text-lg font-semibold mb-4 text-clsuGreen">Profile Information</h2>

<form action="<?= site_url('account/update') ?>" method="post" enctype="multipart/form-data" class="space-y-6">
<?= csrf_field() ?>

<!-- Full Name -->
<div>
  <label class="block font-semibold mb-2">Full Name</label>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div>
      <label class="block text-sm mb-1">First Name</label>
      <input type="text" name="first_name" value="<?= esc($profile['first_name'] ?? '') ?>" class="border rounded px-3 py-2 w-full">
    </div>
    <div>
      <label class="block text-sm mb-1">Middle Name</label>
      <input type="text" name="middle_name" value="<?= esc($profile['middle_name'] ?? '') ?>" class="border rounded px-3 py-2 w-full">
    </div>
    <div>
      <label class="block text-sm mb-1">Last Name</label>
      <input type="text" name="last_name" value="<?= esc($profile['last_name'] ?? '') ?>" class="border rounded px-3 py-2 w-full">
    </div>
    <div>
      <label class="block text-sm mb-1">Suffix</label>
      <input type="text" name="suffix" value="<?= esc($profile['suffix'] ?? '') ?>" class="border rounded px-3 py-2 w-full">
    </div>
  </div>
</div>

<!-- Personal Info -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div>
    <label class="block text-sm mb-1">Date of Birth</label>
    <input type="date" name="date_of_birth" value="<?= esc($profile['date_of_birth'] ?? '') ?>" class="border rounded px-3 py-2 w-full">
  </div>
  <div>
    <label class="block text-sm mb-1">Place of Birth</label>
    <input type="text" name="place_of_birth" value="<?= esc($profile['place_of_birth'] ?? '') ?>" class="border rounded px-3 py-2 w-full">
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div>
    <label class="block text-sm mb-1">Sex</label>
    <select name="sex" class="border rounded px-3 py-2 w-full">
      <option value="">Select Sex</option>
      <option <?= ($profile['sex'] ?? '')=='Male'?'selected':'' ?>>Male</option>
      <option <?= ($profile['sex'] ?? '')=='Female'?'selected':'' ?>>Female</option>
    </select>
  </div>
  <div>
    <label class="block text-sm mb-1">Civil Status</label>
    <select name="civil_status" class="border rounded px-3 py-2 w-full">
      <option value="">Select Status</option>
      <option <?= ($profile['civil_status'] ?? '')=='Single'?'selected':'' ?>>Single</option>
      <option <?= ($profile['civil_status'] ?? '')=='Married'?'selected':'' ?>>Married</option>
      <option <?= ($profile['civil_status'] ?? '')=='Widowed'?'selected':'' ?>>Widowed</option>
      <option <?= ($profile['civil_status'] ?? '')=='Divorced'?'selected':'' ?>>Divorced</option>
      <option <?= ($profile['civil_status'] ?? '')=='Separated'?'selected':'' ?>>Separated</option>
    </select>
  </div>
  <div>
    <label class="block text-sm mb-1">Citizenship</label>
    <input type="text" name="citizenship" value="<?= esc($profile['citizenship'] ?? '') ?>" class="border rounded px-3 py-2 w-full">
  </div>
</div>

<!-- Contact Info -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div>
    <label class="block text-sm mb-1">Mobile Number</label>
    <input type="text" name="phone" value="<?= esc($profile['phone'] ?? '') ?>" class="border rounded px-3 py-2 w-full">
  </div>
  <div>
    <label class="block text-sm mb-1">Email Address</label>
    <input type="email" name="email" value="<?= esc($profile['email'] ?? '') ?>" class="border rounded px-3 py-2 w-full">
  </div>
</div>

<!-- Addresses -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div>
    <label class="block text-sm mb-1">Residential Address</label>
    <textarea name="residential_address" class="border rounded px-3 py-2 w-full"><?= esc($profile['residential_address'] ?? '') ?></textarea>
  </div>
  <div>
    <label class="block text-sm mb-1">Permanent Address</label>
    <textarea name="permanent_address" class="border rounded px-3 py-2 w-full"><?= esc($profile['permanent_address'] ?? '') ?></textarea>
  </div>
</div>

<!-- File Uploads -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div>
    <label class="block text-sm mb-1">Upload Resume</label>
    <input type="file" name="resume" class="border rounded px-3 py-2 w-full">
  </div>
  <div>
    <label class="block text-sm mb-1">Upload Photo</label>
    <input type="file" name="photo" class="border rounded px-3 py-2 w-full">
  </div>
</div>

<!-- Buttons -->
<div class="flex justify-end gap-3 pt-4">
  <button type="button" onclick="cancelEdit()" class="bg-gray-400 text-white px-6 py-2 rounded">Cancel</button>
  <button type="submit" class="bg-clsuGreen text-white px-6 py-2 rounded font-semibold">Save Changes</button>
</div>

</form>
</div>

</main>

<footer class="w-full bg-gray-100 py-4 mt-auto border-t">
    <div class="flex justify-end px-6 text-xs text-gray-600">
        <div class="text-right">
            &copy; <?= date('Y') ?> CLSU-HRMO. All rights reserved.<br>
            Powered by <span class="text-green-700">Management Information System Office (CLSU-MISO)</span>
        </div>
    </div>
</footer>
<script>
function showEdit() {
    document.getElementById('readView').classList.add('hidden');
    document.getElementById('editView').classList.remove('hidden');
}

function cancelEdit() {
    document.getElementById('editView').classList.add('hidden');
    document.getElementById('readView').classList.remove('hidden');
}
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (session()->getFlashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= session()->getFlashdata('success') ?>',
            confirmButtonColor: '#0B6B3A'
        }).then((result) => {
            if (result.isConfirmed) {
                // Reload the page to show updated profile
                window.location.href = '<?= site_url('account/personal') ?>';
            }
        });
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: '<?= session()->getFlashdata('error') ?>',
            confirmButtonColor: '#0B6B3A'
        });
    <?php endif; ?>
});
</script>

</body>
</html>