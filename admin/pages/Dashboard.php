<?php 
include_once('../include/header.php');

?>
<?php 
// --- Dashboard Data Fetching ---
include_once('../../include/connection.php'); // Go back two folders to get connection

// 1. Get Total Courses
$result_courses = $con->query("SELECT COUNT(*) as total_courses FROM course");
$total_courses = $result_courses->fetch_assoc()['total_courses'];

// 2. Get Total Instructors (Specific count)
$result_instructors = $con->query("SELECT COUNT(*) as total_instructors FROM staff WHERE position = 'Instructors'");
$total_instructors = $result_instructors->fetch_assoc()['total_instructors'];

// 3. Get Total Non-Academic Staff (Specific count)
$result_non_academic = $con->query("SELECT COUNT(*) as total_non_academic FROM staff WHERE position = 'Non-Academic Staff'");
$total_non_academic = $result_non_academic->fetch_assoc()['total_non_academic'];

// 4. Get Total Enrolled Students
$result_students = $con->query("SELECT COUNT(*) as total_enrolled FROM student_enrollments");
$total_enrolled = $result_students->fetch_assoc()['total_enrolled'];

?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Overview</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div
        class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 border-t-4 border-primary-accent">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Courses</p>
                <p class="text-4xl font-extrabold text-gray-900 mt-1"><?php echo $total_courses; ?></p>
            </div>
            <div class="bg-primary-accent/10 p-3 rounded-full text-primary-accent">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM21 21L3 21a2 2 0 01-2-2V5a2 2 0 012-2h18a2 2 0 012 2v14a2 2 0 01-2 2z">
                    </path>
                </svg>
            </div>
        </div>
        <p class="text-sm text-gray-400 mt-3">Currently active courses</p>
    </div>

    <div
        class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 border-t-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Instructors</p>
                <p class="text-4xl font-extrabold text-gray-900 mt-1"><?php echo $total_instructors; ?></p>
            </div>
            <div class="bg-indigo-500/10 p-3 rounded-full text-indigo-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm-6-4a6 6 0 00-4 0v1h8v-1m-4 0v-2">
                    </path>
                </svg>
            </div>
        </div>
        <p class="text-sm text-gray-400 mt-3">Total registered instructors</p>
    </div>

    <div
        class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 border-t-4 border-teal-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Non-Academic Staff</p>
                <p class="text-4xl font-extrabold text-gray-900 mt-1"><?php echo $total_non_academic; ?></p>
            </div>
            <div class="bg-teal-500/10 p-3 rounded-full text-teal-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.505-9-1.745M16 4V2M8 4V2m4 8a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zM5 16s2.5-3 7-3 7 3 7 3M5 16H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2v6a2 2 0 01-2 2h-2"></path></svg>
            </div>
        </div>
        <p class="text-sm text-gray-400 mt-3">Total registered support staff</p>
    </div>

    <div
        class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 border-t-4 border-amber-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Enrolled</p>
                <p class="text-4xl font-extrabold text-gray-900 mt-1"><?php echo $total_enrolled; ?></p>
            </div>
            <div class="bg-amber-500/10 p-3 rounded-full text-amber-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM12 18H8a2 2 0 00-2 2v1"></path>
                </svg>
            </div>
        </div>
        <p class="text-sm text-gray-400 mt-3">Total student applications</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Student Enrollments Over Time</h3>
        <div class="h-80">
            <canvas id="enrollmentChart"></canvas>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Quick Actions</h3>
            <div class="space-y-3">
                <button
                    class="w-full flex items-center justify-center p-3 text-sm font-medium rounded-lg text-white bg-primary-accent hover:bg-primary-dark transition duration-150 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Create New Course
                </button>
                <button
                    class="w-full flex items-center justify-center p-3 text-sm font-medium rounded-lg text-primary-accent border border-primary-accent hover:bg-primary-accent/10 transition duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7v4a1 1 0 01-1 1H4.5M8 7H6v4m2-4v4m2-4v4a1 1 0 01-1 1H9m3-5a2 2 0 100-4m0 4a2 2 0 110-4m-9 8h6m-6 4h6m-3-6a2 2 0 01-2-2v-4a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2z">
                        </path>
                    </svg>
                    View Instructor Reports
                </button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Recent Activity</h3>
            <ul class="space-y-4">
                <li class="flex items-start">
                    <div class="w-2 h-2 rounded-full bg-primary-accent mt-2 mr-3 flex-shrink-0"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">New Student Registered</p>
                        <p class="text-xs text-gray-500">Alex Johnson joined the platform 5 minutes ago.</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <div class="w-2 h-2 rounded-full bg-indigo-500 mt-2 mr-3 flex-shrink-0"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Course Updated</p>
                        <p class="text-xs text-gray-500">The 'Advanced React' course had 3 new modules added.</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <div class="w-2 h-2 rounded-full bg-amber-500 mt-2 mr-3 flex-shrink-0"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Instructor Added</p>
                        <p class="text-xs text-gray-500">Dr. Maya Patel was onboarded as a new data science instructor.
                        </p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg mt-8">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Latest Enrollments</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">
                        Student Name</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course
                        Enrolled</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Instructor</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">
                        Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">John Smith</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">JavaScript Mastery</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Anya Sharma</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-10-25</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Emily Clark</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Cloud Computing Fundamentals</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dr. Ken Thompson</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-10-24</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">David Lee</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">UX/UI Design Principles</td>
                    <td class="px-6 py-4 whitespace-nowSave">Jane Doe</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-10-24</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Sophia Chen</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Introduction to Python</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Anya Sharma</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-10-23</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>




<?php include_once('../include/footer.php'); ?>