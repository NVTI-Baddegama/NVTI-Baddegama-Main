<?php include_once('../include/header.php'); ?>

<style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Merriweather:wght@400;700;900&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden; /* Hide horizontal scrollbar */
        }
        
        /* Custom styles have been removed and replaced with direct Tailwind utility classes in the header tag. */
</style>

<script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Use the dark blue from index.php as the intended primary color
                        'primary-blue': '#1D4ED8', // Dark Blue
                        'secondary-gray': '#4B5563', // Medium Gray
                        'accent-yellow': '#f59e0b', /* Tailwind amber-500 */
                        // Custom red for the "View Course" button on about.php (based on nav-bg in style.css)
                        'primary-red': '#7c1c20', 
                    },
                    fontFamily: {
                        // Add Inter and Merriweather for global use
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Merriweather', 'serif'],
                    },
                    borderRadius: {
                        // Add custom border radius sizes
                        'xl': '0.75rem',
                        '2xl': '1rem',
                    }
                }
            }
        }
    </script>

<header class="relative overflow-hidden aspect-video md:min-h-[60vh]">
        <div class="relative h-full overflow-hidden">
            <!-- Video will cover the proportional container -->
            <video src="https://res.cloudinary.com/dhxfrmepy/video/upload/v1759822024/1006_1_oponlw.mp4"
                class="absolute top-0 left-0 w-full h-full object-cover" autoplay muted loop playsinline></video>
            
            <!-- Overlay and Text -->
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

    <!-- 2. Main About Content Section -->
    <main class="container mx-auto px-4 py-12 md:py-20">

        <!-- Header Section -->
        <section class="text-center mb-12 md:mb-20">
            <h2 class="text-4xl md:text-5xl font-extrabold text-primary-blue mb-4 leading-tight">
                About NVTI Baddegama
            </h2>
            <p class="text-lg md:text-xl text-secondary-gray max-w-3xl mx-auto">
                Our commitment is to excellence, innovation, and community development, empowering the next generation
                of Sri Lankan professionals.
            </p>
        </section>

        <!-- Main Content Layout (Image + History/Mission/Values) -->
        <section
            class="flex flex-col md:flex-row gap-8 lg:gap-12 items-start bg-white p-6 sm:p-8 md:p-12 rounded-2xl shadow-xl shadow-blue-100 mb-12">

            <!-- Descriptive Text Section (History, Mission, CTA) -->
            <div class="md:w-1/2 w-full space-y-6 order-2 md:order-1">
                <h3 class="text-3xl md:text-4xl font-bold text-primary-blue pb-2 border-b-2 border-blue-100">
                    Our History and Mission
                </h3>
                <p class="text-base md:text-lg leading-relaxed text-secondary-gray">
                    Established as a vital part of the national vocational training network under the Vocational
                    Training Authority (VTA) initiative, **NVTI Baddegama** was founded to specifically
                    address the skill gap and empower youth in the Southern Province. Our critical vision is to be a
                    central hub for cutting-edge vocational and technological training.
                </p>

                <!-- Call to Action Button (Responsive) -->
                <a href="about.php"
                    class="inline-block px-8 py-3 mt-4 text-lg font-semibold text-white bg-primary-blue rounded-lg shadow-lg hover:bg-blue-800 transition duration-300 ease-in-out transform hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-blue-300">
                    Learn More
                </a>
            </div>

            <!-- Placeholder Image Section -->
            <div class="md:w-1/2 w-full order-1 md:order-2">
                <!-- Placeholder Image -->
                <img src="../images/image/about_main.png"
                    alt="A professional photo representing the NVTI Baddgama Vocational Training Center or facility"
                    class="w-full h-auto object-cover rounded-xl shadow-2xl transition duration-300 hover:shadow-2xl hover:scale-[1.01]"
                    onerror="this.onerror=null; this.src='https://placehold.co/800x500/4B5563/ffffff?text=Facility+Image+Unavailable';" />
            </div>

        </section>
    </main>

      
<?php include_once('../include/footer.php'); ?>
