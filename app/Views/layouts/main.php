<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) : 'HRMO - Human Resource Management Office' ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Custom styles */
        .sidebar {
            transition: all 0.3s ease;
        }
        
        .main-content {
            transition: margin-left 0.3s ease;
        }
        
        .active-link {
            background-color: #0B6B3A;
            color: white !important;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-green-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-users-cog text-xl mr-2"></i>
                        <span class="font-bold text-xl">HRMO</span>
                    </div>
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="<?= base_url('/') ?>" class="border-b-2 border-transparent hover:border-white px-1 pt-1 text-sm font-medium">
                            <i class="fas fa-home mr-1"></i> Home
                        </a>
                        <a href="<?= base_url('/jobs') ?>" class="border-b-2 border-transparent hover:border-white px-1 pt-1 text-sm font-medium">
                            <i class="fas fa-briefcase mr-1"></i> Jobs
                        </a>
                        <a href="<?= base_url('/job-vacancies') ?>" class="border-b-2 border-transparent hover:border-white px-1 pt-1 text-sm font-medium">
                            <i class="fas fa-list mr-1"></i> Vacancies
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <?php if (session()->get('logged_in')): ?>
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-3">
                                <span class="text-sm"><?= esc(session()->get('username')) ?></span>
                                <a href="<?= base_url('/dashboard') ?>" class="bg-green-600 hover:bg-green-500 px-3 py-2 rounded-md text-sm font-medium">
                                    <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                                </a>
                                <a href="<?= base_url('/logout') ?>" class="bg-red-600 hover:bg-red-500 px-3 py-2 rounded-md text-sm font-medium">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= base_url('/login') ?>" class="bg-blue-600 hover:bg-blue-500 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-8">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                &copy; <?= date('Y') ?> CLSU Human Resource Management Office. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Success and error message handling
        <?php if(session()->has('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= session('success') ?>',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>

        <?php if(session()->has('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= session('error') ?>',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>
    </script>
</body>
</html>