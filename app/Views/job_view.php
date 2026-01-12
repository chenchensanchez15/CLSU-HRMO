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

<header class="bg-clsuGreen text-white py-3 px-6 shadow">
    <div class="flex items-center gap-3">
        
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
</header>

<main class="max-w-6xl mx-auto bg-white p-4 my-4 rounded-lg shadow-lg">

    <h2 class="text-2xl font-extrabold text-clsuGreen mb-3">
        <?= esc($job['position_title']) ?>
    </h2>

    <p class="text-base mb-1"><strong>Office:</strong> <?= esc($job['office']) ?></p>
    <p class="text-base mb-1"><strong>Item No.:</strong> <?= esc($job['item_no']) ?></p>
    <p class="text-base mb-1"><strong>Salary Grade:</strong> <?= esc($job['salary_grade']) ?></p>
    <p class="text-base mb-2"><strong>Monthly Salary:</strong> <?= esc($job['monthly_salary']) ?></p>

    <hr class="my-3 border-gray-300">

    <details class="mb-3 border rounded">
        <summary class="cursor-pointer px-3 py-1.5 font-semibold bg-gray-100 text-sm">
            Qualification Standards
        </summary>
        <div class="px-4 py-2 text-sm">
            <ul class="list-disc ml-4 space-y-0.5">
                <li><strong>Education:</strong> <?= esc($job['education']) ?></li>
                <li><strong>Training:</strong> <?= esc($job['training']) ?></li>
                <li><strong>Experience:</strong> <?= esc($job['experience']) ?></li>
                <li><strong>Eligibility:</strong> <?= esc($job['eligibility']) ?></li>
                <li><strong>Competency:</strong> <?= esc($job['competency']) ?></li>
            </ul>
        </div>
    </details>

    <details class="mb-3 border rounded">
        <summary class="cursor-pointer px-3 py-1.5 font-semibold bg-gray-100 text-sm">
            Duties and Responsibilities
        </summary>
        <div class="px-4 py-2 text-sm">
            <?= nl2br(esc((string) $job['duties_responsibilities'])) ?>
        </div>
    </details>
    
    <details class="mb-3 border rounded">
        <summary class="cursor-pointer px-3 py-1.5 font-semibold bg-gray-100 text-sm">
            Application Requirements
        </summary>
        <div class="px-4 py-2 text-sm">
           <?= nl2br(esc((string) $job['application_requirements'])) ?>
        </div>
    </details>

    <p class="font-semibold text-base mb-3">
        Deadline for Submission:
        <span class="text-red-600">
            <?= date('F j, Y', strtotime($job['application_deadline'])) ?>
        </span>
    </p>

    <div class="text-center mt-3">
        <a href="<?= base_url('register') ?>"
           class="bg-clsuGreen text-white px-6 py-2 rounded font-semibold hover:bg-green-800 transition text-sm">
            Apply Now
        </a>
    </div>

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
