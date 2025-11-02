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
                ELSE 13 -- This puts any other positions at the end
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

                    <div
                        class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 flex flex-col items-center p-6">
                        <div class="mb-4">
                            <?php if (!empty($row['profile_photo'])): ?>
                                <img src="<?php echo htmlspecialchars($photo_upload_path . $row['profile_photo']); ?>"
                                    alt="Profile of <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                    class="w-28 h-28 object-cover border-2 border-blue-400 shadow rounded-md" />
                            <?php else: ?>
                                <div
                                    class="w-28 h-28 rounded-md bg-gray-200 text-gray-600 flex items-center justify-center text-3xl font-semibold border shadow-inner">
                                    <?php echo htmlspecialchars(strtoupper(substr($row['first_name'], 0, 1))); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="text-center mt-2">
                            <h3 class="text-xl font-semibold text-gray-800">
                                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                <?php echo htmlspecialchars($row['position']); ?>
                            </p>
                        </div>
                        <div class="mt-4">
                            <button type="button" data-id="<?php echo htmlspecialchars($row['id']); ?>"
                                class="open-modal-btn px-3 py-1.5 rounded text-white text-sm transition"
                                style="background-color: #7A1418;"
                                aria-label="View profile of <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>">
                                View Profile
                            </button>
                        </div>
                    </div>
                    <?php
                } // While loop ends
            } else {
                echo "<p class='text-center col-span-full'>No staff members found.</p>";
            }
            $con->close();
            ?>
        </div>
    </div>

    <div id="staffModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 hidden">

        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full m-4 overflow-hidden" id="modalContent">

            <div class="flex justify-between items-center border-b p-4">
                <h2 class="text-2xl font-bold text-gray-800">Staff Member Profile</h2>
                <button type="button" id="modalCloseBtn"
                    class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>

            <div id="modalBody" class="p-6">

                <div id="modalLoader" class="text-center w-full py-20">
                    <p>Loading details...</p>
                </div>

                <div id="modalDetails" class="md:flex hidden">

                    <div class="md:w-1/3 p-4 flex-shrink-0 flex items-center justify-center">
                        <img id="modalImage" class="**w-48 h-48** rounded-lg object-cover shadow-md hidden" src=""
                            alt="Profile Photo">
                        <div id="modalAvatar"
                            class="**w-48 h-48** rounded-lg bg-gray-300 flex items-center justify-center shadow-md hidden">
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
                        <p class="mb-2"><strong>Assigned Course:</strong> <span id="modalCourse"></span></p>
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
                            modalCourse.textContent = data.course_no || 'N/A';

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