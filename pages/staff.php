<?php
include_once '../include/header.php';
require '../include/connection.php';

$sql = "SELECT id,service_id, first_name, last_name, position, profile_photo 
            FROM staff 
            WHERE status = 'active'
            ORDER BY 
              CASE 
                WHEN position = 'Deputy Director' THEN 1
                WHEN position = 'Assistant Director' THEN 2
                WHEN position = 'Training Officer' THEN 3
                WHEN position = 'Finance Officer' THEN 4
                WHEN position = 'Testing Officer' THEN 5
                WHEN position = 'Program Officer' THEN 6
                WHEN position = 'Senior Instructor' THEN 7
                WHEN service_id ='1119' THEN 8
                WHEN position = 'Instructor' THEN 9
                WHEN position = 'Management Assistant' THEN 10
                WHEN position = 'Driver' THEN 11
                WHEN position = 'Labor' THEN 12
                ELSE 13
              END ASC, 
              first_name ASC, 
              last_name ASC";

$result = $con->query($sql);

// Image Path
$photo_upload_path = '../uploads/profile_photos/';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Staff - NVTI Baddegama</title>
    <link href="../path/to/your/tailwind.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-bold text-center mb-12 text-gray-800">Our Staff</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">

            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>

                    <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden w-full max-w-xs mx-auto">
                        <?php if (!empty($row['profile_photo'])): ?>
                            <div class="h-[280px] w-full overflow-hidden">
                                <img src="<?php echo htmlspecialchars($photo_upload_path . $row['profile_photo']); ?>"
                                     alt="Profile of <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                     class="w-full h-full object-cover" />
                            </div>
                        <?php else: ?>
                            <div
                                class="w-full h-[280px] bg-gray-100 text-gray-600 flex items-center justify-center text-4xl font-semibold">
                                <?php echo htmlspecialchars(strtoupper(substr($row['first_name'], 0, 1))); ?>
                            </div>
                        <?php endif; ?>
                    
                        <div class="p-5 text-center">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                <?php echo htmlspecialchars($row['position']); ?>
                            </p>
                    
                            <div class="mt-4">
                                <button type="button"
                                        data-id="<?php echo htmlspecialchars($row['id']); ?>"
                                        class="open-modal-btn px-4 py-2 rounded-md text-white text-sm font-medium transition"
                                        style="background-color: #7A1418;"
                                        aria-label="View profile of <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>">
                                    View Profile
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php
                }
            } else {
                echo "<p class='text-center col-span-full'>No staff members found.</p>";
            }
            $con->close();
            ?>
        </div>
    </div>

    <!-- Fixed Modal with proper mobile scroll -->
    <div id="staffModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 hidden overflow-y-auto p-4">

        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full my-8 overflow-hidden" id="modalContent">

            <div class="flex justify-between items-center border-b p-4 sticky top-0 bg-white z-10">
                <h2 class="text-2xl font-bold text-gray-800">Staff Member Profile</h2>
                <button type="button" id="modalCloseBtn"
                    class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>

            <div id="modalBody" class="p-6 overflow-y-auto" style="max-height: calc(90vh - 120px);">

                <div id="modalLoader" class="text-center w-full py-20">
                    <p>Loading details...</p>
                </div>

                <div id="modalDetails" class="md:flex hidden">

                    <div class="md:w-1/3 p-4 flex-shrink-0 flex justify-center md:justify-start">
                        <img id="modalImage" class="w-48 h-48 rounded-lg object-cover shadow-md hidden" src=""
                            alt="Profile Photo">
                        <div id="modalAvatar"
                            class="w-48 h-48 rounded-lg bg-gray-300 flex items-center justify-center shadow-md hidden">
                            <span id="modalAvatarLetter" class="text-gray-700 text-7xl font-bold"></span>
                        </div>
                    </div>

                    <div class="md:w-2/3 p-4">
                        <h1 id="modalName" class="text-3xl font-bold text-gray-900"></h1>
                        <p id="modalPosition" class="text-xl text-blue-600 font-semibold mb-4"></p>
                        <hr class="my-4">
                        <h3 class="text-lg font-semibold mb-2">Details</h3>
                        <p class="mb-2"><strong>Email:</strong> <a id="modalEmail" href="" class="text-blue-500"></a>
                        </p>
                        <p class="mb-2"><strong>Contact No:</strong> <span id="modalContact"></span></p>
                        <p class="mb-2"><strong>Gender:</strong> <span id="modalGender"></span></p>
                        <p class="mb-2" id="modalCourseRow" style="display:none;"><strong>Assigned Course:</strong> <span id="modalCourse"></span></p>
                        
                        <!-- Assigned Courses Section for Instructors -->
                        <div id="modalAssignedCoursesSection" style="display:none;">
                            <hr class="my-4">
                            <h3 class="text-lg font-semibold mb-3">Assigned Courses</h3>
                            <div id="modalAssignedCourses" class="space-y-2">
                                <!-- Courses will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const JS_PHOTO_PATH = '<?php echo $photo_upload_path; ?>';

        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('staffModal');
            const closeBtn = document.getElementById('modalCloseBtn');
            const modalLoader = document.getElementById('modalLoader');
            const modalDetails = document.getElementById('modalDetails');
            const modalImage = document.getElementById('modalImage');
            const modalAvatar = document.getElementById('modalAvatar');
            const modalAvatarLetter = document.getElementById('modalAvatarLetter');
            const modalName = document.getElementById('modalName');
            const modalPosition = document.getElementById('modalPosition');
            const modalContact = document.getElementById('modalContact');
            const modalGender = document.getElementById('modalGender');
            const modalCourse = document.getElementById('modalCourse');
            const modalEmail = document.getElementById('modalEmail');
            const viewButtons = document.querySelectorAll('.open-modal-btn');

            viewButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const staffId = button.dataset.id;
                    modal.classList.remove('hidden');
                    modalDetails.classList.add('hidden');
                    modalLoader.classList.remove('hidden');
                    
                    // Prevent body scroll when modal is open
                    document.body.style.overflow = 'hidden';

                    fetch(`get_staff_details.php?id=${staffId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) { throw new Error(data.error); }

                            if (data.profile_photo) {
                                modalImage.src = JS_PHOTO_PATH + data.profile_photo;
                                modalImage.classList.remove('hidden');
                                modalAvatar.classList.add('hidden');
                            } else {
                                const firstLetter = (data.first_name || '?').charAt(0).toUpperCase();
                                modalAvatarLetter.textContent = firstLetter;
                                modalAvatar.classList.remove('hidden');
                                modalImage.classList.add('hidden');
                            }

                            modalName.textContent = data.full_name || 'N/A';
                            modalPosition.textContent = data.position || 'N/A';
                            modalEmail.textContent = data.email || 'N/A';
                            modalEmail.href = `mailto:${data.email || ''}`;
                            modalContact.textContent = data.contact_no || 'N/A';
                            modalGender.textContent = data.gender || 'N/A';
                            
                            // Handle old single course display
                            if (data.course_no && data.course_no.trim() !== '') {
                                modalCourse.textContent = data.course_no;
                                document.getElementById('modalCourseRow').style.display = '';
                            } else {
                                document.getElementById('modalCourseRow').style.display = 'none';
                            }

                            // Handle assigned courses for Instructors and Senior Instructors
                            const assignedCoursesSection = document.getElementById('modalAssignedCoursesSection');
                            const assignedCoursesContainer = document.getElementById('modalAssignedCourses');
                            
                            if (data.assigned_courses && data.assigned_courses.length > 0) {
                                assignedCoursesSection.style.display = 'block';
                                assignedCoursesContainer.innerHTML = '';
                                
                                data.assigned_courses.forEach(course => {
                                    const courseDiv = document.createElement('div');
                                    courseDiv.className = 'bg-blue-50 border-l-4 border-blue-500 p-3 rounded';
                                    
                                    const statusBadge = course.status === 'active' 
                                        ? '<span class="inline-block px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full ml-2">Active</span>'
                                        : '<span class="inline-block px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full ml-2">Inactive</span>';
                                    
                                    courseDiv.innerHTML = `
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-semibold text-gray-800">${course.course_name}</p>
                                                <p class="text-sm text-gray-600">Course No: ${course.course_no}</p>
                                                <p class="text-xs text-gray-500 mt-1">Assigned: ${new Date(course.assigned_date).toLocaleDateString()}</p>
                                            </div>
                                            <div>
                                                ${statusBadge}
                                            </div>
                                        </div>
                                    `;
                                    
                                    assignedCoursesContainer.appendChild(courseDiv);
                                });
                            } else if ((data.position === 'Instructor' || data.position === 'Senior Instructor')) {
                                assignedCoursesSection.style.display = 'block';
                                assignedCoursesContainer.innerHTML = '<p class="text-gray-500 text-sm italic">No courses assigned yet.</p>';
                            } else {
                                assignedCoursesSection.style.display = 'none';
                            }

                            modalLoader.classList.add('hidden');
                            modalDetails.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error('Error fetching staff details:', error);
                            modalLoader.innerHTML = `<p class="text-red-500">Error: ${error.message}</p>`;
                        });
                });
            });

            function closeModal() {
                modal.classList.add('hidden');
                modalLoader.innerHTML = '<p>Loading details...</p>';
                modalImage.classList.add('hidden');
                modalAvatar.classList.add('hidden');
                
                // Re-enable body scroll when modal is closed
                document.body.style.overflow = '';
            }

            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) { closeModal(); }
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        });
    </script>

</body>

</html>

<?php include_once '../include/footer.php'; ?>