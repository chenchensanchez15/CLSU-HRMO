<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMO Online Job Application System | CLSU</title>

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
<body class="bg-gray-100 text-gray-800">


<section class="bg-clsuGreen text-white text-center py-10 px-6">
    <h2 class="text-4xl font-bold mb-2">
        CLSU Online Job Application
    </h2>
    <p class="text-lg mb-2 max-w-2xl mx-auto">
        Explore career opportunities and apply online with ease and security.
    </p>
</section>

<section id="jobs" class="max-w-7xl mx-auto py-16 px-6">
    <h3 class="text-3xl font-bold text-center mb-12">
        Available Job Vacancies
    </h3>

    <?php if (!empty($jobs)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

            <?php foreach ($jobs as $job): ?>
                <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition flex flex-col">
                    <h4 class="text-xl font-semibold mb-2">
                        <?= esc($job['position_title']) ?>
                    </h4>

                    <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                        <?= esc($job['description']) ?>
                    </p>

                    <div class="text-xs text-gray-500 mb-4">
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
</section>

<footer class="bg-clsuGreen text-white text-center py-6">
    <p>
        &copy; <?= date('Y') ?> Central Luzon State University – Human Resource Management Office
    </p>
</footer>

</body>
</html>