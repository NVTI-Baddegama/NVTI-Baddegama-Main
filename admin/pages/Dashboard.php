<?php
// Ensure header is included first and session started if needed
include_once('../include/header.php'); // header.php should handle session_start()

// --- Database Connection ---
include_once('../../include/connection.php');

// --- 1. Fetch Statistics ---
$total_courses = $con->query("SELECT COUNT(*) as count FROM course")->fetch_assoc()['count'];
$total_instructors = $con->query("SELECT COUNT(*) as count FROM staff WHERE position = 'Instructors'")->fetch_assoc()['count'];
$total_non_academic = $con->query("SELECT COUNT(*) as count FROM staff WHERE position = 'Non-Academic Staff'")->fetch_assoc()['count'];
$total_enrolled = $con->query("SELECT COUNT(*) as count FROM student_enrollments")->fetch_assoc()['count'];

// --- 2. Fetch Data for "Recent Activity" ---
$latest_student_result = $con->query("SELECT full_name, application_date FROM student_enrollments ORDER BY application_date DESC LIMIT 1");
$latest_student = $latest_student_result->fetch_assoc();
$latest_course_result = $con->query("SELECT course_name, id FROM course ORDER BY id DESC LIMIT 1");
$latest_course = $latest_course_result->fetch_assoc();
$latest_staff_result = $con->query("SELECT first_name, last_name, position, id FROM staff ORDER BY id DESC LIMIT 1");
$latest_staff = $latest_staff_result->fetch_assoc();

// --- 3. Fetch Data for "Latest Enrollments" Table ---
$enrollments = [];
$enrollment_query = "SELECT Student_id, full_name, nic, course_option_one, application_date, is_processed
                     FROM student_enrollments
                     ORDER BY application_date DESC
                     LIMIT 5";
$enrollment_result = $con->query($enrollment_query);
if ($enrollment_result && $enrollment_result->num_rows > 0) {
    while ($row = $enrollment_result->fetch_assoc()) {
        $enrollments[] = $row;
    }
}

// --- NEW: 4. Fetch Data for Enrollment Chart (Last 7 Days) ---
$chart_data = [];
$chart_labels = [];
$enrollment_chart_query = "SELECT DATE(application_date) as enrollment_date, COUNT(*) as count
                           FROM student_enrollments
                           WHERE application_date >= CURDATE() - INTERVAL 7 DAY
                           GROUP BY DATE(application_date)
                           ORDER BY enrollment_date ASC";
$chart_result = $con->query($enrollment_chart_query);
if ($chart_result) {
    while ($row = $chart_result->fetch_assoc()) {
        $chart_labels[] = date('M j', strtotime($row['enrollment_date'])); // Format date like "Oct 24"
        $chart_data[] = (int)$row['count'];
    }
}
// Convert PHP arrays to JSON for JavaScript
$chart_labels_json = json_encode($chart_labels);
$chart_data_json = json_encode($chart_data);
// --- END NEW ---

?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Overview</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Courses</p>
                <p class="text-4xl font-extrabold text-gray-900 mt-1"><?php echo $total_courses; ?></p>
            </div>
            <div class="bg-green-100 p-3 rounded-full text-green-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM21 21L3 21a2 2 0 01-2-2V5a2 2 0 012-2h18a2 2 0 012 2v14a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>
        <p class="text-sm text-gray-400 mt-3">Currently active courses</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Instructors</p>
                <p class="text-4xl font-extrabold text-gray-900 mt-1"><?php echo $total_instructors; ?></p>
            </div>
            <div class="bg-indigo-100 p-3 rounded-full text-indigo-600">
               <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm-6-4a6 6 0 00-4 0v1h8v-1m-4 0v-2"></path></svg>
            </div>
        </div>
        <p class="text-sm text-gray-400 mt-3">Total registered instructors</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-teal-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Non-Academic Staff</p>
                <p class="text-4xl font-extrabold text-gray-900 mt-1"><?php echo $total_non_academic; ?></p>
            </div>
            <div class="bg-teal-100 p-3 rounded-full text-teal-600">
                 <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.505-9-1.745M16 4V2M8 4V2m4 8a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zM5 16s2.5-3 7-3 7 3 7 3M5 16H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2v6a2 2 0 01-2 2h-2"></path></svg>
            </div>
        </div>
        <p class="text-sm text-gray-400 mt-3">Total registered support staff</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-amber-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Enrolled</p>
                <p class="text-4xl font-extrabold text-gray-900 mt-1"><?php echo $total_enrolled; ?></p>
            </div>
            <div class="bg-amber-100 p-3 rounded-full text-amber-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM12 18H8a2 2 0 00-2 2v1"></path></svg>
            </div>
        </div>
        <p class="text-sm text-gray-400 mt-3">Total student applications</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Student Enrollments (Last 7 Days)</h3>
        <div class="h-80">
            <canvas id="enrollmentChart"></canvas>
        </div>
    </div>
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Quick Actions</h3>
             <div class="space-y-3">
                 <a href="add_course.php" class="w-full flex items-center justify-center p-3 text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition duration-150 shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Add New Course
                 </a>
                 <a href="manage_staff.php" class="w-full flex items-center justify-center p-3 text-sm font-medium rounded-lg text-green-700 border border-green-600 hover:bg-green-50 transition duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5m-5 0a2 2 0 100-4m0 4a2 2 0 110-4m-9-2h.01M7 12h.01M7 16h.01M4 20h4l-2 2h-2zm-3-4a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H3a2 2 0 01-2-2v-2z"></path></svg>
                    Manage Staff
                 </a>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Recent Activity</h3>
             <ul class="space-y-4">
                <?php if ($latest_student): ?>
                <li class="flex items-start">
                    <div class="w-2 h-2 rounded-full bg-blue-500 mt-2 mr-3 flex-shrink-0"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">New Student Registered</p>
                        <p class="text-xs text-gray-500">
                            <?php echo htmlspecialchars($latest_student['full_name']); ?> applied on <?php echo date('M j, Y', strtotime($latest_student['application_date'])); ?>.
                        </p>
                    </div>
                </li>
                 <?php endif; ?>
                 <?php if ($latest_course): ?>
                <li class="flex items-start">
                    <div class="w-2 h-2 rounded-full bg-green-500 mt-2 mr-3 flex-shrink-0"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">New Course Added</p>
                        <p class="text-xs text-gray-500">
                           Course "<?php echo htmlspecialchars($latest_course['course_name']); ?>" was recently added.
                        </p>
                    </div>
                </li>
                <?php endif; ?>
                <?php if ($latest_staff): ?>
                <li class="flex items-start">
                    <div class="w-2 h-2 rounded-full bg-indigo-500 mt-2 mr-3 flex-shrink-0"></div>
                     <div>
                        <p class="text-sm font-medium text-gray-800">New Staff Member</p>
                        <p class="text-xs text-gray-500">
                            <?php echo htmlspecialchars($latest_staff['first_name'] . ' ' . $latest_staff['last_name']); ?> (<?php echo htmlspecialchars($latest_staff['position']); ?>) was recently registered.
                        </p>
                    </div>
                </li>
                 <?php endif; ?>
                 <?php if (!$latest_student && !$latest_course && !$latest_staff): ?>
                      <li class="text-sm text-gray-500">No recent activity found.</li>
                 <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg mt-8">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Latest Enrollments (Top 5)</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Choice 1</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIC</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                 <?php if (!empty($enrollments)): ?>
                    <?php foreach ($enrollments as $student): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($student['course_option_one']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($student['nic']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('Y-m-d', strtotime($student['application_date'])); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($student['is_processed'] == 1): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Processed</span>
                            <?php else: ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            <?php endif; ?>
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="view_student_details.php?nic=<?php echo htmlspecialchars($student['nic']); ?>" class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">No recent enrollments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php
// Close connection
if (isset($con)) {
    $con->close();
}
// Include footer AFTER the chart script
include_once('../include/footer.php');
?>

<script>
    // Wait for the DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', () => {
        // Get the canvas element
        const ctx = document.getElementById('enrollmentChart');

        // Check if the canvas element exists and Chart.js is loaded
        if (ctx && typeof Chart !== 'undefined') {
            // Get the data from PHP (converted to JSON)
            const chartLabels = <?php echo $chart_labels_json; ?>;
            const chartData = <?php echo $chart_data_json; ?>;

            // Create the chart
            new Chart(ctx, {
                type: 'line', // Type of chart (line, bar, pie etc.)
                data: {
                    labels: chartLabels, // X-axis labels (Dates)
                    datasets: [{
                        label: 'Enrollments', // Legend label
                        data: chartData,      // Y-axis data (Counts)
                        fill: true,          // Fill area under the line
                        borderColor: 'rgb(79, 70, 229)', // Line color (Indigo)
                        backgroundColor: 'rgba(79, 70, 229, 0.1)', // Fill color
                        tension: 0.1         // Line curve (0 for straight lines)
                    }]
                },
                options: {
                    responsive: true, // Make it responsive
                    maintainAspectRatio: false, // Don't maintain aspect ratio to fill container height
                    scales: {
                        y: {
                            beginAtZero: true, // Start Y-axis at 0
                             ticks: {
                                // Ensure only whole numbers are shown on Y-axis
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true // Show legend
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        },
                    }
                }
            });
        } else {
            // Log an error if canvas or Chart.js is missing
            console.error("Could not find canvas element with ID 'enrollmentChart' or Chart.js library is not loaded.");
            // Optionally display a message to the user in the chart area
             if(ctx) {
                 ctx.getContext('2d').fillText("Chart could not be loaded.", 10, 50);
             }
        }
    });
</script>