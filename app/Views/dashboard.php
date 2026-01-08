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
/* Custom dashboard styles */
body { margin:0; font-family: Arial, sans-serif; background:#f4f6f9; }

/* NAVBAR */
.navbar {
    background: #0B6B3A;
    padding: 15px 30px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.navbar a {
    color: #fff;
    margin-right: 20px;
    font-weight: bold;
    text-decoration: none;
}

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
.profile-pic {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #ccc;
    margin: 0 auto 15px;
}
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
</head>
<body class="bg-gray-100 min-h-screen">

<div class="navbar">
    <div>
        <a href="#">Personal</a>
        <a href="#">Trainings</a>
        <a href="#">Other Details</a>
    </div>
    <div class="font-bold text-lg">
        CLSU Online Job Application
    </div>
</nav>

<!-- MAIN CONTAINER -->
<div class="max-w-7xl mx-auto px-6 py-8 grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- LEFT PANEL -->
    <div class="left">
        <div class="profile-pic">
            <!-- If photo exists, use <img> -->
            <?php if(!empty($user['photo'])): ?>
                <img src="<?= base_url('uploads/'.$user['photo']) ?>" alt="Profile" style="width:120px;height:120px;border-radius:50%;">
            <?php endif; ?>
        </div>
        <h3><?= esc($user['first_name'] ?? 'No') ?> <?= esc($user['last_name'] ?? 'Name') ?></h3>
        <p><?= esc($user['email'] ?? 'noemail@example.com') ?></p>
        <p><?= esc($user['contact'] ?? 'N/A') ?></p>
    </div>

    <!-- RIGHT PANEL -->
    <div class="md:col-span-2 space-y-6">

        <!-- APPLIED POSITIONS -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-clsu mb-4">
                Positions Applied For
            </h3>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">Position</th>
                            <th class="px-4 py-2">Department</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applications)): ?>
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-center text-gray-500">
                                    No applications yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($applications as $app): ?>
                                <tr class="border-b">
                                    <td class="px-4 py-2"><?= esc($app['position_title']) ?></td>
                                    <td class="px-4 py-2"><?= esc($app['department']) ?></td>
                                    <td class="px-4 py-2">
                                        <span class="
                                            px-3 py-1 rounded-full text-white text-xs
                                            <?= $app['status'] === 'Pending' ? 'bg-yellow-500' : '' ?>
                                            <?= $app['status'] === 'Approved' ? 'bg-green-600' : '' ?>
                                            <?= $app['status'] === 'Rejected' ? 'bg-red-600' : '' ?>
                                        ">
                                            <?= esc($app['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- AVAILABLE VACANCIES -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-clsu mb-4">
                Available Job Vacancies
            </h3>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">Position</th>
                            <th class="px-4 py-2">Department</th>
                            <th class="px-4 py-2">Employment</th>
                            <th class="px-4 py-2">Salary</th>
                            <th class="px-4 py-2">Deadline</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vacancies)): ?>
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                                    No open job positions.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($vacancies as $job): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= esc($job['position_title']) ?></td>
                                    <td class="px-4 py-2"><?= esc($job['department']) ?></td>
                                    <td class="px-4 py-2"><?= esc($job['employment_type']) ?></td>
                                    <td class="px-4 py-2"><?= esc($job['monthly_salary']) ?></td>
                                    <td class="px-4 py-2"><?= esc($job['application_deadline']) ?></td>
                                    <td class="px-4 py-2">
                                        <form method="post" action="<?= base_url('apply') ?>">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="vacancy_id" value="<?= $job['id'] ?>">
                                            <a
                                                href="<?= base_url('apply?id='.$job['id']) ?>"
                                                class="bg-cyan-500 hover:bg-cyan-400 text-white px-3 py-1 text-sm rounded-md transition"
                                            >
                                                Apply
                                            </a>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

</body>
</html>
