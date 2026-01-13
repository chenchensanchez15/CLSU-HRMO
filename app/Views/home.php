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
                        clsuGreen: '#116c2f',
                        clsuGold: '#F2C94C'
                    }
                }
            }
        }
    </script>
</head>

<body class="flex flex-col min-h-screen bg-gray-100 text-gray-800">

    <header class="bg-clsuGreen text-white py-6 px-6">
        <div class="max-w-5xl mx-auto flex items-center justify-center">
            <div class="flex items-center gap-4 text-center md:text-left">
                <img
                    src="/HRMO/public/assets/images/clsu-logo2.png"
                    alt="CLSU Logo"
                    class="w-20 h-auto"
                />
                <div>
                    <h1 class="text-4xl font-bold mb-1">
                        CLSU Online Job Application
                    </h1>
                    <p class="text-lg max-w-2xl">
                        Explore career opportunities and apply online with ease and security.
                    </p>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl mx-auto py-10 px-6">
        <h2 class="text-3xl font-bold text-center mb-6">
            Available Job Vacancies
        </h2>

        <?php if (!empty($jobs)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <?php foreach ($jobs as $job): ?>
                    <div class="bg-white p-5 rounded-lg shadow hover:shadow-md transition flex flex-col">
                        <h3 class="text-xl font-semibold mb-2">
                            <?= esc($job['position_title']) ?>
                        </h3>

                        <p class="text-sm text-gray-600 mb-3 line-clamp-3">
                            <?= esc($job['description']) ?>
                        </p>

                        <div class="text-xs text-gray-500 mb-3 space-y-1">
                            <p><strong>Department:</strong> <?= esc($job['department']) ?></p>
                            <p><strong>Employment Type:</strong> <?= esc($job['employment_type']) ?></p>
                        </div>

                        <a href="<?= base_url('jobs/view/' . $job['id']) ?>"
                           class="mt-auto inline-block text-clsuGreen font-semibold hover:underline">
                            View Details
                        </a>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php else: ?>
            <p class="text-center text-gray-500">
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
