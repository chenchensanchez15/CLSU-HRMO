<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicant Registration | CLSU HRMO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
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


<!-- FORM CONTAINER -->
<main class="flex-1 flex items-center justify-center px-4 py-6">
    <div class="w-full max-w-xl bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- FORM HEADER -->
        <div class="bg-clsuGreen text-white px-6 py-4 text-center">
            <h1 class="text-xl font-bold">Applicant Registration Form</h1>
            <p class="text-xs opacity-90">CLSU Online Job Application System</p>
        </div>

        <!-- FORM -->
        <form class="p-6 space-y-4" action="<?= base_url('register/save') ?>" method="post">

            <!-- PERSONAL INFO -->
            <div>
                <h2 class="text-base font-semibold text-clsuGreen mb-2">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">First Name</label>
                        <input type="text" name="first_name" class="w-full border rounded-md px-2 py-1.5 focus:ring-2 focus:ring-clsuGreen focus:outline-none" placeholder="Juan" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Middle Name</label>
                        <input type="text" name="middle_name" class="w-full border rounded-md px-2 py-1.5 focus:ring-2 focus:ring-clsuGreen focus:outline-none" placeholder="Santos">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Last Name</label>
                        <input type="text" name="last_name" class="w-full border rounded-md px-2 py-1.5 focus:ring-2 focus:ring-clsuGreen focus:outline-none" placeholder="Dela Cruz" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Extension</label>
                        <input type="text" name="extension" class="w-full border rounded-md px-2 py-1.5 focus:ring-2 focus:ring-clsuGreen focus:outline-none" placeholder="Jr., Sr., III">
                    </div>
                </div>
            </div>

            <!-- EMAIL -->
            <div>
                <label class="block text-xs font-medium mb-1">Email Address</label>
                <input type="email" name="email" class="w-full border rounded-md px-2 py-1.5 focus:ring-2 focus:ring-clsuGreen focus:outline-none" placeholder="example@email.com" required>
            </div>

            <!-- CONFIRMATION -->
            <div class="flex items-start gap-2 text-xs">
                <input type="checkbox" class="mt-1" required>
                <p>I confirm that the information provided is accurate and complete.</p>
            </div>

            <!-- SUBMIT -->
            <button type="submit" class="w-full bg-clsuGreen text-white py-2.5 rounded-md font-bold hover:bg-green-800 transition text-sm">
                Register
            </button>

            <p class="text-xs text-center">
                Already have an account? 
                <a href="<?= base_url('login') ?>" class="text-clsuGreen font-semibold hover:underline">Login here</a>
            </p>

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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (session()->getFlashdata('swal_error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Registration Failed',
            text: '<?= session()->getFlashdata('swal_error') ?>',
            confirmButtonColor: '#0B6B3A'
        });
    </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('swal_success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?= session()->getFlashdata('swal_success') ?>',
            confirmButtonColor: '#0B6B3A'
        });
    </script>
    <?php endif; ?>

</body>
</html>
