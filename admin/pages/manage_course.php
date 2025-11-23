<?php
include_once('../include/header.php');
include('../../include/connection.php');

// if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php");
//     exit();
// }

// --- Message Handling Logic ---
$message = '';
$message_type = ''; // 'success' or 'error'

if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'updated':
            $message = 'Course updated successfully!';
            $message_type = 'success';
            break;
        case 'deleted':
            $message = 'Course deleted successfully.';
            $message_type = 'success';
            break;
        case 'error':
            $message = 'An error occurred. Please try again.';
            $message_type = 'error';
            break;
    }
}
// --- END: Message Handling Logic ---


// Retrieve all courses from the database, joining with staff to get instructor name
// Retrieve all courses...
// In manage_course.php, update your SELECT query to include course_video:

$query = "SELECT c.id, 
                 c.course_no, 
                 c.course_name, 
                 c.course_fee, 
                 c.course_duration, 
                 c.course_image, 
                 c.course_video,  -- MAKE SURE THIS IS INCLUDED
                 c.qualifications, 
                 c.status,
                 s.first_name, 
                 s.last_name, 
                 s.staff_id
          FROM course c 
          LEFT JOIN staff s ON c.course_no = s.course_no AND s.position = 'Instructor'
          ORDER BY c.id";
          
$courses = mysqli_query($con, $query);

if (!$courses) {
    die("Query failed: " . mysqli_error($con));
}

// --- Get all available instructors ---
$instructor_query = "SELECT staff_id, first_name, last_name, course_no FROM staff WHERE position = 'Instructor' ORDER BY first_name, last_name";
$instructor_result = mysqli_query($con, $instructor_query);
$instructors_list = [];
while ($instructor_row = mysqli_fetch_assoc($instructor_result)) {
    $instructors_list[] = $instructor_row;
}
$instructors_json = json_encode($instructors_list);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 p-6 sm:p-8">

    <div class="max-w-7xl mx-auto">

        <h2 class="text-3xl font-bold text-gray-800 mb-6">Manage Courses</h2>

        <?php if (!empty($message)): ?>
            <div id="alert-message" class="relative <?php echo ($message_type == 'success') ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> border px-4 py-3 rounded-lg mb-6" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-dismiss="alert-message">
                    <span class="text-2xl font-bold">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-xl shadow-lg mt-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Manage Courses</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">Course No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">Course Name</th>
                            <!--<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>-->
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                          <?php if (mysqli_num_rows($courses) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($courses)): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($row['course_no']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($row['course_name']); ?>
                                    </td>
                                    
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        LKR <?php echo htmlspecialchars($row['course_fee']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($row['status'] == 'active') : ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        <?php else : ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <!-- In your manage_course.php table, verify the edit button has ALL these attributes: -->

                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 transition duration-150 edit-btn" 
                                        data-id="<?php echo $row['id']; ?>" 
                                        data-name="<?php echo htmlspecialchars($row['course_name']); ?>" 
                                        data-fee="<?php echo htmlspecialchars($row['course_fee']); ?>" 
                                        data-status="<?php echo $row['status']; ?>"
                                        data-course-no="<?php echo htmlspecialchars($row['course_no']); ?>"
                                        data-staff-id="<?php echo htmlspecialchars($row['staff_id'] ?? ''); ?>"
                                        data-course-duration="<?php echo htmlspecialchars($row['course_duration']); ?>"
                                        data-course-image="<?php echo htmlspecialchars($row['course_image'] ?? ''); ?>"
                                        data-qualifications="<?php echo htmlspecialchars($row['qualifications'] ?? ''); ?>"
                                        data-course-video="<?php echo htmlspecialchars($row['course_video'] ?? ''); ?>">
                                        Edit
                                    </button>

<!-- IMPORTANT: Make sure the last line has 'course_video' not just 'video' -->
                                        <button type="button" class="text-red-600 hover:text-red-900 transition duration-150 delete-btn"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['course_name']); ?>">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No courses found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>

   
         <div id="editModal" class=" fixed inset-0 z-50 bg-gray-600 bg-opacity-50 hidden overflow-y-scroll p-20 items-center justify-center p-4">
        <div class="max-h-[600px] overflow-y-scroll bg-white rounded-xl shadow-lg w-full max-w-lg">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-xl font-semibold text-gray-800">Edit Course</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form action="../lib/update_course.php" method="POST" class="p-6 space-y-4" enctype="multipart/form-data">
                <input type="hidden" id="edit_course_id" name="course_id">
                <input type="hidden" id="edit_old_course_no" name="old_course_no">
                <input type="hidden" id="edit_old_staff_id" name="old_staff_id">
                <input type="hidden" id="edit_current_image" name="current_image">
                
                <div>
                    <label for="edit_course_name" class="block text-sm font-medium text-gray-700">Course Name</label>
                    <input type="text" id="edit_course_name" name="course_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>

                <div>
                    <label for="edit_qualifications" class="block text-sm font-medium text-gray-700">Entry Qualifications</label>
                    <textarea id="edit_qualifications" name="qualifications" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g., G.C.E. (O/L) Examination, Basic computer literacy"></textarea>
                </div>


                <div>
                    <label for="edit_course_no" class="block text-sm font-medium text-gray-700">Course No</label>
                    <input type="text" id="edit_course_no" name="course_no" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>

                <div>
                    <label for="edit_course_duration" class="block text-sm font-medium text-gray-700">Course Duration (in months)</label>
                    <input type="number" id="edit_course_duration" name="course_duration" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>
                
                <div>
                    <label for="edit_course_fee" class="block text-sm font-medium text-gray-700">Course Fee</label>
                    <input type="text" id="edit_course_fee" name="course_fee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Image</label>
                    <img id="edit_image_preview" src="" alt="Course Image" class="mt-2 w-32 h-32 object-cover rounded-md border border-gray-200 bg-gray-50">
                    
                    <label for="edit_course_image" class="block text-sm font-medium text-gray-700 mt-4">Upload New Image (optional)</label>
                    <input type="file" id="edit_course_image" name="course_image" class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100">
                </div>

                 <div>
                    <label for="edit_course_video" class="block text-sm font-medium text-gray-700">Course Video</label>
                    <input type="text" id="edit_course_video" name="course_video" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" >
                </div>

                <div>
                    <label for="edit_status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="edit_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="flex justify-end pt-4">
                    <button type="button" id="cancelButton" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 mr-2">Cancel</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Update Course</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-xl font-semibold text-gray-800">Confirm Deletion</h3>
                <button id="closeDeleteModal" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            <div class="p-6">
                <p class="text-gray-700">Are you sure you want to delete this course?
                    <br>
                    <strong id="delete_course_name" class="font-semibold text-gray-900"></strong>
                </p>
                <p class="text-sm text-red-600 mt-2">This action cannot be undone.</p>
            </div>
            <div class="flex justify-end p-4 bg-gray-50 rounded-b-xl">
                <button type="button" id="cancelDeleteButton" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 mr-2">Cancel</button>
                <a id="confirmDeleteButton" href="#" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete</a>
            </div>
        </div>
    </div>

    <script>
        const allInstructors = <?php echo $instructors_json; ?>;
        console.log('Available Instructors:', allInstructors);

        document.addEventListener('DOMContentLoaded', () => {
            
            // --- Edit Modal Elements ---
            const editModal = document.getElementById('editModal');
            const closeModal = document.getElementById('closeModal');
            const cancelButton = document.getElementById('cancelButton');
            const editButtons = document.querySelectorAll('.edit-btn');
            
            // --- Edit Form Fields ---
            const courseIdInput = document.getElementById('edit_course_id');
            const courseNameInput = document.getElementById('edit_course_name');
            const courseFeeInput = document.getElementById('edit_course_fee');
            const statusInput = document.getElementById('edit_status');
            const qualificationsInput = document.getElementById('edit_qualifications');
            const oldCourseNoInput = document.getElementById('edit_old_course_no');
            const oldStaffIdInput = document.getElementById('edit_old_staff_id');
            const courseNoInput = document.getElementById('edit_course_no');
            const courseDurationInput = document.getElementById('edit_course_duration');
            const currentImageInput = document.getElementById('edit_current_image');
            const imagePreview = document.getElementById('edit_image_preview');
            const imageFileInput = document.getElementById('edit_course_image');
            const courseVideoInput = document.getElementById('edit_course_video');

            // --- FIX 1: ADDED MISSING VARIABLE DEFINITIONS ---
            // --- Delete Modal Elements ---
            const deleteModal = document.getElementById('deleteModal');
            const closeDeleteModal = document.getElementById('closeDeleteModal');
            const cancelDeleteButton = document.getElementById('cancelDeleteButton');
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const deleteCourseName = document.getElementById('delete_course_name');
            const confirmDeleteButton = document.getElementById('confirmDeleteButton');

            // --- Alert Message Element ---
            const alertMessage = document.getElementById('alert-message');
            // --- END FIX 1 ---
            
            // --- Edit Modal Functions ---
            const openEditModal = () => {
                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            };
            const hideEditModal = () => {
                editModal.classList.add('hidden');
                editModal.classList.remove('flex');
                imageFileInput.value = null; 
            };

            // --- Delete Modal Functions ---
            const openDeleteModal = () => {
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            };
            const hideDeleteModal = () => {
                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');
            };

            // --- Attach EDIT Button Logic ---
            // Replace the existing edit button click handler with this fixed version:

            // COMPLETE FIXED VERSION - Replace your entire edit button handler

            editButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Set all basic fields
                    courseIdInput.value = button.dataset.id;
                    courseNameInput.value = button.dataset.name;
                    courseFeeInput.value = button.dataset.fee;
                    statusInput.value = button.dataset.status;
            
                    // Set course number fields
                    const currentCourseNo = button.dataset.courseNo;
                    oldCourseNoInput.value = currentCourseNo;
                    courseNoInput.value = currentCourseNo;
                    
                    // Set staff and duration
                    const currentStaffId = button.dataset.staffId;
                    oldStaffIdInput.value = currentStaffId;
                    courseDurationInput.value = button.dataset.courseDuration;
            
                    // Set qualifications
                    qualificationsInput.value = button.dataset.qualifications || '';
            
                    // CRITICAL FIX: Set video field explicitly
                    const currentVideo = button.dataset.courseVideo;
                    console.log('Video from dataset:', currentVideo); // Debug log
                    courseVideoInput.value = currentVideo || '';
            
                    // Handle image
                    const currentImage = button.dataset.courseImage;
                    currentImageInput.value = currentImage;
            
                    if (currentImage) {
                        imagePreview.src = `../../uploads/course_images/${currentImage}`; 
                        imagePreview.alt = button.dataset.name;
                    } else {
                        imagePreview.src = 'https://via.placeholder.com/128x128.png?text=No+Image'; 
                        imagePreview.alt = 'No Image';
                    }
                    
                    // Open modal AFTER all values are set
                    openEditModal();
                    
                    // Double-check after modal opens (backup fix)
                    setTimeout(() => {
                        if (!courseVideoInput.value && currentVideo) {
                            courseVideoInput.value = currentVideo;
                            console.log('Video field re-populated after modal open');
                        }
                    }, 100);
                });
            });
            
            // --- Add preview logic for file input ---
            imageFileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        imagePreview.src = event.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });

            // --- Attach Edit Modal Close Logic ---
            closeModal.addEventListener('click', hideEditModal);
            cancelButton.addEventListener('click', hideEditModal);
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal) hideEditModal();
            });

            // --- Attach DELETE Button Logic ---
            deleteButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    const name = button.dataset.name;
                    deleteCourseName.textContent = name;
                    confirmDeleteButton.href = `../lib/delete_course.php?id=${id}`;
                    openDeleteModal();
                });
            });

            // --- Attach Delete Modal Close Logic ---
            closeDeleteModal.addEventListener('click', hideDeleteModal);
            cancelDeleteButton.addEventListener('click', hideDeleteModal);
            deleteModal.addEventListener('click', (e) => {
                if (e.target === deleteModal) hideDeleteModal();
            });

            // --- Attach ALERT Message Logic ---
            if (alertMessage) {
                // This 'closeButton' logic now works because 'alertMessage' is defined
                const closeButton = alertMessage.querySelector('button[data-dismiss="alert-message"]');
                
                if (closeButton) {
                    closeButton.addEventListener('click', () => {
                        alertMessage.style.display = 'none';
                    });
                }

                // --- FIX 2: Clear URL query string to prevent message on refresh ---
                if (window.history.replaceState) {
                    // Get the current URL without the query string
                    const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                    // Replace the current history state with the clean URL
                    window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
                }
                // --- END FIX 2 ---
                
                // Auto-hide after 5 seconds (Your existing logic)
                setTimeout(() => {
                    alertMessage.style.transition = 'opacity 0.5s ease';
                    alertMessage.style.opacity = '0';
                    setTimeout(() => {
                        alertMessage.style.display = 'none';
                    }, 500); // Wait for fade-out to finish
                }, 5000);
            }
        });
    </script>
    <?php include_once('../include/footer.php'); ?>
    </body>
</html>