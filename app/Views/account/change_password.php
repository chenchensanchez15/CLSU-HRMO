<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<head>
    <title>Change Password | CLSU Online Job Application System</title>
    <link rel="icon" type="image/x-icon" href="/CLSU-HRMO/public/assets/images/favicon.ico">
</head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Tailwind CSS -->
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

<!-- HEADER -->
<header class="bg-clsuGreen text-white py-3 px-6 shadow">
    <div class="flex items-center gap-3">
        <img
            src="/CLSU-HRMO/public/assets/images/clsu-logo2.png"
            alt="CLSU Logo"
            class="w-12 h-auto"
        >
        <div class="flex flex-col leading-tight">
            <a href="<?= site_url('dashboard') ?>" class="text-xl font-bold">
                CLSU Online Job Application
            </a>
        </div>
    </div>
</header>

<!-- MAIN CONTENT -->
<div class="flex-1 flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden">
        
        <!-- FORM HEADER -->
        <div class="bg-clsuGreen text-white px-6 py-4 text-center">
            <h1 class="text-xl font-bold">Change Password</h1>
            <p class="text-xs opacity-90 mt-1">Secure your account</p>
        </div>

        <!-- FORM -->
        <form id="changePasswordForm" action="<?= site_url('account/updatePassword') ?>" method="post" class="p-6 space-y-4">
            <?= csrf_field() ?>
        
            <!-- LAST UPDATED INFO -->
            <?php 
                $userModel = new \App\Models\UserModel();
                $user = $userModel->find(session()->get('user_id'));
                $lastUpdated = $user['updated_at'] ?? $user['created_at'] ?? null;
                if ($lastUpdated):
                    // Convert database timestamp (UTC) to Philippine time
                    $utcDateTime = new DateTime($lastUpdated, new DateTimeZone('UTC'));
                    $philippineTimeZone = new DateTimeZone('Asia/Manila');
                    $utcDateTime->setTimezone($philippineTimeZone);
                    $formattedDate = $utcDateTime->format('F j, Y');
                    $formattedTime = $utcDateTime->format('g:i A');
            ?>
            <div class="text-sm text-gray-600">
                Last Updated: <?= $formattedDate ?> at <?= $formattedTime ?>
            </div>
            <?php endif; ?>
        
            <!-- CURRENT PASSWORD -->
            <div>
                <label class="block text-xs font-medium mb-1 text-gray-700">Current Password</label>
                <input 
                    type="password" 
                    name="current_password" 
                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-clsuGreen focus:outline-none" 
                    placeholder="Enter your current password"
                    required
                >
            </div>

            <!-- NEW PASSWORD -->
            <div>
                <label class="block text-xs font-medium mb-1 text-gray-700">New Password</label>
                <input 
                    type="password" 
                    name="new_password" 
                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-clsuGreen focus:outline-none" 
                    placeholder="Enter your new password"
                    required
                >
            </div>

            <!-- CONFIRM PASSWORD -->
            <div>
                <label class="block text-xs font-medium mb-1 text-gray-700">Confirm New Password</label>
                <input 
                    type="password" 
                    name="confirm_password" 
                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-clsuGreen focus:outline-none" 
                    placeholder="Confirm your new password"
                    required
                >
            </div>

            <!-- BUTTONS -->
            <div class="flex gap-3 pt-4">
                <?php if(session()->get('first_login') == 0): ?>
                <a 
                    href="<?= site_url('dashboard') ?>" 
                    class="flex-1 text-center py-2.5 rounded-md font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition text-sm"
                >
                    Cancel
                </a>
                <?php endif; ?>

                <button 
                    type="submit" 
                    class="flex-1 py-2.5 rounded-md font-bold bg-clsuGreen text-white hover:bg-green-800 transition text-sm"
                >
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- FOOTER -->
<footer class="w-full bg-gray-100 py-4 border-t">
    <div class="flex justify-end px-6 text-xs text-gray-600">
        <div class="text-right">
            &copy; <?= date('Y') ?> CLSU-HRMO. All rights reserved.<br>
            Powered by <span class="text-green-700">Management Information System Office (CLSU-MISO)</span>
        </div>
    </div>
</footer>

<!-- SWEETALERT SCRIPTS -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    <?php if(session()->get('first_login') == 1): ?>
    // Prevent leaving page if first_login = 1
    window.onbeforeunload = function() {
        return "You must change your password before leaving!";
    };
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= session()->getFlashdata('success') ?>',
            confirmButtonColor: '#0B6B3A',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            // Redirect to dashboard after showing success message
            window.location.href = "<?= site_url('dashboard') ?>";
        });
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?= session()->getFlashdata('error') ?>',
            confirmButtonColor: '#0B6B3A',
            timer: 2000,
            showConfirmButton: false
        });
    <?php endif; ?>
});
</script>

</body>
</html>