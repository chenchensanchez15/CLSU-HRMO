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

<header class="bg-clsuGreen text-white px-8 py-4 flex flex-col md:flex-row md:justify-between md:items-center">

    <div class="flex items-center space-x-4">
        <div>
            <h1 class="text-xl font-bold leading-tight">
                Central Luzon State University
            </h1>
            <p class="text-sm opacity-90">
                HRMO Online Job Application System
            </p>
        </div>
    </div>

    <nav class="mt-4 md:mt-0 space-x-5 font-semibold">
        <a href="<?= base_url('login') ?>" class="hover:underline">Login</a>
        <a href="<?= base_url('register') ?>" class="hover:underline">Register</a>
    </nav>

</header>

<section class="bg-clsuGreen text-white text-center py-24 px-6">
    <h2 class="text-4xl font-bold mb-4">
        Welcome to CLSU HRMO Online Job Application
    </h2>
    <p class="text-lg mb-8 max-w-2xl mx-auto">
        Explore career opportunities and apply online with ease and security.
    </p>
    <a href="#jobs"
       class="bg-clsuGold text-black px-8 py-3 rounded-md font-bold hover:bg-yellow-400 transition">
        View Job Openings
    </a>
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

<header class="bg-clsuGreen text-white px-8 py-4 flex flex-col md:flex-row md:justify-between md:items-center">

    <div class="flex items-center space-x-4">
        <div>
            <h1 class="text-xl font-bold leading-tight">
                Central Luzon State University
            </h1>
            <p class="text-sm opacity-90">
                HRMO Online Job Application System
            </p>
        </div>
    </div>

    <nav class="mt-4 md:mt-0 space-x-5 font-semibold">
        <a href="<?= base_url('login') ?>" class="hover:underline">Login</a>
        <a href="<?= base_url('register') ?>" class="hover:underline">Register</a>
    </nav>

</header>

<section class="bg-clsuGreen text-white text-center py-24 px-6">
    <h2 class="text-4xl font-bold mb-4">
        Welcome to CLSU HRMO Online Job Application
    </h2>
    <p class="text-lg mb-8 max-w-2xl mx-auto">
        Explore career opportunities and apply online with ease and security.
    </p>
    <a href="#jobs"
       class="bg-clsuGold text-black px-8 py-3 rounded-md font-bold hover:bg-yellow-400 transition">
        View Job Openings
    </a>
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
