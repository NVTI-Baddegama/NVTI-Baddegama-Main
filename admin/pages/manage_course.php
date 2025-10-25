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
$query = "SELECT c.*, s.first_name, s.last_name, s.staff_id 
          FROM course c 
          LEFT JOIN staff s ON c.course_no = s.course_no";
$courses = mysqli_query($con, $query);

// --- NEW: Get all available instructors ---
// We get instructors who are 'Instructors' AND are either unassigned (course_no IS NULL)
// OR are already assigned to *this* course (which we handle in the loop)
$instructor_query = "SELECT staff_id, first_name, last_name, course_no FROM staff WHERE position = 'Instructors'";
$instructor_result = mysqli_query($con, $instructor_query);
$instructors_list = [];
while ($instructor_row = mysqli_fetch_assoc($instructor_result)) {
    $instructors_list[] = $instructor_row;
}

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
        /* Apply the Inter font to the whole page */
        body {
            font-family: 'Inter', sans-serif;
        }
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">
                                Course Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Instructor
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                          <?php
                          // Check if there are any courses
                          if (mysqli_num_rows($courses) > 0) {
                              // Loop through each course
                              while ($row = mysqli_fetch_assoc($courses)) {
                          ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($row['course_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php
                                        // Check if first_name exists (due to LEFT JOIN)
                                        if (!empty($row['first_name'])) {
                                            echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                                        } else {
                                            echo 'N/A'; // Display N/A if no instructor is assigned
                                        }
                                        ?>
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
                                        <button type="button" class="text-indigo-600 hover:text-indigo-900 transition duration-150 edit-btn" 
                                            data-id="<?php echo $row['id']; ?>" 
                                            data-name="<?php echo htmlspecialchars($row['course_name']); ?>" 
                                            data-fee="<?php echo htmlspecialchars($row['course_fee']); ?>" 
                                            data-status="<?php echo $row['status']; ?>"
                                            data-course-no="<?php echo htmlspecialchars($row['course_no']); ?>"
                                            data-staff-id="<?php echo htmlspecialchars($row['staff_id'] ?? ''); ?>">
                                            Edit
                                        </button>

                                    
                                        
                                        <button type="button" class="text-red-600 hover:text-red-900 transition duration-150 delete-btn"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['course_name']); ?>">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                              <?php
                              } // End while loop
                          } else {
                              // No courses found
                              ?>
                              <tr>
                                  <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                      No courses found.
                                  </td>
                              </tr>
                          <?php
                          } // End if/else
                          ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>

     <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center p-4">
         <div class="bg-white rounded-xl shadow-lg w-full max-w-lg">
             <div class="flex justify-between items-center p-4 border-b">
                 <h3 class="text-xl font-semibold text-gray-800">Edit Course</h3>
                 <button id="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
             </div>
             <form action="../lib/update_course.php" method="POST" class="p-6 space-y-4">
                 <input type="hidden" id="edit_course_id" name="course_id">
                 <input type="hidden" id="edit_course_no" name="course_no">
                 <input type="hidden" id="edit_old_staff_id" name="old_staff_id">
                 
                 <div>
                     <label for="edit_course_name" class="block text-sm font-medium text-gray-700">Course Name</label>
                     <input type="text" id="edit_course_name" name="course_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                 </div>

                 <!-- NEW: Instructor Dropdown -->
                 <div>
                    <label for="edit_new_staff_id" class="block text-sm font-medium text-gray-700">Instructor</label>
                    <select id="edit_new_staff_id" name="new_staff_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Unassign Instructor</option>
                        <?php foreach ($instructors_list as $instructor): ?>
                            <?php 
                            // Only show instructors who are unassigned OR assigned to this specific course
                            if ($instructor['course_no'] === null || $instructor['course_no'] === $row['course_no']): 
                            ?>
                            <option value="<?php echo htmlspecialchars($instructor['staff_id']); ?>">
                                <?php echo htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']); ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                 </div>
                 
                 <div>
                     <label for="edit_course_fee" class="block text-sm font-medium text-gray-700">Course Fee</label>
                     <input type="text" id="edit_course_fee" name="course_fee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
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
                     <button type="submit" class="bg-primary-accent text-white px-4 py-2 rounded-lg hover:bg-primary-dark">Update Course</button>
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
                <a id="confirmDeleteButton" href="../lib/delete_course.php" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete</a>
            </div>
        </div>
    </div>

    

        <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Edit Modal Elements ---
            const editModal = document.getElementById('editModal');
            const closeModal = document.getElementById('closeModal');
            const cancelButton = document.getElementById('cancelButton');
            const editButtons = document.querySelectorAll('.edit-btn');
            const courseIdInput = document.getElementById('edit_course_id');
            const courseNameInput = document.getElementById('edit_course_name');
            const courseFeeInput = document.getElementById('edit_course_fee');
            const statusInput = document.getElementById('edit_status');
            
            // --- NEW: Edit Modal Instructor Elements ---
            const instructorSelect = document.getElementById('edit_new_staff_id');
            const courseNoInput = document.getElementById('edit_course_no');
            const oldStaffIdInput = document.getElementById('edit_old_staff_id');

            // --- Delete Modal Elements ---
            const deleteModal = document.getElementById('deleteModal');
            const closeDeleteModal = document.getElementById('closeDeleteModal');
            const cancelDeleteButton = document.getElementById('cancelDeleteButton');
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const deleteCourseName = document.getElementById('delete_course_name');
            const confirmDeleteButton = document.getElementById('confirmDeleteButton');

            // --- Edit Modal Functions ---
            const openEditModal = () => {
                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            };
            const hideEditModal = () => {
                editModal.classList.add('hidden');
                editModal.classList.remove('flex');
            };

            editButtons.forEach(button => {
                button.addEventListener('click', () => {
                    courseIdInput.value = button.dataset.id;
                    courseNameInput.value = button.dataset.name;
                    courseFeeInput.value = button.dataset.fee;
                    statusInput.value = button.dataset.status;

                    // --- NEW: Populate Instructor Fields ---
                    courseNoInput.value = button.dataset.courseNo;
                    oldStaffIdInput.value = button.dataset.staffId;
                    instructorSelect.value = button.dataset.staffId; // Set dropdown to current instructor

                    // --- NEW: Dynamically update instructor dropdown options ---
                    // This ensures the *currently assigned* instructor is always an option,
                    // even if they are assigned to *this* course.
                    const currentCourseNo = button.dataset.courseNo;
                    
                    // Clear existing options (except the "Unassign" one)
                    Array.from(instructorSelect.options).forEach(option => {
                        if (option.value !== "") {
                            option.remove();
                        }
                    });

                    // Re-populate from the PHP list, but with dynamic logic
                    <?php foreach ($instructors_list as $instructor): ?>
                        var instructorCourseNo = "<?php echo $instructor['course_no'] ?? ''; ?>";
                        
                        // Show if: 1. Unassigned OR 2. Assigned to THIS course
                        if (instructorCourseNo === "" || instructorCourseNo === currentCourseNo) {
                            const option = document.createElement('option');
                            option.value = "<?php echo htmlspecialchars($instructor['staff_id']); ?>";
                            option.textContent = "<?php echo htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']); ?>";
                            instructorSelect.appendChild(option);
                        }
                    <?php endforeach; ?>
                    
                    // Reselect the correct instructor
                    instructorSelect.value = button.dataset.staffId; 

                    openEditModal();
                });
            });

            closeModal.addEventListener('click', hideEditModal);
            cancelButton.addEventListener('click', hideEditModal);
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal) hideEditModal();
            });

            // --- Delete Modal Functions ---
            const openDeleteModal = () => {
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            };
            const hideDeleteModal = () => {
                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');
            };

            deleteButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    const name = button.dataset.name;
                    
                    // Set the course name in the modal
                    deleteCourseName.textContent = name;
                    
                    // Set the href for the final delete button - ** NOTE: path corrected **
                    confirmDeleteButton.href = `../lib/delete_course.php?id=${id}`;
                    
                    openDeleteModal();
                });
            });

            closeDeleteModal.addEventListener('click', hideDeleteModal);
            cancelDeleteButton.addEventListener('click', hideDeleteModal);
            deleteModal.addEventListener('click', (e) => {
                if (e.target === deleteModal) hideDeleteModal();
            });


            // --- NEW: Alert Message Handling ---
            const alertMessage = document.getElementById('alert-message');
            if (alertMessage) {
                // Find the close button inside this specific alert
                const closeButton = alertMessage.querySelector('button[data-dismiss="alert-message"]');
                
                // Add click listener to close button
                if (closeButton) {
                    closeButton.addEventListener('click', () => {
                        alertMessage.style.display = 'none';
                    });
                }
                
                // Set timeout to auto-hide the message after 5 seconds
                setTimeout(() => {
                    alertMessage.style.transition = 'opacity 0.5s ease';
                    alertMessage.style.opacity = '0';
                    setTimeout(() => {
                        alertMessage.style.display = 'none';
                    }, 500); // 0.5s for fade animation
                }, 5000); // 5 seconds
            }
            // --- END: Alert Message Handling ---
        });
        </script>

</body>
</html>
