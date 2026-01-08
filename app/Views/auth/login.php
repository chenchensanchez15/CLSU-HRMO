<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | CLSU HRMO</title>
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
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
    <h1 class="text-2xl font-bold text-clsuGreen mb-6 text-center">Login</h1>

    <!-- Display error if login fails -->
    <?php if(session()->getFlashdata('error')): ?>
        <p class="text-red-600 mb-4 text-center"><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>

    <form action="<?= base_url('auth/loginPost') ?>" method="post" class="space-y-4">
        <div>
            <label class="block mb-1 font-medium">Email</label>
            <input type="email" name="email" required 
                   class="w-full border px-3 py-2 rounded-md focus:ring-2 focus:ring-clsuGreen focus:outline-none"
                   placeholder="Enter your email">
        </div>
        <div>
            <label class="block mb-1 font-medium">Password</label>
            <input type="password" name="password" required 
                   class="w-full border px-3 py-2 rounded-md focus:ring-2 focus:ring-clsuGreen focus:outline-none"
                   placeholder="Enter your password">
        </div>
        <button type="submit" 
                class="w-full bg-clsuGreen text-white py-2 rounded-md font-semibold hover:bg-green-800 transition">
            Login
        </button>
    </form>
</div>

</body>
</html>
