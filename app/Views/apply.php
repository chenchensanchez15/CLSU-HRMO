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

<body class="bg-gray-100 min-h-screen flex flex-col">

<header class="bg-clsuGreen text-white py-3 px-6 shadow">
    <div class="flex items-center gap-3">
        <img src="/HRMO/public/assets/images/clsu-logo2.png" alt="CLSU Logo" class="w-12 h-auto">
        <div class="flex flex-col leading-tight">
            <span class="text-xl font-bold">CLSU Online Job Application</span>
            <span class="text-sm font-medium opacity-90">Human Resource Management Office</span>
        </div>
    </div>
</header>

<main class="max-w-3xl mx-auto bg-white my-6 p-6 rounded-lg shadow text-sm">

    <h2 class="text-lg font-semibold mb-2">Applying for a Regular Position</h2>
    <p class="font-bold text-clsuGreen mb-3">
        <?= esc($job['position_title']) ?> (<?= esc($job['monthly_salary']) ?>)
    </p>

    <hr class="my-4">

<form id="applicationForm" method="POST" action="<?= base_url('applications/submit/' . $job['item_no']) ?>" enctype="multipart/form-data">

     <input type="hidden" name="job_position_id" value="<?= esc($job['id']) ?>">

        <!-- Step 1: Personal Information -->
        <div class="step" id="step-1">
            <div class="bg-gray-100 px-3 py-1 rounded font-semibold text-xs mb-2">
                Personal Information
            </div>
            <div class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                    <div>
                        <label class="text-xs font-medium">Surname *</label>
                        <input type="text" name="last_name" placeholder="Enter Surname" required class="border rounded px-2 py-1 text-xs w-full">
                    </div>
                    <div>
                        <label class="text-xs font-medium">First Name *</label>
                        <input type="text" name="first_name" placeholder="Enter First Name" required class="border rounded px-2 py-1 text-xs w-full">
                    </div>
                    <div>
                        <label class="text-xs font-medium">Middle Name</label>
                        <input type="text" name="middle_name" placeholder="Enter Middle Name" class="border rounded px-2 py-1 text-xs w-full">
                    </div>
                    <div>
                        <label class="text-xs font-medium">Jr./Sr.</label>
                        <input type="text" name="name_extension" placeholder="Enter Extension" class="border rounded px-2 py-1 text-xs w-full">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div>
                        <label class="text-xs font-medium">Date of Birth *</label>
                        <input type="date" name="birth_date" required class="border rounded px-2 py-1 text-xs w-full">
                    </div>
                    <div>
                        <label class="text-xs font-medium">Place of Birth</label>
                        <input type="text" name="place_of_birth" placeholder="Enter Place of Birth" class="border rounded px-2 py-1 text-xs w-full">
                    </div>
                    <div>
                        <label class="text-xs font-medium">Sex *</label>
                        <select name="sex" required class="border rounded px-2 py-1 text-xs w-full">
                            <option value="">Select Sex</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label class="text-xs font-medium">Civil Status</label>
                        <input type="text" name="civil_status" placeholder="Enter Civil Status" class="border rounded px-2 py-1 text-xs w-full">
                    </div>
                    <div>
                        <label class="text-xs font-medium">Citizenship</label>
                        <input type="text" name="citizenship" placeholder="Enter Citizenship" class="border rounded px-2 py-1 text-xs w-full">
                    </div>
                </div>

                <div class="text-right">
                    <button type="button" onclick="nextStep(1)" class="bg-clsuGreen text-white px-4 py-2 rounded text-xs font-semibold hover:bg-green-800">
                        Next
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 2: Family Background -->
        <div class="step hidden" id="step-2">
            <div>
                <div class="bg-gray-100 px-3 py-2 rounded font-semibold text-sm mb-4">
                    Family Background
                </div>
                <div class="space-y-6">
                    <!-- Spouse -->
                    <div>
                        <p class="font-semibold text-sm mb-2 text-text-black">Spouse</p>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-3">
                            <div>
                                <label class="text-xs text-text-black">Surname</label>
                                <input type="text" name="spouse_surname" placeholder="Surname" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                            <div>
                                <label class="text-xs text-text-black">First Name</label>
                                <input type="text" name="spouse_first_name" placeholder="First Name" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                            <div>
                                <label class="text-xs text-text-black">Middle Name</label>
                                <input type="text" name="spouse_middle_name" placeholder="Middle Name" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                            <div>
                                <label class="text-xs text-text-black">Extension</label>
                                <input type="text" name="spouse_ext_name" placeholder="Extension name" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs">Occupation</label>
                                <input type="text" name="spouse_occupation" placeholder="Occupation" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                            <div>
                                <label class="text-xs">Contact Number</label>
                                <input type="text" name="spouse_contact" placeholder="Contact Number" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                        </div>
                    </div>
                    <!-- Father -->
                    <div>
                        <p class="font-semibold text-sm mb-2 text-text-black">Father</p>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                            <div>
                                <label class="text-xs text-text-black">Surname</label>
                                <input type="text" name="father_surname" placeholder="Surname" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                            <div>
                                <label class="text-xs text-text-black">First Name</label>
                                <input type="text" name="father_first_name" placeholder="First Name" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                            <div>
                                <label class="text-xs text-text-black">Middle Name</label>
                                <input type="text" name="father_middle_name" placeholder="Middle Name" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                            <div>
                                <label class="text-xs text-text-black">Extension</label>
                                <input type="text" name="father_ext_name" placeholder="Extension name" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                        </div>
                    </div>
                    <!-- Mother -->
                    <div>
                        <p class="font-semibold text-sm mb-2 text-text-black">Mother (Maiden Name)</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="text-xs text-text-black">Surname</label>
                                <input type="text" name="mother_maiden_surname" placeholder="Surname" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                            <div>
                                <label class="text-xs text-text-black">First Name</label>
                                <input type="text" name="mother_first_name" placeholder="First Name" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                            <div>
                                <label class="text-xs text-text-black">Middle Name</label>
                                <input type="text" name="mother_middle_name" placeholder="Middle Name" class="border rounded px-2 py-2 text-sm w-full">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right mt-4">
                <button type="button" onclick="prevStep(2)" class="bg-gray-400 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-gray-500">
                    Previous
                </button>
                <button type="button" onclick="nextStep(2)" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold hover:bg-green-800">
                    Next
                </button>
            </div>
        </div>

        <!-- Step 3: Educational Background -->
        <div class="step hidden" id="step-3">
            <div>
                <div class="bg-gray-100 px-3 py-2 rounded font-semibold text-sm mb-4">Educational Background</div>
                <div class="space-y-6">
                    <!-- Elementary -->
                    <div>
                        <p class="font-semibold text-sm mb-2">Elementary</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div><label class="text-xs">Name of School</label><input type="text" name="elementary_school" class="border rounded px-2 py-2 text-sm w-full"></div>
                            <div><label class="text-xs">Location</label><input type="text" name="elementary_location" class="border rounded px-2 py-2 text-sm w-full"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div><label class="text-xs">Year Graduated</label><input type="text" name="elementary_year" class="border rounded px-2 py-2 text-sm w-full"></div>
                            <div><label class="text-xs">Awards / Honors (if any)</label><input type="text" name="elementary_awards" class="border rounded px-2 py-2 text-sm w-full"></div>
                        </div>
                    </div>
                    <!-- High School -->
                    <div>
                        <p class="font-semibold text-sm mb-2">High School</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div><label class="text-xs">Name of School</label><input type="text" name="highschool_school" class="border rounded px-2 py-2 text-sm w-full"></div>
                            <div><label class="text-xs">Location</label><input type="text" name="highschool_location" class="border rounded px-2 py-2 text-sm w-full"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div><label class="text-xs">Year Graduated</label><input type="text" name="highschool_year" class="border rounded px-2 py-2 text-sm w-full"></div>
                            <div><label class="text-xs">Awards / Honors (if any)</label><input type="text" name="highschool_awards" class="border rounded px-2 py-2 text-sm w-full"></div>
                        </div>
                    </div>
                    <!-- College -->
                    <div>
                        <p class="font-semibold text-sm mb-2">College</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div><label class="text-xs">Name of School</label><input type="text" name="college_school" class="border rounded px-2 py-2 text-sm w-full"></div>
                            <div><label class="text-xs">Location</label><input type="text" name="college_location" class="border rounded px-2 py-2 text-sm w-full"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div><label class="text-xs">Year Graduated</label><input type="text" name="college_year" class="border rounded px-2 py-2 text-sm w-full"></div>
                            <div><label class="text-xs">Honors / Awards (if any)</label><input type="text" name="college_awards" class="border rounded px-2 py-2 text-sm w-full"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right mt-4">
                <button type="button" onclick="prevStep(3)" class="bg-gray-400 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-gray-500">Previous</button>
                <button type="button" onclick="nextStep(3)" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold hover:bg-green-800">Next</button>
            </div>
        </div>

        <!-- Step 4: Work Experience -->
        <div class="step hidden" id="step-4">
            <div>
                <div class="bg-gray-100 px-3 py-2 rounded font-semibold text-sm mb-3">Work Experience</div>
                <div class="space-y-3">
                    <input type="text" name="current_work" placeholder="Current Work / Position / Agency" class="border rounded px-2 py-2 text-sm w-full">
                    <input type="text" name="previous_work" placeholder="Previous Work / Position / Agency" class="border rounded px-2 py-2 text-sm w-full">
                    <input type="text" name="work_duration" placeholder="Duration (From – To)" class="border rounded px-2 py-2 text-sm w-full">
                    <input type="text" name="work_awards" placeholder="Awards / Achievements" class="border rounded px-2 py-2 text-sm w-full">
                </div>
            </div>
            <div class="text-right mt-3">
                <button type="button" onclick="prevStep(4)" class="bg-gray-400 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-gray-500">Previous</button>
                <button type="button" onclick="nextStep(4)" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold hover:bg-green-800">Next</button>
            </div>
        </div>

        <!-- Step 5: File Attachments & Submit -->
        <div class="step hidden" id="step-5">
            <div>
                <div class="bg-gray-100 px-3 py-2 rounded font-semibold text-sm mb-4">File Attachments</div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium">1. Resume / Curriculum Vitae (PDF)</label>
                        <input type="file" name="resume" accept=".pdf" required class="border rounded px-2 py-2 text-sm w-full">
                        <p class="text-xs text-gray-500">Upload your latest resume in PDF format.</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium">2. Government-issued ID (Front)</label>
                        <input type="file" name="id_front" accept=".jpg,.jpeg,.png,.pdf" required class="border rounded px-2 py-2 text-sm w-full">
                    </div>
                    <div>
                        <label class="text-xs font-medium">3. Government-issued ID (Back)</label>
                        <input type="file" name="id_back" accept=".jpg,.jpeg,.png,.pdf" required class="border rounded px-2 py-2 text-sm w-full">
                    </div>
                    <div>
                        <label class="text-xs font-medium">4. Additional ID (optional)</label>
                        <input type="file" name="additional_id" accept=".jpg,.jpeg,.png,.pdf" class="border rounded px-2 py-2 text-sm w-full">
                        <p class="text-xs text-gray-500">Any other valid government ID (optional).</p>
                    </div>
                    <p class="text-xs text-gray-500">Accepted formats: PDF, JPG, PNG | Maximum file size: 5MB per file</p>
                </div>
            </div>
            <div class="text-right mt-4">
                <button type="button" onclick="prevStep(5)" class="bg-gray-400 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-gray-500">Previous</button>
                <button type="submit" id="submitApplication" class="bg-clsuGreen text-white px-4 py-2 rounded text-sm font-semibold hover:bg-green-800">Submit Application</button>
            </div>
        </div>

    </form>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function nextStep(current) {
    document.getElementById('step-' + current).classList.add('hidden');
    document.getElementById('step-' + (current + 1)).classList.remove('hidden');
}
function prevStep(current) {
    document.getElementById('step-' + current).classList.add('hidden');
    document.getElementById('step-' + (current - 1)).classList.remove('hidden');
}

// SweetAlert2 confirmation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('applicationForm');
    const submitBtn = document.getElementById('submitApplication');

    submitBtn.addEventListener('click', function(e) {
        e.preventDefault(); // prevent immediate form submission
        Swal.fire({
            title: 'Are you sure?',
            text: "Please confirm that all your data and information are correct.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0B6B3A',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('applicationForm');
    const submitBtn = document.getElementById('submitApplication');

    submitBtn.addEventListener('click', function(e) {
        e.preventDefault(); // prevent default form submission

        Swal.fire({
            title: 'Are you sure?',
            text: "Please confirm that all your data and information are correct.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0B6B3A',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {

                // Prepare form data including files
                const formData = new FormData(form);

                // Send AJAX request
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text()) // you can return JSON from controller if you prefer
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Application Submitted!',
                        text: 'Your job application has been successfully sent.',
                        timer: 2500,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect after alert closes
                        window.location.href = '<?= base_url("dashboard") ?>';
                    });
                })
                .catch(error => {
                    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                    console.error(error);
                });
            }
        });
    });
});
</script>

</body>
</html>
