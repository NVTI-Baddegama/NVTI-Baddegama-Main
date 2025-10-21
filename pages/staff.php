<?php 
// 1. Include Header and Database Connection
include_once('../include/header.php'); 
include_once('../include/connection.php');

// 2. --- NEW SQL QUERY WITH CUSTOM SORTING ---
$query = "SELECT * FROM staff 
          WHERE status = 'active'
          ORDER BY
            CASE
              WHEN position = 'Instructors' THEN 2
              WHEN position = 'Non-Academic Staff' THEN 3
              ELSE 1  -- This puts all 'Special Characters' (like Assistant Director) first
            END ASC,
            first_name ASC";
          
$result = $con->query($query);
?>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

<body class="min-h-screen bg-gray-50" onload="lucide.createIcons()">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <header class="text-center mb-10">
            <h1 class="text-5xl font-extrabold text-gray-900 mb-2">
                Meet Our Team
            </h1>
            <p class="text-lg text-gray-600">Our team of dedicated professionals.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <?php
            // 3. Check if any staff members were found
            if ($result && $result->num_rows > 0) {
                
                // 4. Loop through each staff member (The loop code remains the same)
                while ($staff = $result->fetch_assoc()) {
                    
                    // --- Handle Profile Photo ---
                    $photo_url = "https://placehold.co/150x150/5DADE2/ffffff?text=" . substr($staff['first_name'], 0, 1); // Default placeholder
                    
                    if (!empty($staff['profile_photo']) && file_exists('../uploads/profile_photos/' . $staff['profile_photo'])) {
                        // If photo exists, use it
                        $photo_url = '../uploads/profile_photos/' . $staff['profile_photo'];
                    }
                    
                    // --- Handle Full Name ---
                    $full_name = htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']);
            ?>

            <div class="bg-white shadow-xl rounded-xl p-6 flex space-x-6 items-start transform transition duration-300 hover:scale-[1.01] hover:shadow-2xl border border-gray-50">
                
                <img 
                    src="<?php echo $photo_url; ?>" 
                    alt="<?php echo $full_name; ?>" 
                    class="w-32 h-32 rounded-xl object-cover shadow-md flex-shrink-0"
                >
                
                <div class="flex flex-col flex-grow">
                    <h2 class="text-xl font-bold text-gray-900 mb-0.5"><?php echo $full_name; ?></h2>
                    <p class="text-md text-indigo-600 font-semibold mb-2"><?php echo htmlspecialchars($staff['position']); ?></p>
                    
                    <span class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition duration-150 mt-auto">
                        View Full Profile
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </span>
                </div>
            </div>
            <?php
                } // End of while loop
            } else {
                // 5. Show message if no staff are found
                echo '<p class="col-span-1 md:col-span-2 text-center text-gray-600 text-lg">No staff members found at this time.</p>';
            }
            ?>

        </div>
        </div>

<?php include_once('../include/footer.php'); ?>