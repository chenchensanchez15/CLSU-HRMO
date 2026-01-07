<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Job Application | Step 1 - Personal Information</title>
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

<body class="bg-gray-100 min-h-screen">

<header class="bg-clsuGreen text-white px-8 py-5">
    <h1 class="text-xl font-bold">CLSU HRMO Online Job Application</h1>
</header>

<main class="max-w-4xl mx-auto bg-white my-10 p-8 rounded-lg shadow">

    <h2 class="text-lg font-semibold mb-2">Applying for a regular position:</h2>
    <p class="text-md font-bold text-clsuGreen">Administrative Officer V (Php 51,304.00)</p>
    <a href="#" class="text-sm text-blue-600 hover:underline">Click here to view details</a>

    <hr class="my-6">

    <div class="text-sm mb-4">
        <p class="font-semibold">Privacy Note:</p>
        <p class="mb-3">In accordance with Republic Act No. 10173 (Data Privacy Act of 2012), all information you provide will be treated with strict confidentiality.</p>
        <p class="mb-3">CLSU adheres to the Equal Opportunity Principle in recruitment. Vacant positions are open to all qualified applicants regardless of gender, age, civil status, political affiliation, ethnicity, physical impairment, beliefs, or religion.</p>
        <p>Thank you for your cooperation.</p>
    </div>

    <div class="flex items-start gap-2 text-sm mb-6">
        <input type="checkbox" required class="mt-1">
        <p>I hereby authorize CLSU to collect, process, and store my personal data.</p>
    </div>

    <div class="bg-gray-100 px-4 py-2 rounded mb-6 font-semibold text-sm">
        Personal Information
    </div>

    <form class="space-y-6">

        <div>
            <label class="block font-semibold mb-2">Full Name</label>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" placeholder="First Name *" required class="border rounded px-3 py-2">
                <input type="text" placeholder="Last Name *" required class="border rounded px-3 py-2">
                <input type="text" placeholder="Middle Name" class="border rounded px-3 py-2">
                <input type="text" placeholder="Name Extension (e.g. Jr.)" class="border rounded px-3 py-2">
            </div>
        </div>

        <div>
            <label class="block font-semibold mb-2">Contact Number</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" placeholder="Mobile Number *" required class="border rounded px-3 py-2">
                <input type="text" placeholder="Mobile Number (alternate)" class="border rounded px-3 py-2">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="email" placeholder="Email Address *" required class="border rounded px-3 py-2">
            <input type="date" required class="border rounded px-3 py-2">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <select required class="border rounded px-3 py-2">
                <option value="">Sex *</option>
                <option>Male</option>
                <option>Female</option>
            </select>
            <select required class="border rounded px-3 py-2">
                <option value="">Civil Status *</option>
                <option>Single</option>
                <option>Married</option>
                <option>Widowed</option>
                <option>Divorced</option>
                <option>Separated</option>
            </select>
            <input type="text" placeholder="Religion" class="border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-semibold mb-2">Address</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <select required class="border rounded px-3 py-2">
                    <option value="">Province *</option>
                </select>
                <select required class="border rounded px-3 py-2">
                    <option value="">City / Municipality *</option>
                </select>
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="bg-clsuGreen text-white px-6 py-3 rounded font-semibold hover:bg-green-800">
                Next Step
            </button>
        </div>

    </form>
</main>

<footer class="bg-clsuGreen text-white text-center py-4">
    <p>&copy; 2026 Central Luzon State University – HRMO</p>
</footer>

</body>
</html>
