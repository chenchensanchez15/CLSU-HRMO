<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($job['position_title']) ?> | HRMO Online Job Application</title>
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
    <h1 class="text-xl font-bold">CLSU HRMO Online Job Application</h1>
</header>

<main class="max-w-5xl mx-auto bg-white p-8 my-10 rounded-lg shadow">

    <!-- Job Title & Basic Info -->
    <h2 class="text-2xl font-bold text-clsuGreen mb-4"><?= esc($job['position_title']) ?></h2>
    <p class="text-sm mb-2"><strong>Office:</strong> <?= esc($job['office']) ?></p>
    <p class="text-sm mb-2"><strong>Item No.:</strong> <?= esc($job['item_no']) ?></p>
    <p class="text-sm mb-2"><strong>Salary Grade:</strong> <?= esc($job['salary_grade']) ?></p>
    <p class="text-sm mb-6"><strong>Monthly Salary:</strong> <?= esc($job['monthly_salary']) ?></p>

    <hr class="my-6">

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

    <!-- Job Description -->
    <details class="mb-6 border rounded">
        <summary class="cursor-pointer px-4 py-3 font-semibold bg-gray-100">Job Description</summary>
        <div class="px-6 py-4 text-sm">
            <?= nl2br(esc((string) $job['description'])) ?>

        </div>
    </details>

<p class="font-semibold text-lg mb-6">
    Deadline for Submission: 
    <span class="text-red-600">
        <?= date('F j, Y', strtotime($job['application_deadline'])) ?>
    </span>
</p>


    <!-- Apply Button -->
    <div class="text-center">
        <a href="#" class="bg-clsuGreen text-white px-8 py-3 rounded font-semibold hover:bg-green-800 transition">
            Apply for this Position
        </a>
    </div>

</main>

<footer class="bg-clsuGreen text-white text-center py-4">
    <p>&copy; <?= date('Y') ?> Central Luzon State University – HRMO</p>
</footer>

</body>
</html>
