<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLSU Online Job Application</title>

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

<body class="flex flex-col min-h-screen bg-gray-100 text-gray-800">

  <header class="bg-clsuGreen text-white py-4 px-4">
    <div class="max-w-5xl mx-auto flex items-center justify-center">
        <div class="flex items-center gap-3 text-center md:text-left">
            <img
                src="/HRMO/public/assets/images/clsu-logo2.png"
                alt="CLSU Logo"
                class="w-16 h-auto"
            />
            <div>
                <h1 class="text-3xl font-bold mb-1">
                    CLSU Online Job Application
                </h1>
                <p class="text-base max-w-2xl">
                    Explore career opportunities and apply online with ease and security.
                </p>
            </div>
        </div>
    </div>
</header>


<main class="flex-1 max-w-7xl mx-auto py-10 px-6">
    <h2 class="text-2xl md:text-3xl font-bold text-center mb-6">
        Available Job Vacancies
    </h2>

    <?php if (!empty($jobs)): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($jobs as $job): ?>
        <div class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col h-full shadow-sm hover:shadow-lg transition duration-200">
            
            <!-- Job Title -->
            <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-2 hover:text-clsuGreen transition">
                <?= esc($job['position_title']) ?>
            </h3>

            <!-- Job Description -->
            <p class="text-xs text-gray-700 mb-3 leading-relaxed line-clamp-3">
                <?= esc($job['description']) ?>
            </p>

        <div class="text-xs text-gray-600 mb-5 space-y-1">
            <div class="flex gap-2">
                <span class="font-medium text-gray-800">Department:</span>
                <span class="text-gray-700"><?= esc($job['department']) ?></span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-gray-800">Employment Type:</span>
                <span class="text-gray-700"><?= esc($job['employment_type']) ?></span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-gray-800">Monthly Salary:</span>
                <span class="text-gray-700"><?= esc($job['monthly_salary']) ?></span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-gray-800">Deadline:</span>
                <span class="text-red-600 font-semibold"><?= date('F j, Y', strtotime($job['application_deadline'])) ?></span>
            </div>
        </div>

            <!-- View Details Button -->
            <div class="mt-auto flex justify-end">
                <a href="<?= base_url('jobs/view/' . $job['id']) ?>"
                class="inline-block bg-clsuGreen text-white font-semibold rounded-lg px-3 py-1.5 hover:bg-green-800 transition text-xs">
                    View Details
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <p class="text-center text-gray-500 text-sm mt-10">
            No job vacancies available at the moment.
        </p>
    <?php endif; ?>

</main>


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
