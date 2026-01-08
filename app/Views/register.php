<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicant Registration | CLSU HRMO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
<body class="min-h-screen bg-gray-100 flex items-center justify-center px-4">

<div class="w-full max-w-2xl bg-white rounded-2xl shadow-lg overflow-hidden">

    <div class="bg-clsuGreen text-white px-8 py-6 text-center">
        <h1 class="text-2xl font-bold">Applicant Registration Form</h1>
        <p class="text-sm opacity-90">CLSU Online Job Application System</p>
    </div>

 <form class="p-8 space-y-6" action="<?= base_url('register/save') ?>" method="post">
        <div>
            <h2 class="text-lg font-semibold text-clsuGreen mb-4">Personal Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">First Name</label>
                    <input type="text" name="first_name" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-clsuGreen focus:outline-none" placeholder="Juan" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Middle Name</label>
                    <input type="text" name="middle_name" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-clsuGreen focus:outline-none" placeholder="Santos" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Last Name</label>
                    <input type="text" name="last_name" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-clsuGreen focus:outline-none" placeholder="Dela Cruz" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Extension</label>
                    <input type="text" name="extension" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-clsuGreen focus:outline-none" placeholder="Jr., Sr., III">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email Address</label>
            <input type="email" name="email" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-clsuGreen focus:outline-none" placeholder="example@email.com" required>
        </div>

        <div class="flex items-start gap-2 text-sm">
            <input type="checkbox" class="mt-1" required>
            <p>I confirm that the information provided is accurate and complete.</p>
        </div>

        <button type="submit" class="w-full bg-clsuGreen text-white py-3 rounded-md font-bold hover:bg-green-800 transition">
            Register
        </button>

       <p class="text-sm text-center">
    Already have an account?
    <a href="<?= base_url('login') ?>" class="text-clsuGreen font-semibold hover:underline">Login here</a>
</p>

    </form>
</div>

</body>
</html>
