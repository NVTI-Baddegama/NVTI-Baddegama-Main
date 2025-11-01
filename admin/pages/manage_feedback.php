<?php
include_once('../include/header.php');
include('../../include/connection.php'); // Uses $con

// Helper function to render stars
if (!function_exists('render_stars')) {
    function render_stars($rating) {
        $output = '<div class="flex items-center space-x-1 text-accent-yellow">';
        $star_svg = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.445a1 1 0 00-.364 1.118l1.287 3.958c.3.921-.755 1.688-1.54 1.118l-3.367-2.445a1 1 0 00-1.175 0l-3.367 2.445c-.784.57-1.838-.197-1.54-1.118l1.287-3.958a1 1 0 00-.364-1.118L2.28 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.958z"></path></svg>';
        $empty_star_svg = '<svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.445a1 1 0 00-.364 1.118l1.287 3.958c.3.921-.755 1.688-1.54 1.118l-3.367-2.445a1 1 0 00-1.175 0l-3.367 2.445c-.784.57-1.838-.197-1.54-1.118l1.287-3.958a1 1 0 00-.364-1.118L2.28 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.958z"></path></svg>';
        for ($i = 1; $i <= 5; $i++) { $output .= ($i <= $rating) ? $star_svg : $empty_star_svg; }
        $output .= '</div>';
        return $output;
    }
}

// Fetch ALL feedback from the database
$feedbacks = [];
if ($con && !$con->connect_error) {
    $sql = "SELECT id, user_name, feedback, rating, approve FROM feedback ORDER BY id DESC";
    $result = $con->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $feedbacks[] = $row;
        }
    }
} else {
    $db_error = "Error connecting to database.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedbacks</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#1D4ED8',
                        'secondary-gray': '#4B5563',
                        'accent-yellow': '#f59e0b',
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">
    
    <main class="container mx-auto p-4 md:p-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-primary-blue mb-8">
            Manage Feedbacks
        </h1>

        <?php if (isset($db_error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p><?php echo $db_error; ?></p>
            </div>
        <?php elseif (empty($feedbacks)): ?>
            <div class="bg-blue-50 border-l-4 border-primary-blue text-primary-blue p-4" role="alert">
                <p>No feedbacks have been submitted yet.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <?php foreach ($feedbacks as $fb):
                    // --- Styles for Toggle Button ---
                    $is_approved = $fb['approve'] == 1;
                    $card_style = $is_approved ? 'bg-white border-green-300' : 'bg-yellow-50 border-yellow-400';
                    $button_style = $is_approved ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-600 hover:bg-green-700';
                    $button_text = $is_approved ? 'Unapprove' : 'Approve';
                    $new_status_to_set = $is_approved ? 0 : 1;
                ?>
                
                <div id="card-<?php echo $fb['id']; ?>" class="rounded-lg shadow-md p-5 border-t-4 <?php echo $card_style; ?> transition-all duration-300 opacity-100">
                    
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-bold text-gray-800">
                            <?php echo htmlspecialchars($fb['user_name']); ?>
                        </h3>
                        
                        <div class="flex space-x-2">
                            <button 
                                class="toggle-btn text-white px-3 py-1 rounded-md text-sm font-medium <?php echo $button_style; ?> transition-colors duration-300"
                                data-id="<?php echo $fb['id']; ?>"
                                data-new-status="<?php echo $new_status_to_set; ?>"
                            >
                                <?php echo $button_text; ?>
                            </button>
                            
                            <button 
                                class="delete-btn text-white px-3 py-1 rounded-md text-sm font-medium bg-red-600 hover:bg-red-700 transition-colors duration-300"
                                data-id="<?php echo $fb['id']; ?>"
                                data-name="<?php echo htmlspecialchars($fb['user_name']); ?>"
                            >
                                Delete
                            </button>
                        </div>
                    </div>

                    <blockquote class="text-gray-700 italic border-l-4 border-gray-200 pl-4 my-4">
                        <p>"<?php echo htmlspecialchars($fb['feedback']); ?>"</p>
                    </blockquote>

                    <div class="mt-4">
                        <?php echo render_stars($fb['rating']); ?>
                    </div>
                </div>
                
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <div id="customAlertDialog" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black bg-opacity-50 transition-opacity duration-300">
        <div id="customAlertContent" class="bg-white rounded-lg shadow-xl w-full max-w-sm transform transition-all scale-95 opacity-0">
            <div id="alertHeader" class="flex items-center justify-between p-4 border-b border-gray-200 rounded-t-lg bg-red-100">
                <h3 id="alertTitle" class="text-xl font-semibold text-red-700">Error</h3>
                <button id="alertCloseBtn" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <p id="alertMessage" class="text-base leading-relaxed text-gray-600">Message...</p>
            </div>
            <div class="flex items-center justify-end p-4 space-x-2 border-t border-gray-200 rounded-b-lg">
                <button id="alertOkBtn" type="button" class="text-white bg-primary-blue hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    OK
                </button>
            </div>
        </div>
    </div>

    <div id="customConfirmDialog" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black bg-opacity-50 transition-opacity duration-300">
        <div id="confirmContent" class="bg-white rounded-lg shadow-xl w-full max-w-sm transform transition-all scale-95 opacity-0">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 rounded-t-lg bg-red-50">
                <h3 class="text-xl font-semibold text-red-700">Confirm Deletion</h3>
            </div>
            <div class="p-6">
                <p id="confirmMessage" class="text-base leading-relaxed text-gray-600">Message...</p>
            </div>
            <div class="flex items-center justify-end p-4 space-x-2 border-t border-gray-200 rounded-b-lg">
                <button id="confirmCancelBtn" type="button" class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900">
                    Cancel
                </button>
                <button id="confirmOkBtn" type="button" class="text-white bg-red-600 hover:bg-red-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        
        // --- DIALOG 1: ALERT (Error) ---
        const alertDialog = document.getElementById('customAlertDialog');
        const alertContent = document.getElementById('customAlertContent');
        const alertMessage = document.getElementById('alertMessage');
        const alertCloseBtn = document.getElementById('alertCloseBtn');
        const alertOkBtn = document.getElementById('alertOkBtn');

        const hideCustomAlert = () => {
            alertContent.classList.add('scale-95', 'opacity-0');
            alertDialog.classList.remove('bg-opacity-50');
            setTimeout(() => { alertDialog.classList.add('hidden'); }, 300);
        };
        const showCustomAlert = (message) => {
            alertMessage.textContent = message;
            alertDialog.classList.remove('hidden');
            setTimeout(() => {
                alertDialog.classList.add('bg-opacity-50');
                alertContent.classList.remove('scale-95', 'opacity-0');
            }, 10);
        };
        alertCloseBtn.addEventListener('click', hideCustomAlert);
        alertOkBtn.addEventListener('click', hideCustomAlert);

        // --- DIALOG 2: CONFIRM (Delete) ---
        const confirmDialog = document.getElementById('customConfirmDialog');
        const confirmContent = document.getElementById('confirmContent');
        const confirmMessage = document.getElementById('confirmMessage');
        const confirmOkBtn = document.getElementById('confirmOkBtn');
        const confirmCancelBtn = document.getElementById('confirmCancelBtn');
        let currentDeleteId = null; // Store the ID of the item to be deleted

        const hideConfirmDialog = () => {
            confirmContent.classList.add('scale-95', 'opacity-0');
            confirmDialog.classList.remove('bg-opacity-50');
            setTimeout(() => {
                confirmDialog.classList.add('hidden');
                currentDeleteId = null;
            }, 300);
        };
        const showConfirmDialog = (id, name) => {
            currentDeleteId = id;
            confirmMessage.textContent = `Are you sure you want to delete the feedback from ${name}? This action cannot be undone.`;
            confirmDialog.classList.remove('hidden');
            setTimeout(() => {
                confirmDialog.classList.add('bg-opacity-50');
                confirmContent.classList.remove('scale-95', 'opacity-0');
            }, 10);
        };
        confirmCancelBtn.addEventListener('click', hideConfirmDialog);
        confirmDialog.addEventListener('click', (e) => {
            if (e.target === confirmDialog) hideConfirmDialog();
        });


        // --- LOGIC 1: TOGGLE APPROVAL ---
        const allToggleButtons = document.querySelectorAll('.toggle-btn');
        allToggleButtons.forEach(button => {
            button.addEventListener('click', () => {
                const feedbackId = button.dataset.id;
                const newStatus = button.dataset.newStatus;
                
                button.disabled = true;
                button.textContent = '...';

                fetch('../lib/toggle_feedback.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${feedbackId}&new_status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const card = document.getElementById(`card-${feedbackId}`);
                        if (newStatus == 1) { // --- Just APPROVED ---
                            card.classList.remove('bg-yellow-50', 'border-yellow-400');
                            card.classList.add('bg-white', 'border-green-300');
                            button.classList.remove('bg-green-600', 'hover:bg-green-700');
                            button.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                            button.textContent = 'Unapprove';
                            button.dataset.newStatus = 0;
                        } else { // --- Just UNAPPROVED ---
                            card.classList.remove('bg-white', 'border-green-300');
                            card.classList.add('bg-yellow-50', 'border-yellow-400');
                            button.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                            button.classList.add('bg-green-600', 'hover:bg-green-700');
                            button.textContent = 'Approve';
                            button.dataset.newStatus = 1;
                        }
                    } else {
                        showCustomAlert('Error: ' + data.message);
                        button.textContent = (newStatus == 1) ? 'Approve' : 'Unapprove'; 
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    showCustomAlert('A network or server error occurred. Please check the console.');
                })
                .finally(() => {
                    button.disabled = false;
                });
            });
        });

        // --- LOGIC 2: DELETE FEEDBACK (Trigger) ---
        const allDeleteButtons = document.querySelectorAll('.delete-btn');
        allDeleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                const feedbackId = button.dataset.id;
                const feedbackName = button.dataset.name;
                showConfirmDialog(feedbackId, feedbackName);
            });
        });

        // --- LOGIC 3: DELETE FEEDBACK (Confirm) ---
        confirmOkBtn.addEventListener('click', () => {
            if (!currentDeleteId) return;
            
            const feedbackId = currentDeleteId;
            const deleteButton = document.querySelector(`.delete-btn[data-id="${feedbackId}"]`);
            
            confirmOkBtn.disabled = true;
            confirmOkBtn.textContent = 'Deleting...';
            if (deleteButton) deleteButton.disabled = true;

            fetch('../lib/delete_feedback.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${feedbackId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById(`card-${feedbackId}`);
                    if (card) {
                        card.classList.add('opacity-0', 'scale-90');
                        setTimeout(() => { card.remove(); }, 300);
                    }
                    hideConfirmDialog();
                } else {
                    showCustomAlert('Error: ' + data.message);
                    if (deleteButton) deleteButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                showCustomAlert('A network or server error occurred. Please check the console.');
                if (deleteButton) deleteButton.disabled = false;
            })
            .finally(() => {
                confirmOkBtn.disabled = false;
                confirmOkBtn.textContent = 'Delete';
                hideConfirmDialog(); // Ensure dialog is hidden
            });
        });

    });
    </script>
    <?php include_once('../include/footer.php'); ?>
</body>
</html>