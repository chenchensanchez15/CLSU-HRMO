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
    </div>
</header>
<main class="max-w-2xl w-full mx-auto my-10 bg-white p-6 rounded-lg shadow-lg">

    <h2 class="text-xl font-bold text-clsuGreen mb-4 text-center">Change Password</h2>

    <form id="changePasswordForm" action="<?= site_url('account/updatePassword') ?>" method="post" class="space-y-4">
        <?= csrf_field() ?>

        <div>
            <label class="block text-sm mb-1 font-semibold">Current Password</label>
            <input type="password" name="current_password" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm mb-1 font-semibold">New Password</label>
            <input type="password" name="new_password" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm mb-1 font-semibold">Confirm New Password</label>
            <input type="password" name="confirm_password" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="flex justify-end gap-3 pt-3">
            <?php if(session()->get('first_login') == 0): ?>
            <a href="<?= site_url('dashboard') ?>" class="px-4 py-2 rounded bg-gray-400 text-white hover:bg-gray-500">Cancel</a>
            <?php endif; ?>

            <button type="submit" class="px-4 py-2 rounded bg-clsuGreen text-white hover:bg-green-800">Save</button>
        </div>
    </form>

</main>

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
            confirmButtonColor: '#0B6B3A'
        }).then((result) => {
            // Redirect to dashboard after password update
            window.location.href = "<?= site_url('dashboard') ?>";
        });
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?= session()->getFlashdata('error') ?>',
            confirmButtonColor: '#0B6B3A'
        });
    <?php endif; ?>
});
</script>


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


</body>
</html>