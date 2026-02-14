<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | CLSU HRMO</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<body class="min-h-screen bg-gray-100 flex flex-col">

<header class="bg-clsuGreen text-white py-3 px-6 shadow">
    <div class="flex items-center gap-3">
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
</header>

<div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md mx-auto mt-10">
    <h1 class="text-2xl font-bold text-clsuGreen mb-6 text-center">Login</h1>

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

    <!-- Register Link -->
<div class="mt-4 text-center text-sm">
    <span class="text-gray-600">Don't have an account?</span>
    <a href="<?= base_url('register') ?>" 
       class="text-clsuGreen font-semibold hover:underline">
        Register here
    </a>
</div>

</div>

<footer class="w-full bg-gray-100 py-4 mt-auto border-t">
    <div class="flex justify-end px-6 text-xs text-gray-600">
        <div class="text-right">
            &copy; <?= date('Y') ?> CLSU-HRMO. All rights reserved.<br>
            Powered by <span class="text-green-700">Management Information System Office (CLSU-MISO)</span>
        </div>
    </div>
</footer>

<!-- SweetAlert2 Error -->
<?php if(session()->getFlashdata('error')): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Login Failed',
        text: '<?= session()->getFlashdata('error') ?>',
        confirmButtonColor: '#0B6B3A'
    });
</script>
<?php endif; ?>

<!-- Registration Success -->
<?php if(session()->getFlashdata('registration_success')): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Account Created Successfully!',
        text: '<?= session()->getFlashdata('registration_success') ?>',
        confirmButtonColor: '#0B6B3A',
    }).then((result) => {
        if (result.isConfirmed) {
            // Stay on login page
            window.location.href = '<?= base_url('login') ?>';
        }
    });
</script>
<?php endif; ?>

</body>
</html>
