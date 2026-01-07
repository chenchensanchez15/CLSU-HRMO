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


<header class="bg-clsuGreen text-white px-8 py-5">
    <h1 class="text-xl font-bold">CLSU Online Job Application</h1>
</header>
<main class="max-w-6xl mx-auto bg-white p-12 my-12 rounded-xl shadow-xl">

    <!-- Job Title & Basic Info -->
    <h2 class="text-3xl font-extrabold text-clsuGreen mb-6"><?= esc($job['position_title']) ?></h2>

    <p class="text-lg mb-3"><strong>Office:</strong> <?= esc($job['office']) ?></p>
    <p class="text-lg mb-3"><strong>Item No.:</strong> <?= esc($job['item_no']) ?></p>
    <p class="text-lg mb-3"><strong>Salary Grade:</strong> <?= esc($job['salary_grade']) ?></p>
    <p class="text-lg mb-8"><strong>Monthly Salary:</strong> <?= esc($job['monthly_salary']) ?></p>

    <hr class="my-8 border-gray-300">


    <!-- Qualifications & Requirements -->
    <details class="mb-6 border rounded">
        <summary class="cursor-pointer px-4 py-3 font-semibold bg-gray-100">Qualification Standards</summary>
        <div class="px-6 py-4 text-sm">
            <ul class="list-disc ml-6">
                <li><strong>Education:</strong> <?= esc($job['education']) ?></li>
                <li><strong>Training:</strong> <?= esc($job['training']) ?></li>
                <li><strong>Experience:</strong> <?= esc($job['experience']) ?></li>
                <li><strong>Eligibility:</strong> <?= esc($job['eligibility']) ?></li>
                <li><strong>Competency:</strong> <?= esc($job['competency']) ?></li>
            </ul>
        </div>
    </details>

    <!-- Duties & Responsibilities -->
    <details class="mb-6 border rounded">
        <summary class="cursor-pointer px-4 py-3 font-semibold bg-gray-100">Duties and Responsibilities</summary>
        <div class="px-6 py-4 text-sm">
            <?= nl2br(esc((string) $job['duties_responsibilities'])) ?>
        </div>
    </details>

    <!-- Application Requirements -->
    <details class="mb-6 border rounded">
        <summary class="cursor-pointer px-4 py-3 font-semibold bg-gray-100">Application Requirements</summary>
        <div class="px-6 py-4 text-sm">
           <?= nl2br(esc((string) $job['application_requirements'])) ?>
        </div>
    </details>


<p class="font-semibold text-lg mb-6">
    Deadline for Submission: 
    <span class="text-red-600">
        <?= date('F j, Y', strtotime($job['application_deadline'])) ?>
    </span>
</p>

<div class="text-center mt-6">
    <a href="<?= base_url('register') ?>"
       class="bg-clsuGreen text-white px-8 py-3 rounded font-semibold hover:bg-green-800 transition">
        Apply Now
    </a>
</div>


</main>

<footer class="bg-clsuGreen text-white text-center py-6">
    <p>
        &copy; <?= date('Y') ?> Central Luzon State University – Human Resource Management Office
    </p>
</footer>

</body>
</html>
