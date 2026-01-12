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
<div class="flex items-center gap-6">
    <nav class="hidden md:flex gap-6 font-semibold mt-7"> <!-- added mt-2 -->
        <a href="<?= site_url('dashboard') ?>" class="hover:underline">Home</a>
        <a href="<?= site_url('account/personal') ?>" class="hover:underline">Personal</a>
        <a href="#" class="hover:underline">Trainings</a>
        <a href="<?= site_url('account/profile') ?>" class="hover:underline">Profile</a>
    </nav>

    <!-- Profile Dropdown -->
    <div class="account-menu relative mt-1"> <!-- optional: adjust independently -->
        <?php 
            $photo = !empty($user['photo']) ? $user['photo'] : 'default-avatar.png';
        ?>
        <button onclick="toggleDropdown()" class="flex items-center gap-2 bg-none border-none cursor-pointer">
            <img src="<?= base_url('uploads/'.$photo) ?>" 
                 alt="Profile" 
                 class="w-10 h-10 rounded-full border-2 border-white object-cover">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div id="accountDropdown" class="account-dropdown absolute right-0 mt-2 hidden bg-white text-black min-w-[160px] rounded shadow-lg z-50">
            <a href="<?= site_url('logout') ?>" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
        </div>
    </div>
</div>


    </div>
</header>

<div class="container">

    <!-- LEFT PANEL -->
    <div class="left">
        <div class="profile-pic">
            <?php if(!empty($user['photo'])): ?>
                <img src="<?= base_url('uploads/' . esc($user['photo'])) ?>" alt="Profile" class="w-full h-full rounded-full">
            <?php endif; ?>
        </div>
        <h3><?= esc($user['first_name'] ?? 'No') ?> <?= esc($user['last_name'] ?? 'Name') ?></h3>
        <p><?= esc($user['email'] ?? 'noemail@example.com') ?></p>
        <p><?= esc($user['contact'] ?? 'N/A') ?></p>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right">

        <!-- APPLICATIONS -->
        <div class="card">
            <h3>Positions Applied For</h3>
            <table>
                <tr>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Status</th>
                </tr>
                <?php if(empty($applications)): ?>
                    <tr><td colspan="3">No applications yet.</td></tr>
                <?php else: ?>
                    <?php foreach($applications as $app): ?>
                        <tr>
                            <td><?= esc($app['position']) ?></td>
                            <td><?= esc($app['department']) ?></td>
                            <td>
                                <span class="status <?= esc($app['status']) ?>">
                                    <?= esc($app['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>

        <!-- AVAILABLE JOB VACANCIES -->
        <div class="card">
            <h3>Available Job Vacancies</h3>
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Position</th>
                        <th>Office</th>
                        <th>Department</th>
                        <th>Employment Type</th>
                        <th>Deadline</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($vacancies)):
                        $count = 0;
                        foreach($vacancies as $vac):
                            $count++;
                    ?>
                    <tr>
                        <td><?= $count ?></td>
                        <td><?= esc($vac['position_title']) ?></td>
                        <td><?= esc($vac['office']) ?></td>
                        <td><?= esc($vac['department']) ?></td>
                        <td><?= esc($vac['employment_type']) ?></td>
                        <td><?= esc($vac['application_deadline']) ?></td>
                        <td>
                            <form method="post" action="<?= site_url('applications/apply') ?>">
                                <input type="hidden" name="vacancy_id" value="<?= esc($vac['id']) ?>">
                                <button type="submit" class="bg-clsuGreen text-white px-2 py-1 rounded text-xs">Apply</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="7">No open vacancies.</td></tr>
                    <?php endif; ?>

                    
                </tbody>
            </table>
        </div>

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

</body>
</html>
