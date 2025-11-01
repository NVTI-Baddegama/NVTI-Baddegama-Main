<?php
include_once('../include/header.php');
include('../../include/connection.php');

// Helper function to render stars (copied from your index page)
if (!function_exists('render_stars')) {
    function render_stars($rating) {
        $output = '<div class="flex items-center space-x-1 text-accent-yellow">';
        $star_svg = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.445a1 1 0 00-.364 1.118l1.287 3.958c.3.921-.755 1.688-1.54 1.118l-3.367-2.445a1 1 0 00-1.175 0l-3.367 2.445c-.784.57-1.838-.197-1.54-1.118l1.287-3.958a1 1 0 00-.364-1.118L2.28 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.958z"></path></svg>';
        $empty_star_svg = '<svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.445a1 1 0 00-.364 1.118l1.287 3.958c.3.921-.755 1.688-1.54 1.118l-3.367-2.445a1 1 0 00-1.175 0l-3.367 2.445c-.784.57-1.838-.197-1.54-1.118l1.287-3.958a1 1 0 00-.364-1.118L2.28 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.958z"></path></svg>';
        
        for ($i = 1; $i <= 5; $i++) {
            $output .= ($i <= $rating) ? $star_svg : $empty_star_svg;
        }
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
    // Handle connection error
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
        // Use your project's Tailwind config
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
                    $is_approved = $fb['approve'] == 1;

                    // --- Dynamic styles based on approval status ---
                    // Card style
                    $card_style = $is_approved 
                        ? 'bg-white border-green-300' 
                        : 'bg-yellow-50 border-yellow-400';
                    
                    // Button style
                    $button_style = $is_approved 
                        ? 'bg-yellow-500 hover:bg-yellow-600' 
                        : 'bg-green-600 hover:bg-green-700';
                    
                    // Button text
                    $button_text = $is_approved ? 'Unapprove' : 'Approve';
                    
                    // The NEW status we will send on click
                    $new_status_to_set = $is_approved ? 0 : 1;
                ?>
                
                <div id="card-<?php echo $fb['id']; ?>" class="rounded-lg shadow-md p-5 border-t-4 <?php echo $card_style; ?> transition-all duration-300">
                    
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-bold text-gray-800">
                            <?php echo htmlspecialchars($fb['user_name']); ?>
                        </h3>
                        
                        <button 
                            class="toggle-btn text-white px-3 py-1 rounded-md text-sm font-medium <?php echo $button_style; ?> transition-colors duration-300"
                            data-id="<?php echo $fb['id']; ?>"
                            data-new-status="<?php echo $new_status_to_set; ?>"
                        >
                            <?php echo $button_text; ?>
                        </button>
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

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Find all toggle buttons
        const allToggleButtons = document.querySelectorAll('.toggle-btn');
        
        allToggleButtons.forEach(button => {
            button.addEventListener('click', () => {
                const feedbackId = button.dataset.id;
                const newStatus = button.dataset.newStatus;
                
                // Disable button to prevent double-click
                button.disabled = true;
                button.textContent = 'Updating...';

                // Send the data to the backend script
                fetch('../lib/toggle_feedback.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${feedbackId}&new_status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // --- Update UI without reloading ---
                        const card = document.getElementById(`card-${feedbackId}`);
                        
                        if (newStatus == 1) { 
                            // --- It was just APPROVED ---
                            card.classList.remove('bg-yellow-50', 'border-yellow-400');
                            card.classList.add('bg-white', 'border-green-300');
                            
                            button.classList.remove('bg-green-600', 'hover:bg-green-700');
                            button.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                            
                            button.textContent = 'Unapprove';
                            button.dataset.newStatus = 0; // Set the next state
                            
                        } else {
                            // --- It was just UNAPPROVED ---
                            card.classList.remove('bg-white', 'border-green-300');
                            card.classList.add('bg-yellow-50', 'border-yellow-400');
                            
                            button.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                            button.classList.add('bg-green-600', 'hover:bg-green-700');
                            
                            button.textContent = 'Approve';
                            button.dataset.newStatus = 1; // Set the next state
                        }
                        
                    } else {
                        // On error
                        alert('Error updating status: ' + data.message);
                        // Revert button text
                        button.textContent = (newStatus == 1) ? 'Approve' : 'Unapprove'; 
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    alert('An unexpected error occurred. Please check the console.');
                })
                .finally(() => {
                    // Re-enable the button
                    button.disabled = false;
                });
            });
        });
    });
    </script>

</body>
</html>