<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Profile | CLSU HRMO</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">

<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-6 text-clsuGreen">Update Profile</h2>

    <form action="<?= site_url('account/update') ?>" method="post" enctype="multipart/form-data" class="space-y-4">
        
        <div>
            <label class="block text-gray-700">First Name</label>
            <input type="text" name="first_name" value="<?= esc($user['first_name']) ?>" class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label class="block text-gray-700">Last Name</label>
            <input type="text" name="last_name" value="<?= esc($user['last_name']) ?>" class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" value="<?= esc($user['email']) ?>" class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label class="block text-gray-700">Contact</label>
            <input type="text" name="contact" value="<?= esc($user['contact']) ?>" class="w-full border p-2 rounded">
        </div>

        <div>
            <label class="block text-gray-700">Profile Photo</label>
            <input type="file" name="photo" class="w-full border p-2 rounded">
            <?php if(!empty($user['photo'])): ?>
                <img src="<?= base_url('uploads/' . esc($user['photo'])) ?>" class="mt-2 w-24 h-24 rounded-full">
            <?php endif; ?>
        </div>

        <button type="submit" class="bg-clsuGreen text-white px-4 py-2 rounded">Update Profile</button>
    </form>
</div>

</body>
</html>
