<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMO Online Job Application System | CLSU</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col">

<header class="bg-clsuGreen text-white py-3 px-6 shadow">
    <div class="flex items-center gap-3">
        <img src="/HRMO/public/assets/images/clsu-logo2.png" alt="CLSU Logo" class="w-12 h-auto">
        <div class="flex flex-col leading-tight">
            <span class="text-xl font-bold">CLSU Online Job Application</span>
            <span class="text-sm font-medium opacity-90">Human Resource Management Office</span>
        </div>
    </div>
</header>
<main class="flex-1 w-full max-w-4xl mx-auto bg-white p-6 my-6 rounded-lg shadow-lg">

    <h2 class="text-2xl font-extrabold text-clsuGreen mb-4">
        <?= esc($job['position_title']) ?>
    </h2>

    <!-- Job Overview (2 columns) -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm text-gray-800 mb-4">

    <p><strong>Office:</strong> <?= esc($job['office'] ?? 'N/A') ?></p>
    <p><strong>Salary Grade:</strong> <?= esc($job['salary_grade'] ?? 'N/A') ?></p>

    <p><strong>Item No:</strong> <?= esc($job['plantilla_item_no'] ?? 'N/A') ?></p>
    <p><strong>Monthly Salary:</strong> ₱<?= number_format($job['monthly_salary'] ?? 0, 2) ?></p>

    <p><strong>Posted:</strong> <?= !empty($job['created_at']) ? date('F j, Y', strtotime($job['created_at'])) : 'N/A' ?></p>
    <p class="text-red-600 font-semibold"><strong>Deadline:</strong> <?= date('F j, Y', strtotime($job['application_deadline'])) ?></p>

</div>


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

   <!-- Buttons -->
    <div class="flex justify-end mt-6 gap-2">
        <a href="<?= base_url() ?>"
           class="bg-gray-300 text-gray-800 px-6 py-2 rounded font-semibold hover:bg-gray-400 transition text-sm">
            Cancel
        </a>

        <button onclick="requireLogin()"
            class="bg-clsuGreen text-white px-6 py-2 rounded font-semibold hover:bg-green-800 transition text-sm">
            Apply Now
        </button>
    </div>


</main>

<footer class="w-full bg-gray-100 py-4 border-t mt-auto">
    <div class="flex justify-end px-6 text-xs text-gray-600">
        <div class="text-right">
            &copy; <?= date('Y') ?> CLSU-HRMO. All rights reserved.<br>
            Powered by <span class="text-green-700">Management Information System Office (CLSU-MISO)</span>
        </div>
    </div>
</footer>

<script>
function requireLogin() {
    Swal.fire({
        icon: 'warning',
        title: 'Login Required',
        text: 'You need to log in first before applying for this job.',
        confirmButtonColor: '#0B6B3A',
        confirmButtonText: 'Go to Login'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= base_url('login') ?>";
        }
    });
}
</script>
</body>
</html>
