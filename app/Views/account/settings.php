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
</script>
</head>

<body class="bg-gray-100 min-h-screen">

<header class="bg-clsuGreen text-white px-8 py-5">
    <h1 class="text-xl font-bold">CLSU Online Job Application</h1>
</header>

<main class="max-w-5xl mx-auto bg-white my-10 p-8 rounded-lg shadow">

    <h2 class="text-lg font-semibold mb-4 text-clsuGreen">Update Your Profile </h2>
<form action="<?= site_url('account/update') ?>" method="post" enctype="multipart/form-data" class="space-y-6">
    <?= csrf_field() ?>

    <!-- Full Name -->
    <div>
        <label class="block font-semibold mb-2">Full Name</label>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="first_name" value="<?= esc($profile['first_name'] ?? '') ?>" placeholder="First Name *" class="border rounded px-3 py-2" required>
            <input type="text" name="middle_name" value="<?= esc($profile['middle_name'] ?? '') ?>" placeholder="Middle Name" class="border rounded px-3 py-2">
            <input type="text" name="last_name" value="<?= esc($profile['last_name'] ?? '') ?>" placeholder="Last Name *" class="border rounded px-3 py-2" required>
            <input type="text" name="suffix" value="<?= esc($profile['suffix'] ?? '') ?>" placeholder="Suffix" class="border rounded px-3 py-2">
        </div>
    </div>

    <!-- Date of Birth / Place of Birth -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="date" name="date_of_birth" value="<?= esc($profile['date_of_birth'] ?? '') ?>" class="border rounded px-3 py-2">
        <input type="text" name="place_of_birth" value="<?= esc($profile['place_of_birth'] ?? '') ?>" placeholder="Place of Birth" class="border rounded px-3 py-2">
    </div>

    <!-- Sex / Civil Status / Citizenship -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <select name="sex" class="border rounded px-3 py-2">
            <option value="">Sex *</option>
            <option <?= ($profile['sex'] ?? '')=='Male'?'selected':'' ?>>Male</option>
            <option <?= ($profile['sex'] ?? '')=='Female'?'selected':'' ?>>Female</option>
        </select>
        <select name="civil_status" class="border rounded px-3 py-2">
            <option value="">Civil Status *</option>
            <option <?= ($profile['civil_status'] ?? '')=='Single'?'selected':'' ?>>Single</option>
            <option <?= ($profile['civil_status'] ?? '')=='Married'?'selected':'' ?>>Married</option>
            <option <?= ($profile['civil_status'] ?? '')=='Widowed'?'selected':'' ?>>Widowed</option>
            <option <?= ($profile['civil_status'] ?? '')=='Divorced'?'selected':'' ?>>Divorced</option>
            <option <?= ($profile['civil_status'] ?? '')=='Separated'?'selected':'' ?>>Separated</option>
        </select>
        <input type="text" name="citizenship" value="<?= esc($profile['citizenship'] ?? '') ?>" placeholder="Citizenship" class="border rounded px-3 py-2">
    </div>

    <!-- Height / Weight / Blood Type -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="number" step="0.01" name="height" value="<?= esc($profile['height'] ?? '') ?>" placeholder="Height (m)" class="border rounded px-3 py-2">
        <input type="number" step="0.01" name="weight" value="<?= esc($profile['weight'] ?? '') ?>" placeholder="Weight (kg)" class="border rounded px-3 py-2">
        <input type="text" name="blood_type" value="<?= esc($profile['blood_type'] ?? '') ?>" placeholder="Blood Type" class="border rounded px-3 py-2">
    </div>

    <!-- Contact Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="text" name="phone" value="<?= esc($profile['phone'] ?? '') ?>" placeholder="Mobile Number" class="border rounded px-3 py-2">
        <input type="email" name="email" value="<?= esc($profile['email'] ?? '') ?>" placeholder="Email Address" class="border rounded px-3 py-2">
    </div>

    <!-- Addresses -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <textarea name="residential_address" placeholder="Residential Address" class="border rounded px-3 py-2"><?= esc($profile['residential_address'] ?? '') ?></textarea>
        <textarea name="permanent_address" placeholder="Permanent Address" class="border rounded px-3 py-2"><?= esc($profile['permanent_address'] ?? '') ?></textarea>
    </div>

    <!-- Education / Training / Experience -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <textarea name="education" placeholder="Education" class="border rounded px-3 py-2"><?= esc($profile['education'] ?? '') ?></textarea>
        <textarea name="training" placeholder="Training" class="border rounded px-3 py-2"><?= esc($profile['training'] ?? '') ?></textarea>
        <textarea name="experience" placeholder="Experience" class="border rounded px-3 py-2"><?= esc($profile['experience'] ?? '') ?></textarea>
    </div>

    <!-- Eligibility / Competency / Resume -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="text" name="eligibility" value="<?= esc($profile['eligibility'] ?? '') ?>" placeholder="Eligibility" class="border rounded px-3 py-2">
        <input type="text" name="competency" value="<?= esc($profile['competency'] ?? '') ?>" placeholder="Competency" class="border rounded px-3 py-2">
        <input type="file" name="resume" class="border rounded px-3 py-2">
    </div>

    <!-- Photo Upload -->
    <div>
        <label class="block font-semibold mb-2">Upload Photo</label>
        <input type="file" name="photo" class="border rounded px-3 py-2">
    </div>

    <div class="text-right">
        <button type="submit" class="bg-clsuGreen text-white px-6 py-3 rounded font-semibold hover:bg-green-800">
            Update Profile
        </button>
    </div>
</form>

</main>

<footer class="bg-clsuGreen text-white text-center py-4">
    <p>&copy; 2026 Central Luzon State University – HRMO</p>
</footer>

</body>
</html>
