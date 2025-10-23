<?php
include('../../include/connection.php');

session_start();
// if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php");
//     exit();
// }


// Retrieve all courses from the database, joining with staff to get instructor name
$query = "SELECT c.*, s.first_name, s.last_name 
          FROM course c 
          LEFT JOIN staff s ON c.course_no = s.course_no";
$courses = mysqli_query($con, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course Dashboard</title>
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Include Chart.js -->
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

        <!-- Header Title -->
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Manage Courses</h2>



        <!-- Manage Courses Table -->
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
                                        $<?php echo htmlspecialchars($row['course_fee']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($row['status'] == 'active') : ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        <?php else : ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <!-- Links to edit and delete pages, passing the course ID -->
                                        <a href="edit_course.php?id=<?php echo $row['id']; ?>" class="text-indigo-600 hover:text-indigo-900 transition duration-150">Edit</a>
                                        <a href="delete_course.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900 transition duration-150">Delete</a>
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


</body>
</html>

