<?php
include_once('../include/header.php');
include('../../include/connection.php');

// if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php");
//     exit();
// }

// --- Main Query: Retrieve all modules with their course names ---
// CORRECTED: Join on modules.course_id = course.id
$query = "SELECT m.*, c.course_name 
          FROM modules m 
          LEFT JOIN course c ON m.course_id = c.id";
$modules = mysqli_query($con, $query);

// --- Helper Query: Get all courses for dropdowns ---
// CORRECTED: Select course.id, not course.course_no
$course_query = "SELECT id, course_name FROM course ORDER BY course_name";
$courses_result = mysqli_query($con, $course_query);
$courses_list = [];
while ($course_row = mysqli_fetch_assoc($courses_result)) {
    $courses_list[] = $course_row;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Modules Dashboard</title>
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-accent': '#10b981',
                        /* Emerald Green */
                        'primary-dark': '#059669',
                        'secondary-dark': '#1e293b',
                        /* Slate-800 */
                    },
                    borderRadius: {
                        'xl': '0.75rem',
                    },
                    fontFamily: {
                        // Set Inter as the default font
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    <style>
        /* Apply the Inter font to the whole page */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 p-6 sm:p-8">

    <div class="max-w-7xl mx-auto">

        <!-- Header Title -->
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Manage Modules</h2>

        <!-- Manage Modules Table -->
        <div class="bg-white p-6 rounded-xl shadow-lg mt-8">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-xl font-semibold text-gray-800">All Modules</h3>
                <button id="addModuleBtn" class="bg-primary-accent text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition duration-150">
                    Add New Module
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">
                                Module Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Course
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">

                        <?php
                        if ($modules && mysqli_num_rows($modules) > 0) {
                            while ($row = mysqli_fetch_assoc($modules)) {
                        ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($row['module_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($row['course_name'] ?? 'N/A'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <!-- Edit button -->
                                        <button type="button" class="text-indigo-600 hover:text-indigo-900 transition duration-150 edit-btn" 
                                            data-id="<?php echo $row['id']; ?>" 
                                            data-name="<?php echo htmlspecialchars($row['module_name']); ?>" 
                                            data-course-id="<?php echo htmlspecialchars($row['course_id']); ?>">
                                            Edit
                                        </button>
                                        
                                        <!-- Delete button -->
                                        <button type="button" class="text-red-600 hover:text-red-900 transition duration-150 delete-btn" 
                                            data-id="<?php echo $row['id']; ?>" 
                                            data-name="<?php echo htmlspecialchars($row['module_name']); ?>">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            } // End while
                        } else {
                            ?>
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No modules found.
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

    <!-- Add Module Modal -->
    <div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-xl font-semibold text-gray-800">Add New Module</h3>
                <button id="closeAddModal" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            <form action="add_module.php" method="POST" class="p-6 space-y-4">
                <div>
                    <label for="add_module_name" class="block text-sm font-medium text-gray-700">Module Name</label>
                    <input type="text" id="add_module_name" name="module_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>
                <div>
                    <label for="add_course_id" class="block text-sm font-medium text-gray-700">Course</label>
                    <select id="add_course_id" name="course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="">Select a course</option>
                        <?php foreach ($courses_list as $course) : ?>
                            <option value="<?php echo htmlspecialchars($course['id']); ?>">
                                <?php echo htmlspecialchars($course['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-end pt-4">
                    <button type="button" id="cancelAddButton" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 mr-2">Cancel</button>
                    <button type="submit" class="bg-primary-accent text-white px-4 py-2 rounded-lg hover:bg-primary-dark">Add Module</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Module Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-xl font-semibold text-gray-800">Edit Module</h3>
                <button id="closeEditModal" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            <form action="update_module.php" method="POST" class="p-6 space-y-4">
                <input type="hidden" id="edit_module_id" name="module_id">
                
                <div>
                    <label for="edit_module_name" class="block text-sm font-medium text-gray-700">Module Name</label>
                    <input type="text" id="edit_module_name" name="module_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>
                <div>
                    <label for="edit_course_id" class="block text-sm font-medium text-gray-700">Course</LabeL>
                    <select id="edit_course_id" name="course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="">Select a course</option>
                        <?php foreach ($courses_list as $course) : ?>
                            <option value="<?php echo htmlspecialchars($course['id']); ?>">
                                <?php echo htmlspecialchars($course['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="button" id="cancelEditButton" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 mr-2">Cancel</button>
                    <button type="submit" class="bg-primary-accent text-white px-4 py-2 rounded-lg hover:bg-primary-dark">Update Module</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Module Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-xl font-semibold text-gray-800">Confirm Deletion</h3>
                <button id="closeDeleteModal" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            <div class="p-6">
                <p class="text-gray-700">Are you sure you want to delete this module?
                    <br>
                    <strong id="delete_module_name" class="font-semibold text-gray-900"></strong>
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
        document.addEventListener('DOMContentLoaded', () => {
            // --- Common Modal Functions ---
            const openModal = (modal) => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };
            const closeModal = (modal) => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };
            const addModalListeners = (modal, closeBtn, cancelBtn) => {
                closeBtn.addEventListener('click', () => closeModal(modal));
                cancelBtn.addEventListener('click', () => closeModal(modal));
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) closeModal(modal);
                });
            };

            // --- Add Modal ---
            const addModal = document.getElementById('addModal');
            const addModuleBtn = document.getElementById('addModuleBtn');
            const closeAddModal = document.getElementById('closeAddModal');
            const cancelAddButton = document.getElementById('cancelAddButton');
            
            addModuleBtn.addEventListener('click', () => openModal(addModal));
            addModalListeners(addModal, closeAddModal, cancelAddButton);

            // --- Edit Modal ---
            const editModal = document.getElementById('editModal');
            const closeEditModal = document.getElementById('closeEditModal');
            const cancelEditButton = document.getElementById('cancelEditButton');
            const editButtons = document.querySelectorAll('.edit-btn');

            const moduleIdInput = document.getElementById('edit_module_id');
            const moduleNameInput = document.getElementById('edit_module_name');
            const courseIdInput = document.getElementById('edit_course_id');
            
            editButtons.forEach(button => {
                button.addEventListener('click', () => {
                    moduleIdInput.value = button.dataset.id;
                    moduleNameInput.value = button.dataset.name;
                    courseIdInput.value = button.dataset.courseId;
                    openModal(editModal);
                });
            });
            addModalListeners(editModal, closeEditModal, cancelEditButton);

            // --- Delete Modal ---
            const deleteModal = document.getElementById('deleteModal');
            const closeDeleteModal = document.getElementById('closeDeleteModal');
            const cancelDeleteButton = document.getElementById('cancelDeleteButton');
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const deleteModuleName = document.getElementById('delete_module_name');
            const confirmDeleteButton = document.getElementById('confirmDeleteButton');

            deleteButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    const name = button.dataset.name;
                    
                    deleteModuleName.textContent = name;
                    confirmDeleteButton.href = `delete_module.php?id=${id}`;
                    openModal(deleteModal);
                });
            });
            addModalListeners(deleteModal, closeDeleteModal, cancelDeleteButton);
        });
    </script>

</body>

</html>

