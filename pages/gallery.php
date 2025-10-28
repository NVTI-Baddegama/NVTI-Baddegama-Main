<?php
include_once('../include/header.php');
// --- PHP ---
// Define the structure of our bento grid.
// We'll use an array of "columns". Each column can have one or more items.
$galleryColumns = [
    // Column 1: One tall image
    [
        'type' => 'column',
        'items' => [
            ['src' => 'https://picsum.photos/seed/img1/400/600', 'alt' => 'Tall 1', 'class' => 'h-96 w-64']
        ]
    ],
    // Column 2: Two stacked square images
    [
        'type' => 'column',
        'items' => [
            ['src' => 'https://picsum.photos/seed/img2/400/400', 'alt' => 'Square 1', 'class' => 'h-48 w-48'],
            ['src' => 'https://picsum.photos/seed/img3/400/400', 'alt' => 'Square 2', 'class' => 'h-48 w-48']
        ]
    ],
    // Column 3: One wide image
    [
        'type' => 'column',
        'items' => [
            ['src' => 'https://picsum.photos/seed/img4/600/400', 'alt' => 'Wide 1', 'class' => 'h-96 w-96']
        ]
    ],
    // Column 4: Two stacked, one wide, one small
    [
        'type' => 'column',
        'items' => [
            ['src' => 'https://picsum.photos/seed/img5/500/300', 'alt' => 'Mid 1', 'class' => 'h-64 w-80'],
            ['src' => 'https://picsum.photos/seed/img6/500/200', 'alt' => 'Mid 2', 'class' => 'h-32 w-80']
        ]
    ],
    // Column 5: Mirrored Column 2
    [
        'type' => 'column',
        'items' => [
            ['src' => 'https://picsum.photos/seed/img7/400/400', 'alt' => 'Square 3', 'class' => 'h-48 w-48'],
            ['src' => 'https://picsum.photos/seed/img8/400/400', 'alt' => 'Square 4', 'class' => 'h-48 w-48']
        ]
    ],
    // Column 6: Mirrored Column 1
    [
        'type' => 'column',
        'items' => [
            ['src' => 'https://picsum.photos/seed/img9/400/600', 'alt' => 'Tall 2', 'class' => 'h-96 w-64']
        ]
    ],
];

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
    $output = '<div class="flex flex-col space-y-4 flex-shrink-0">'; // 'flex-shrink-0' is crucial
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

        /* Define the scrolling animation.
          It moves from 0 to the left by the *exact width* of the
          original set of images, which we calculate in JavaScript.
        */
        @keyframes scroll-left {
            0% {
                transform: translateX(0);
            }
            100% {
                /* '--scroll-width' is a custom variable we will set via JS.
                  We use calc() to make it a negative value.
                */
                transform: translateX(calc(var(--scroll-width, 1000px) * -1));
            }
        }

        /* The class that JavaScript will add to start the animation */
        #bento-track.animate {
            /* You can change the duration (e.g., 40s) to speed up or slow down */
            animation: scroll-left 60s linear infinite;
        }

        /* Optional: Pause animation on hover */
        /* #bento-track:hover {
            animation-play-state: paused;
        } */
    </style>
</head>
<body class="text-gray-900 antialiased">

    <div class="py-12">
        <h1 class="text-center text-4xl font-bold mb-8">NVTI Baddegama Gallery Demo</h1>

        <div class="w-full max-w-none mx-auto overflow-hidden" 
             style="mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);">

            <div id="bento-track" class="flex items-start space-x-4 p-4">
                
                <?php
                // --- PHP (Render Loop) ---
                // Loop through our PHP array and render the first set of columns.
                foreach ($galleryColumns as $column) {
                    echo render_column($column);
                }
                ?>
                
                </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const track = document.getElementById("bento-track");
            
            // 1. Get all the original items (the columns)
            const originalItems = Array.from(track.children);
            
            // 2. Calculate the total width of these original items
            let originalWidth = 0;
            const trackStyles = window.getComputedStyle(track);
            // Get the 'gap' value (from 'space-x-4', which is 1rem or 16px by default)
            const gapWidth = parseFloat(trackStyles.gap) || 16; 

            originalItems.forEach(item => {
                // Add the item's full width (including margins) plus the gap
                originalWidth += item.offsetWidth + gapWidth;
            });

            // 3. Set the CSS custom property for the animation
            // This tells the CSS animation exactly how far to scroll
            track.style.setProperty('--scroll-width', `${originalWidth}px`);

            // 4. Clone each original item and append it to the track
            originalItems.forEach(item => {
                const clone = item.cloneNode(true);
                clone.setAttribute("aria-hidden", "true"); // Hide clones from screen readers
                track.appendChild(clone);
            });

            // 5. Add the 'animate' class to start the animation
            track.classList.add("animate");
        });
    </script>

</body>
</html>


<?php include_once('../include/footer.php'); ?>