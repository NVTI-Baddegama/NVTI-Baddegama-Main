<?php
include_once('../include/header.php');
// NEW: Include database connection
include_once('../include/connection.php');

// --- NEW: Fetch images from DB ---
$sql = "SELECT image_name, image_path FROM gallery ORDER BY created_at DESC";
$result = $con->query($sql);
$images = [];
if ($result && $result->num_rows > 0) {
    // Fetch all images into an array
    $images = $result->fetch_all(MYSQLI_ASSOC);
}
// --- END: Fetch images ---


$galleryColumns = [
    // Column 1: One tall image
    [
        'type' => 'column',
        'items' => [
            // h-full is fine as it's the only item
            ['src' => 'https://picsum.photos/seed/img1/400/600', 'alt' => 'Tall 1', 'class' => 'h-full w-80']
        ]
    ],
    // Column 2: Two stacked square images
    [
        'type' => 'column',
        'items' => [
            // Use flex-1 to make them share the column height
            ['src' => 'https://picsum.photos/seed/img2/400/400', 'alt' => 'Square 1', 'class' => 'flex-1 w-64'],
            ['src' => 'https://picsum.photos/seed/img3/400/400', 'alt' => 'Square 2', 'class' => 'flex-1 w-64']
        ]
    ],
    // Column 3: One wide image
    [
        'type' => 'column',
        'items' => [
            // h-full is fine
            ['src' => 'https://picsum.photos/seed/img4/600/400', 'alt' => 'Wide 1', 'class' => 'h-full w-[32rem]']
        ]
    ],
    // Column 4: Two stacked, one wide, one small
    [
        'type' => 'column',
        'items' => [
            // Use flex-grow ratios (flex-[2] and flex-1) to maintain 2/3 and 1/3
            ['src' => 'https://picsum.photos/seed/img5/500/300', 'alt' => 'Mid 1', 'class' => 'flex-[2] w-96'], // 2/3 ratio
            ['src' => 'https://picsum.photos/seed/img6/500/200', 'alt' => 'Mid 2', 'class' => 'flex-1 w-96']     // 1/3 ratio
        ]
    ],
    // Column 5: Mirrored Column 2
    [
        'type' => 'column',
        'items' => [
            // Use flex-1 again
            ['src' => 'https://picsum.photos/seed/img7/400/400', 'alt' => 'Square 3', 'class' => 'flex-1 w-64'],
            ['src' => 'https://picsum.photos/seed/img8/400/400', 'alt' => 'Square 4', 'class' => 'flex-1 w-64']
        ]
    ],
    // Column 6: Mirrored Column 1
    [
        'type' => 'column',
        'items' => [
            // h-full is fine
            ['src' => 'https://picsum.photos/seed/img9/400/600', 'alt' => 'Tall 2', 'class' => 'h-full w-80']
        ]
    ],
];

// --- NEW: Dynamically populate the gallery slots with DB images ---
if (!empty($images)) {
    $imageCount = count($images);
    $imageCounter = 0;

    // Loop through each column and item by reference (&) to modify them
    foreach ($galleryColumns as &$column) { 
        foreach ($column['items'] as &$item) { 
            // Get the next image, looping back to the start if we run out
            $currentImage = $images[$imageCounter % $imageCount];

            $db_path = $currentImage['image_path'];
            // This replaces the "../" with "../admin/" to create the correct public path
            $public_path = str_replace('../', '../admin/', $db_path);

            $item['src'] = htmlspecialchars($public_path);
            $item['alt'] = htmlspecialchars($currentImage['image_name']);
            // --- END: PATH CORRECTION ---

            $imageCounter++;
        }
    }
    unset($column); // Unset references after loop
    unset($item);
}
// --- END NEW ---


/**
 * Helper function to render a single gallery item (an image).
 */
function render_item($item) {
    return '
    <div class="rounded-xl overflow-hidden shadow-lg ' . $item['class'] . '">
        <img src="' . $item['src'] . '" alt="' . $item['alt'] . '" class="w-full h-full object-cover">
    </div>';
}

/**
 * Helper function to render a full column.
 */
function render_column($column) {
    // --- CHANGED: Back to h-full and space-y-4 for correct full-height scaling ---
    $output = '<div class="flex flex-col h-full space-y-4 flex-shrink-0">'; // 'flex-shrink-0' is crucial
    foreach ($column['items'] as $item) {
        $output .= render_item($item);
    }
    $output .= '</div>';
    return $output;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <style>
        /* --- CSS (Animation) --- */
        @keyframes scroll-left {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(calc(var(--scroll-width, 1000px) * -1));
            }
        }
        #bento-track.animate {
            /* Slower animation for larger images */
            animation: scroll-left 20s linear infinite; 
        }
    </style>
</head>

<body class="text-gray-900 antialiased m-0 flex flex-col min-h-screen">

    <div class="py-8">
        <h1 class="text-center text-4xl font-bold">NVTI Baddegama Gallery</h1>
    </div>
    
    <div class="flex-1 h-0 w-full">
        
        <div class="w-full h-full max-w-none mx-auto overflow-hidden" 
             style="mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);">

            <div id="bento-track" class="flex h-full items-start p-4 space-x-4">
                
                <?php
                // --- PHP (Render Loop) ---
                foreach ($galleryColumns as $column) {
                    echo render_column($column);
                }
                ?>
                
            </div>
        </div>
    </div>

    <?php include_once('../include/footer.php'); ?>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const track = document.getElementById("bento-track");
            
            const originalItems = Array.from(track.children);
            
            if (originalItems.length === 0) {
                 track.innerHTML = '<p class="text-center text-gray-500 w-full">Gallery is empty.</p>';
                 return;
            }

            let originalWidth = 0;
            const trackStyles = window.getComputedStyle(track);
            const gapWidth = parseFloat(trackStyles.gap) || 16; 

            originalItems.forEach(item => {
                originalWidth += item.offsetWidth + gapWidth;
            });

            track.style.setProperty('--scroll-width', `${originalWidth}px`);

            originalItems.forEach(item => {
                const clone = item.cloneNode(true);
                clone.setAttribute("aria-hidden", "true");
                track.appendChild(clone);
            });

            track.classList.add("animate");
        });
    </script>

</body>
</html>