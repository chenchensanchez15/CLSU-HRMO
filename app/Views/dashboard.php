<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicant Dashboard</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clsu: '#0b5e1e'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- NAVBAR -->
<nav class="bg-clsu text-white px-8 py-4 flex justify-between items-center">
    <div class="space-x-6 font-semibold">
        <a href="#" class="hover:underline">Personal</a>
        <a href="#" class="hover:underline">Trainings</a>
        <a href="#" class="hover:underline">Other Details</a>
    </div>
    <div class="font-bold text-lg">
        CLSU Online Job Application
    </div>
</nav>

<!-- MAIN CONTAINER -->
<div class="max-w-7xl mx-auto px-6 py-8 grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- LEFT PANEL -->
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <div class="w-32 h-32 rounded-full bg-gray-300 mx-auto mb-4"></div>

        <h3 class="text-xl font-semibold text-clsu">
            <?= esc($user['first_name']) ?> <?= esc($user['last_name']) ?>
        </h3>
        <p class="text-gray-600 mt-1"><?= esc($user['email']) ?></p>
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
