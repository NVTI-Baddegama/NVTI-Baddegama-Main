<?php 
// Add session_start() at the very top to handle success/error messages
session_start(); 
include_once('../include/header.php'); 
include_once('../../include/connection.php');

// 1. Check if NIC is provided in the URL
if (!isset($_GET['nic']) || empty($_GET['nic'])) {
    echo "<div class='p-6'><p class='text-red-500'>Error: No student NIC provided.</p></div>";
    include_once('../include/footer.php');
    exit();
}

// 2. Get the NIC and fetch data
$nic = trim($_GET['nic']);

$query = "SELECT * FROM student_enrollments WHERE nic = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $nic);
$stmt->execute();
$result = $stmt->get_result();

// 3. Check if a student was found
if ($result->num_rows == 1) {
    $student = $result->fetch_assoc();
} else {
    echo "<div class='p-6'><p class='text-red-500'>Error: No student found with NIC: " . htmlspecialchars($nic) . "</p></div>";
    include_once('../include/footer.php');
    exit();
}
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Student Application Details</h2>
<div class="mb-6">
    <a href="manage_students.php"
       class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 shadow-md transition duration-150">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Back Student Application List
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
        
        <div class="border-b pb-4 mb-4">
            <h3 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($student['full_name']); ?></h3>
            <p class="text-sm text-gray-500">Student ID: <?php echo htmlspecialchars($student['Student_id']); ?></p>
        </div>

        <div class="space-y-4">
            
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Personal Details</h4>
                <div class="mt-2 text-lg text-gray-800 space-y-1">
                    <p><strong>NIC:</strong> <?php echo htmlspecialchars($student['nic']); ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($student['dob']); ?></p>
                    <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($student['address'])); ?></p>
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Contact Details</h4>
                <div class="mt-2 text-lg text-gray-800 space-y-1">
                    <p><strong>Contact No:</strong> <?php echo htmlspecialchars($student['contact_no']); ?></p>
                    <p><strong>WhatsApp No:</strong> <?php echo htmlspecialchars($student['whatsapp_no'] ?? 'N/A'); ?></p>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Academic Details</h4>
                <div class="mt-2 text-lg text-gray-800 space-y-1">
                    <p><strong>O/L Passed:</strong> <?php echo htmlspecialchars($student['ol_pass_status']); ?></p>
                    <p><strong>O/L Grades:</strong> 
                        Eng: <strong><?php echo htmlspecialchars($student['ol_english_grade'] ?? '-'); ?></strong> | 
                        Maths: <strong><?php echo htmlspecialchars($student['ol_maths_grade'] ?? '-'); ?></strong> | 
                        Science: <strong><?php echo htmlspecialchars($student['ol_science_grade'] ?? '-'); ?></strong>
                    </p>
                    <p><strong>A/L Stream:</strong> <?php echo htmlspecialchars($student['al_category']); ?></p>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Course Choices</h4>
                <div class="mt-2 text-lg text-gray-800 space-y-1">
                    <p><strong>Choice 1:</strong> <?php echo htmlspecialchars($student['course_option_one']); ?></p>
                    <p><strong>Choice 2:</strong> <?php echo htmlspecialchars($student['course_option_two'] ?? 'N/A'); ?></p>
                </div>
            </div>

        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-xl shadow-lg sticky top-28">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Status & Actions</h3>
            
            <div class="mb-6">
                <p class="text-sm font-medium text-gray-500">Application Date</p>
                <p class="text-lg font-semibold text-gray-900"><?php echo date('F j, Y, g:i a', strtotime($student['application_date'])); ?></p>
            </div>

            <div class="mb-6">
                <p class="text-sm font-medium text-gray-500">Current Status</p>
                <?php if ($student['is_processed'] == 1): ?>
                    <p class="text-lg font-bold text-green-600">PROCESSED</p>
                <?php else: ?>
                    <p class="text-lg font-bold text-yellow-600">PENDING</p>
                <?php endif; ?>
            </div>

            <div class="space-y-3">
                
                <?php if ($student['is_processed'] == 0): // Only show "Mark as Processed" if it is still PENDING ?>
                <a href="../lib/student_action_handler.php?action=process&nic=<?php echo htmlspecialchars($student['nic']); ?>" 
                   class="block w-full text-center p-3 font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition duration-150">
                    Mark as Processed
                </a>
                <?php endif; ?>

                <a href="../lib/student_action_handler.php?action=delete&nic=<?php echo htmlspecialchars($student['nic']); ?>" 
                   class="block w-full text-center p-3 font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition duration-150"
                   onclick="return confirm('Are you sure you want to permanently delete this application? This action cannot be undone.');">
                    Delete Application
                </a>
            </div>
            
        </div>
    </div>

</div>

<?php 
// Unset messages after displaying them
if(isset($_SESSION['success_msg'])) unset($_SESSION['success_msg']);
if(isset($_SESSION['error_msg'])) unset($_SESSION['error_msg']);

include_once('../include/footer.php'); 
?>