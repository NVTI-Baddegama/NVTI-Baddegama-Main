<?php include_once ('../include/header.php'); ?>

<main class="relative h-screen w-screen overflow-hidden">

    <div class="main_video">
        <video 
            autoplay 
            muted 
            loop 
            playsinline 
            class="absolute inset-0 h-full w-full object-cover z-0"
            style="filter: blur(4px);" 
        >
            <source src="../images/video/nvti_main.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <div class="relative z-10 flex h-full w-full flex-col items-center justify-center bg-black bg-opacity-40 p-4"> 
        
        <h1 class="text-6xl md:text-8xl font-extrabold text-white text-center leading-none tracking-wider uppercase mb-4 font-serif">
            WELCOME TO <br> NVTI BADDEGAMA 
        </h1>
        
        <h4 class="text-xl md:text-3xl font-medium text-center mt-4 tracking-wider text-white font-serif" style="text-shadow: 0 0 5px #000000;">
            A step toward a skilled life
        </h4>
    </div>

</main>

<?php include_once ('../include/footer.php'); ?>