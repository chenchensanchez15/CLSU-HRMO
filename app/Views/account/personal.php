<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Account Settings | CLSU HRMO</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

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

function showEdit() {
    document.getElementById('readView').classList.add('hidden');
    document.getElementById('editView').classList.remove('hidden');
}

function cancelEdit() {
    document.getElementById('editView').classList.add('hidden');
    document.getElementById('readView').classList.remove('hidden');
}

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

<body class="bg-gray-100 min-h-screen">

<!-- NAVBAR -->
<div class="navbar bg-clsuGreen text-white flex items-center justify-between px-6 py-3">

    <!-- LEFT SIDE: Logo + System Name -->
    <div class="flex items-center gap-4">
        <img src="/HRMO/public/assets/clsu-logo.png" alt="CLSU Logo" class="w-12 h-12 object-contain">
        <div class="flex flex-col leading-tight">
            <span class="font-bold text-lg">Online Job Application System</span>
            <span class="text-sm">Central Luzon State University</span>
        </div>
    </div>

    <!-- MIDDLE: Menu Links -->
    <div class="hidden md:flex gap-6 font-semibold">
        <a href="<?= site_url('dashboard') ?>" class="hover:underline">Home</a>
        <a href="<?= site_url('account/personal') ?>" class="hover:underline">Personal</a>
        <a href="#" class="hover:underline">Trainings</a>
    </div>

    <!-- RIGHT SIDE: Account Photo + Dropdown -->
    <div class="account-menu relative">
        <?php $photo = !empty($profile['photo']) ? $profile['photo'] : 'default-avatar.png'; ?>
        <button onclick="toggleDropdown()" class="flex items-center gap-2 bg-none border-none cursor-pointer">
            <img src="<?= base_url('uploads/'.$photo) ?>" 
                 alt="Profile" 
                 class="w-10 h-10 rounded-full border-2 border-white object-cover">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div id="accountDropdown" class="account-dropdown absolute right-0 mt-2 hidden bg-white text-black min-w-[160px] rounded shadow-lg z-50">
            <a href="<?= site_url('account/personal') ?>" class="block px-4 py-2 hover:bg-gray-100">Personal</a>
            <a href="<?= site_url('logout') ?>" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
        </div>
    </div>

</div>

<main class="max-w-5xl mx-auto bg-white my-10 p-8 rounded-lg shadow">

<!-- ================= READ VIEW ================= -->
<div id="readView" class="space-y-8">

<!-- PROFILE HEADER -->
<div class="flex flex-col md:flex-row items-center md:items-start gap-6 border-b pb-6">

    <!-- PHOTO -->
    <div class="w-36 h-36 rounded-full border-4 border-clsuGreen overflow-hidden bg-gray-200 flex items-center justify-center">
        <?php if (!empty($profile['photo'])): ?>
            <img src="<?= base_url('uploads/'.$profile['photo']) ?>" 
                 alt="Profile Photo"
                 class="w-full h-full object-cover">
        <?php else: ?>
            <span class="text-gray-500 text-sm text-center px-2">
                No Photo<br>Uploaded
            </span>
        <?php endif; ?>
    </div>

    <!-- NAME + EMAIL -->
    <div class="text-center md:text-left">
        <h2 class="text-3xl md:text-4xl font-extrabold uppercase text-clsuGreen leading-tight">
            <?= esc(
                trim(
                    strtoupper($profile['first_name'].' ').
                    ($profile['middle_name']
                        ? strtoupper(substr($profile['middle_name'],0,1)).'. '
                        : ''). 
                    strtoupper($profile['last_name'].' ').
                    strtoupper($profile['suffix'] ?? '')
                )
            ) ?>
        </h2>
        <p class="mt-2 text-lg text-gray-700 font-medium"><?= esc($profile['email'] ?? '—') ?></p>
        <p class="text-sm text-gray-500 mt-1">Applicant Profile</p>
    </div>

</div>

<!-- PERSONAL INFORMATION -->
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

<!-- CONTACT DETAILS -->
<div>
<h3 class="font-semibold text-clsuGreen mb-2">II. Contact Details</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
<p><strong>Mobile Number:</strong> <?= esc($profile['phone'] ?? '—') ?></p>
<p><strong>Email Address:</strong> <?= esc($profile['email'] ?? '—') ?></p>
<p><strong>Residential Address:</strong> <?= esc($profile['residential_address'] ?? '—') ?></p>
<p><strong>Permanent Address:</strong> <?= esc($profile['permanent_address'] ?? '—') ?></p>
</div>
</div>

<!-- EDUCATION -->
<div>
<h3 class="font-semibold text-clsuGreen mb-2">III. Educational Background</h3>
<p class="text-sm whitespace-pre-line"><?= esc($profile['education'] ?? '—') ?></p>
</div>

<!-- TRAINING -->
<div>
<h3 class="font-semibold text-clsuGreen mb-2">IV. Training Programs Attended</h3>
<p class="text-sm whitespace-pre-line"><?= esc($profile['training'] ?? '—') ?></p>
</div>

<!-- EXPERIENCE -->
<div>
<h3 class="font-semibold text-clsuGreen mb-2">V. Work Experience</h3>
<p class="text-sm whitespace-pre-line"><?= esc($profile['experience'] ?? '—') ?></p>
</div>

<!-- ELIGIBILITY -->
<div>
<h3 class="font-semibold text-clsuGreen mb-2">VI. Eligibility</h3>
<p class="text-sm"><?= esc($profile['eligibility'] ?? '—') ?></p>
</div>

<!-- COMPETENCY -->
<div>
<h3 class="font-semibold text-clsuGreen mb-2">VII. Core Competencies</h3>
<p class="text-sm"><?= esc($profile['competency'] ?? '—') ?></p>
</div>

<!-- ATTACHMENTS -->
<div>
<h3 class="font-semibold text-clsuGreen mb-2">VIII. Attachments</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
<p><strong>Resume:</strong> <?= !empty($profile['resume']) ? '<span class="text-green-700 font-semibold">Uploaded</span>' : '—' ?></p>
<p><strong>Photo:</strong> <?= !empty($profile['photo']) ? '<span class="text-green-700 font-semibold">Uploaded</span>' : '—' ?></p>
</div>
</div>

<!-- ACTION -->
<div class="pt-6 flex justify-end">
<button onclick="showEdit()" class="bg-clsuGold px-6 py-2 rounded font-semibold">Update Profile</button>
</div>

</div>

<!-- ================= EDIT VIEW ================= -->
<div id="editView" class="hidden">

<h2 class="text-lg font-semibold mb-4 text-clsuGreen">Profile Information (PDS – CSC Form 212)</h2>

<form action="<?= site_url('account/update') ?>" method="post" enctype="multipart/form-data" class="space-y-6">
<?= csrf_field() ?>

<!-- Full Name -->
<div>
<label class="block font-semibold mb-2">Full Name</label>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
<input type="text" name="first_name" value="<?= esc($profile['first_name'] ?? '') ?>" class="border rounded px-3 py-2">
<input type="text" name="middle_name" value="<?= esc($profile['middle_name'] ?? '') ?>" class="border rounded px-3 py-2">
<input type="text" name="last_name" value="<?= esc($profile['last_name'] ?? '') ?>" class="border rounded px-3 py-2">
<input type="text" name="suffix" value="<?= esc($profile['suffix'] ?? '') ?>" class="border rounded px-3 py-2">
</div>
</div>

<!-- Birth -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<input type="date" name="date_of_birth" value="<?= esc($profile['date_of_birth'] ?? '') ?>" class="border rounded px-3 py-2">
<input type="text" name="place_of_birth" value="<?= esc($profile['place_of_birth'] ?? '') ?>" class="border rounded px-3 py-2">
</div>

<!-- Sex / Civil Status -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<select name="sex" class="border rounded px-3 py-2">
<option value="">Select Sex</option>
<option <?= ($profile['sex'] ?? '')=='Male'?'selected':'' ?>>Male</option>
<option <?= ($profile['sex'] ?? '')=='Female'?'selected':'' ?>>Female</option>
</select>

<select name="civil_status" class="border rounded px-3 py-2">
<option value="">Civil Status</option>
<option <?= ($profile['civil_status'] ?? '')=='Single'?'selected':'' ?>>Single</option>
<option <?= ($profile['civil_status'] ?? '')=='Married'?'selected':'' ?>>Married</option>
<option <?= ($profile['civil_status'] ?? '')=='Widowed'?'selected':'' ?>>Widowed</option>
<option <?= ($profile['civil_status'] ?? '')=='Divorced'?'selected':'' ?>>Divorced</option>
<option <?= ($profile['civil_status'] ?? '')=='Separated'?'selected':'' ?>>Separated</option>
</select>

<input type="text" name="citizenship" value="<?= esc($profile['citizenship'] ?? '') ?>" class="border rounded px-3 py-2">
</div>

<!-- Contact -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<input type="text" name="phone" value="<?= esc($profile['phone'] ?? '') ?>" class="border rounded px-3 py-2">
<input type="email" name="email" value="<?= esc($profile['email'] ?? '') ?>" class="border rounded px-3 py-2">
</div>

<!-- Addresses -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<textarea name="residential_address" class="border rounded px-3 py-2"><?= esc($profile['residential_address'] ?? '') ?></textarea>
<textarea name="permanent_address" class="border rounded px-3 py-2"><?= esc($profile['permanent_address'] ?? '') ?></textarea>
</div>

<!-- Resume & Photo -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<input type="file" name="resume" class="border rounded px-3 py-2">
<input type="file" name="photo" class="border rounded px-3 py-2">
</div>

<!-- Buttons -->
<div class="flex justify-end gap-3 pt-4">
<button type="button" onclick="cancelEdit()" class="bg-gray-400 text-white px-6 py-2 rounded">Cancel</button>
<button type="submit" class="bg-clsuGreen text-white px-6 py-2 rounded font-semibold">Save Changes</button>
</div>

</form>
</div>

</main>

<footer class="bg-clsuGreen text-white text-center py-4">
<p>&copy; 2026 Central Luzon State University – Human Resources System Office</p>
</footer>

</body>
</html>
