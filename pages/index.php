<?php include_once('../include/header.php'); ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Merriweather:wght@400;700;900&display=swap');
</style>

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    // ... (your existing colors)
                    'primary-blue': '#1D4ED8',
                    'secondary-gray': '#4B5563',
                    'accent-yellow': '#f59e0b',
                    'primary-red': '#7c1c20',
                },
                fontFamily: {
                    // ... (your existing fonts)
                    sans: ['Inter', 'sans-serif'],
                    serif: ['Merriweather', 'serif'],
                },
                borderRadius: {
                    // ... (your existing borderRadius)
                    'xl': '0.75rem',
                    '2xl': '1rem',
                },
                
                // --- ADDED FOR SCROLLING FEEDBACK ---
                animation: {
                    'scroll': 'scroll 1s linear infinite',
                },
                keyframes: {
                    'scroll': {
                        '0%': { transform: 'translateX(0)' },
                        // We move by 50% because the track is duplicated (200% width)
                        '100%': { transform: 'translateX(-50%)' }, 
                    }
                }
                // --- END OF ADDED CODE ---
            }
        }
    }
</script>

<body class="font-sans bg-[#f8f9fa]">
    <?php
    // --- Feedback Status Alert ---
    if (isset($_GET['status'])):
        $status = $_GET['status'];
        $message = '';
        $bgColor = '';
        $title = '';

        if ($status == 'success') {
            $title = 'Success!';
            $message = 'Thank you! Your feedback has been submitted for approval.';
            $bgColor = 'bg-green-100 border-green-400 text-green-700';
        } else if ($status == 'error' || $status == 'dberror') {
            $title = 'Error';
            $message = 'An error occurred. Please try submitting your feedback again.';
            $bgColor = 'bg-red-100 border-red-400 text-red-700';
        } else if ($status == 'validation_failed') {
            $title = 'Warning';
            $message = 'Please make sure you fill out all fields and select a rating.';
            $bgColor = 'bg-yellow-100 border-yellow-400 text-yellow-700';
        }

        if ($message): // Only show if we have a message
    ?>
        <div class="container mx-auto px-4 mt-4">
            <div class="<?php echo $bgColor; ?> border-l-4 p-4 rounded-md shadow-md" role="alert">
                <p class="font-bold"><?php echo $title; ?></p>
                <p><?php echo $message; ?></p>
            </div>
        </div>
    <?php 
        endif; 
    endif;
    // --- End Feedback Status Alert ---
    ?>

    <header class="relative overflow-hidden aspect-video md:min-h-[60vh]">
        <div class="relative h-full overflow-hidden">
            <video src="https://res.cloudinary.com/dhxfrmepy/video/upload/v1759822024/1006_1_oponlw.mp4"
                class="absolute top-0 left-0 w-full h-full object-cover" autoplay muted loop playsinline></video>
            
            <div class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-black bg-opacity-60 p-4">
                <h1
                    class="text-4xl sm:text-5xl md:text-7xl lg:text-8xl font-extrabold text-white text-center leading-tight tracking-wider uppercase mb-4 font-serif">
                    WELCOME TO <br> NVTI BADDEGAMA
                </h1>
                <h4 class="text-base sm:text-xl md:text-2xl lg:text-3xl font-medium text-center mt-4 tracking-wider text-white font-serif"
                    style="text-shadow: 0 0 5px #000000;">
                    A step toward a skilled life
                </h4>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-12 md:py-20">

        <section class="text-center mb-12 md:mb-20">
            <h2 class="text-4xl md:text-5xl font-extrabold text-primary-blue mb-4 leading-tight">
                About NVTI Baddegama
            </h2>
            <p class="text-lg md:text-xl text-secondary-gray max-w-3xl mx-auto">
                Our commitment is to excellence, innovation, and community development, empowering the next generation
                of Sri Lankan professionals.
            </p>
        </section>

        <section
            class="flex flex-col md:flex-row gap-8 lg:gap-12 items-start bg-white p-6 sm:p-8 md:p-12 rounded-2xl shadow-xl shadow-blue-100 mb-12">

            <div class="md:w-1/2 w-full space-y-6 order-2 md:order-1">
                <h3 class="text-3xl md:text-4xl font-bold text-primary-blue pb-2 border-b-2 border-blue-100">
                    Our History and Mission
                </h3>
                <p class="text-base md:text-lg leading-relaxed text-secondary-gray">
                    Established as a vital part of the national vocational training network under the Vocational
                    Training Authority (VTA) initiative, <strong>NVTI Baddegama</strong> was founded to specifically
                    address the skill gap and empower youth in the Southern Province. Our critical vision is to be a
                    central hub for cutting-edge vocational and technological training.
                </p>

                <a href="about.php"
                    class="inline-block px-8 py-3 mt-4 text-lg font-semibold text-white bg-primary-blue rounded-lg shadow-lg hover:bg-blue-800 transition duration-300 ease-in-out transform hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-blue-300">
                    Learn More
                </a>
            </div>

            <div class="md:w-1/2 w-full order-1 md:order-2">
                <img src="../images/image/about_main.png"
                    alt="A professional photo representing the NVTI Baddgama Vocational Training Center or facility"
                    class="w-full h-auto object-cover rounded-xl shadow-2xl transition duration-300 hover:shadow-2xl hover:scale-[1.01]"
                    onerror="this.onerror=null; this.src='https://placehold.co/800x500/4B5563/ffffff?text=Facility+Image+Unavailable';" />
            </div>

        </section>

        <section class="py-12 md:py-20">
            <h2 class="text-4xl md:text-5xl font-extrabold text-primary-blue mb-12 leading-tight text-center">
                Our Leaders
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white rounded-2xl shadow-xl shadow-blue-100 overflow-hidden transition duration-300 ease-in-out hover:shadow-2xl">
                    <img class="w-full h-[300px] object-cover" src="../images/leaders/ad.jpeg" alt="Placeholder image for card 1">
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-primary-blue mb-2">Assistent Director</h3>
                        <p class="text-secondary-gray">
                            Providing essential leadership and strategic support, the Assistant Director is key to bridging high-level vision with effective daily operations and team guidance
                        </p>
                       
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl shadow-blue-100 overflow-hidden transition duration-300 ease-in-out hover:shadow-2xl">
                    <img class="w-full h-[300px] object-cover" src="../images/leaders/to.jpeg" alt="Placeholder image for card 2">
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-primary-blue mb-2">Training Officer</h3>
                        <p class="text-secondary-gray">
                            A dedicated champion of professional growth, the Training Officer plays a vital role in developing talent and equipping our team with the skills needed to excel.
                        </p>
                       
                    </div>
                </div>


                <div class="bg-white rounded-2xl shadow-xl shadow-blue-100 overflow-hidden transition duration-300 ease-in-out hover:shadow-2xl">
                    <img class="w-full h-[300px] object-cover" src="https://via.placeholder.com/400x300/FBCFE8/333333?text=Course+Image" alt="Placeholder image for card 4">
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-primary-blue mb-2">Account Officer</h3>
                        <p class="text-secondary-gray">
                            With meticulous attention to detail, the Account Officer ensures the financial integrity and accuracy of our operations, serving as a crucial steward of our resources.
                        </p>
                        
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl shadow-blue-100 overflow-hidden transition duration-300 ease-in-out hover:shadow-2xl">
                    <img class="w-full h-[300px] object-cover" src="https://via.placeholder.com/400x300/BFDBFE/333333?text=Course+Image" alt="Placeholder image for card 3">
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-primary-blue mb-2">Program Officer</h3>
                        <p class="text-secondary-gray">
                            The driving force behind our key initiatives, the Program Officer expertly manages projects from concept to completion, ensuring we meet our goals and deliver impact.
                        </p>
                       
                    </div>
                </div>

            </div>
        </section>

        <div>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mb-12">
                <h2 class="text-4xl md:text-5xl font-extrabold text-primary-blue leading-tight text-center">
                    What our students say
                </h2>
            </div>

            <?php
            // --- 1. Database Connection ---
           include_once('../include/connection.php');

            // Check if connection exists and is valid
            if (!$con || $con->connect_error) {
                // For production, you might want to log this error instead of displaying it
                echo '<p class="text-center text-red-500">Could not connect to the feedback service.</p>';
            } else {
                
                // --- 2. Fetch Approved Feedback from Database ---
                $feedbacks = []; // Initialize an empty array
                $sql = "SELECT user_name, feedback, rating FROM feedback WHERE approve = 1 ORDER BY id DESC";
                $result = $con->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $feedbacks[] = [
                            'name' => $row['user_name'],
                            'feedback' => $row['feedback'],
                            'rating' => $row['rating']
                        ];
                    }
                }
         }

          
            if (!function_exists('render_stars')) {
                function render_stars($rating) {
                    $output = '<div class="flex items-center space-x-1 text-accent-yellow">';
                    $star_svg = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.445a1 1 0 00-.364 1.118l1.287 3.958c.3.921-.755 1.688-1.54 1.118l-3.367-2.445a1 1 0 00-1.175 0l-3.367 2.445c-.784.57-1.838-.197-1.54-1.118l1.287-3.958a1 1 0 00-.364-1.118L2.28 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.958z"></path></svg>';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) $output .= $star_svg;
                    }
                    $output .= '</div>';
                    return $output;
                }
            }
            
            /**
             * Creates a single feedback card.
             */
            if (!function_exists('create_feedback_card')) {
                function create_feedback_card($item) {
                    return '
                    <div class="flex-shrink-0 w-[380px] h-full bg-white rounded-2xl shadow-xl shadow-blue-100 p-6 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-xl font-bold text-primary-blue">' . htmlspecialchars($item['name']) . '</h4>
                            ' . render_stars($item['rating']) . '
                        </div>
                        <blockquote class="flex-grow text-secondary-gray italic border-l-4 border-blue-100 pl-4">
                            <p>"' . htmlspecialchars($item['feedback']) . '"</p>
                        </blockquote>
                    </div>';
                }
            }

            // --- 3. Display Scroller or a "No Feedback" Message ---
            if (!empty($feedbacks)) :
            ?>
                <div class="w-full overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_10%,black_90%,transparent)]">
                    <div class="flex w-max animate-scroll hover:[animation-play-state:paused]">
                        <div class="flex space-x-6 px-3 py-4">
                            <?php foreach ($feedbacks as $item) { echo create_feedback_card($item); } ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center p-8 bg-white rounded-2xl shadow-lg shadow-blue-100">
                    <p class="text-lg text-secondary-gray">
                        No student feedback is available at the moment.
                    </p>
                    <p class="text-md text-gray-500 mt-2">
                        Why not be the first to share your experience?
                    </p>
                </div>
            <?php endif; ?>
        </div>

            <div class="w-full flex justify-center">

    <button id="openFeedbackModal" 
            class="px-6 py-2 text-base font-semibold text-white bg-primary-blue rounded-lg shadow-md hover:bg-blue-800 transition duration-300 ease-in-out transform hover:scale-105 active:scale-95">
        Add Yours
    </button>
    
</div>
    </main>

    <div id="feedbackModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black bg-opacity-60 transition-opacity duration-300">
    
    <div id="modalContent" class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-auto relative transform transition-all duration-300 scale-95 opacity-0">
        
        <div class="flex items-start justify-between p-5 border-b border-gray-200 rounded-t-2xl">
            <h3 class="text-2xl font-bold text-primary-blue">
                Share Your Feedback
            </h3>
            <button id="closeFeedbackModal" type="button" class=" text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
        
        <div class="p-6">
            <form id="feedbackForm" action="../lib/process_feedback.php" method="POST" class="space-y-4">
                
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Your Rating</label>
                    <div id="star-rating" class="flex space-x-1">
                        <?php 
                        // Star SVG path
                        $star_svg_path = "M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.445a1 1 0 00-.364 1.118l1.287 3.958c.3.921-.755 1.688-1.54 1.118l-3.367-2.445a1 1 0 00-1.175 0l-3.367 2.445c-.784.57-1.838-.197-1.54-1.118l1.287-3.958a1 1 0 00-.364-1.118L2.28 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.958z";
                        
                        for ($i = 1; $i <= 5; $i++): ?>
                            <svg class="w-8 h-8 text-gray-300 cursor-pointer star" data-value="<?php echo $i; ?>" fill="currentColor" viewBox="0 0 20 20">
                                <path d="<?php echo $star_svg_path; ?>"></path>
                            </svg>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="rating-value" value="0" required>
                </div>
                
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-700">Your Name</label>
                    <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-blue focus:border-primary-blue" placeholder="e.g., K. Perera" required>
                </div>

                <div>
                    <label for="feedback" class="block mb-2 text-sm font-medium text-gray-700">Your Feedback</label>
                    <textarea id="feedback" name="feedback" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-blue focus:border-primary-blue" placeholder="What did you like or think we could improve?" required></textarea>
                </div>

                <button type="submit" class="w-100  px-6 py-3 text-lg font-semibold text-white bg-primary-blue rounded-lg shadow-lg hover:bg-blue-800 transition duration-300">
                    Submit Feedback
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Modal Elements ---
    const modal = document.getElementById('feedbackModal');
    const modalContent = document.getElementById('modalContent');
    const openBtn = document.getElementById('openFeedbackModal');
    const closeBtn = document.getElementById('closeFeedbackModal');
    
    // --- Star Rating Elements ---
    const stars = document.querySelectorAll('#star-rating .star');
    const ratingInput = document.getElementById('rating-value');
    const feedbackForm = document.getElementById('feedbackForm');
    let currentRating = 0; // Stores the clicked rating

    // --- Modal Toggle Functions ---
    const openModal = () => {
        modal.classList.remove('hidden');
        // Trigger transition effects
        setTimeout(() => {
            modal.classList.add('bg-opacity-60');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10); // Short delay to allow CSS to catch up
    };

    const closeModal = () => {
        // Trigger closing transition
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        modal.classList.remove('bg-opacity-60');
        
        // Hide modal after transition
        setTimeout(() => {
            modal.classList.add('hidden');
            // Reset form on close
            feedbackForm.reset();
            setRating(0); // Reset stars
        }, 300); // Must match transition duration
    };

    // --- Modal Event Listeners ---
    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    
    // Close by clicking the overlay
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // --- Star Rating Logic ---
    
    /**
     * Sets the visual star rating and updates the hidden input.
     * @param {number} value The rating value (1-5) or 0 to clear.
     */
    const setRating = (value) => {
        currentRating = value; // Update the "clicked" state
        ratingInput.value = value; // Update the form input
        
        stars.forEach(star => {
            if (star.dataset.value <= value) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-accent-yellow'); // 'accent-yellow' from your config
            } else {
                star.classList.remove('text-accent-yellow');
                star.classList.add('text-gray-300');
            }
        });
    };

    /**
     * Visually highlights stars on hover.
     * @param {number} value The rating value (1-5) being hovered.
     */
    const hoverRating = (value) => {
        stars.forEach(star => {
            if (star.dataset.value <= value) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-accent-yellow');
            } else {
                star.classList.remove('text-accent-yellow');
                star.classList.add('text-gray-300');
            }
        });
    };

    stars.forEach(star => {
        // Set the clicked rating
        star.addEventListener('click', () => {
            setRating(star.dataset.value);
        });

        // Show hover effect
        star.addEventListener('mouseover', () => {
            hoverRating(star.dataset.value);
        });
    });
    
    // Reset stars to the "clicked" rating when mouse leaves the star container
    document.getElementById('star-rating').addEventListener('mouseout', () => {
        setRating(currentRating);
    });


    // --- Form Validation (Simple) ---
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', (e) => {
            // Check if a rating was selected
            if (ratingInput.value === '0') {
                e.preventDefault(); // Stop form submission
                alert('Please select a star rating (1-5).');
            }
            // You can add more validation for name/feedback here
        });
    }
});
</script>
<?php include_once('../include/footer.php'); ?>