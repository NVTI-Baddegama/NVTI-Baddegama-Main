<?php include_once('../include/header.php'); ?>

<body class="min-h-screen text-gray-800">

    <div class="container mx-auto px-4 py-8 md:py-12">
        
        <!-- Header Section -->
        <header class="text-center mb-10 md:mb-16">
            <h1 class="text-5xl md:text-6xl font-extrabold text-primary-blue mb-4 leading-tight">
                About NVTI Baddgama
            </h1>
            <p class="text-xl text-secondary-gray max-w-2xl mx-auto">
                Our commitment is to excellence, innovation, and community development.
            </p>
        </header>

        <!-- 1. Main Content Layout (Image + History/Mission/Values) -->
        <div class="flex flex-col md:flex-row gap-8 lg:gap-12 items-start bg-white p-6 md:p-10 rounded-xl shadow-2xl shadow-blue-100 mb-12">
            
            <!-- Placeholder Image Section -->
            <div class="md:w-1/2 w-full order-1 md:order-2">
                <!-- Use a descriptive placeholder image for now, referencing the Vocational Training Center -->
                <img 
                    src="../images/image/about_main.png" 
                    alt="A professional photo representing the NVTI Baddgama Vocational Training Center or facility" 
                    class="w-full h-auto object-cover rounded-lg shadow-xl transition duration-300 hover:shadow-2xl hover:scale-[1.01]"
                />
            </div>

            <!-- Descriptive Text Section (History, Mission, CTA) -->
            <div class="md:w-1/2 w-full space-y-6 order-2 md:order-1">
                <h2 class="text-4xl font-bold text-primary-blue pb-2">
                    Our History and Mission
                </h2>
                <p class="text-lg leading-relaxed text-secondary-gray">
                    Established as a vital part of the national vocational training network under the Vocational Training Authority (VTA) initiative, <strong>NVTI Baddgama</strong> was founded to specifically address the skill gap and empower youth in the Southern Province. Our critical vision is to be a central hub for cutting-edge vocational and technological training. Our core mission is not just to educate, but to empower. We equip every individual with the <strong>practical skills, industry certification, and professional confidence</strong> necessary to thrive and contribute meaningfully to Sri Lanka's rapidly evolving global economy.
                </p>
                <p class="text-lg leading-relaxed text-secondary-gray">
                    We focus on real-world application, ensuring our graduates are job-ready from day one. By fostering creativity, critical thinking, and strong professional ethics, we prepare the next generation of skilled technicians and innovators.
                </p>
                <a href="#" class="inline-block px-6 py-3 text-white font-semibold rounded-lg bg-[#7c1c20] hover:bg-[#4e1416] transition duration-200 opacity-90" tabindex="-1">View Course</a>
               
            </div>
        </div>

        <!-- 2. Vision and Values Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            
            <!-- Vision -->
            <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg border-t-4 border-accent-yellow">
                <h3 class="text-3xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-8 h-8 text-accent-yellow mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.001 12.001 0 0012 21a12.001 12.001 0 008.618-14.016z"></path></svg>
                    Our Vision
                </h3>
                <p class="text-lg text-secondary-gray">
                    To be recognized as the premier Vocational Training Institute in Sri Lanka, setting the benchmark for industry-aligned, sustainable, and inclusive technical education. We strive to be the driving force behind regional economic prosperity by producing highly sought-after, ethically grounded, and globally competitive professionals.
                </p>
            </div>
            
            <!-- Core Values -->
            <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg border-t-4 border-primary-blue">
                <h3 class="text-3xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-8 h-8 text-primary-blue mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Core Values
                </h3>
                <ul class="space-y-2 text-md text-secondary-gray list-none">
                    <li class="flex items-center"><span class="text-primary-blue font-extrabold mr-2">&bull;</span> <strong>Commitment to Quality:</strong> Unwavering dedication to the highest standards of teaching and infrastructure.</li>
                    <li class="flex items-center"><span class="text-primary-blue font-extrabold mr-2">&bull;</span> <strong>Industry Relevance:</strong> Continuously updating curricula in collaboration with industry partners.</li>
                    <li class="flex items-center"><span class="text-primary-blue font-extrabold mr-2">&bull;</span> <strong>Inclusivity:</strong> Ensuring access to quality training for all members of the community.</li>
                </ul>
            </div>
        </div>

        <!-- 3. Facilities and Advantage Section -->
        <div class="bg-gray-100 p-8 md:p-12 rounded-xl shadow-inner mb-12">
            <h2 class="text-4xl font-bold text-gray-800 text-center mb-8">
                The NVTI Baddgama Advantage
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Facility Point -->
                <div class="bg-white p-5 rounded-lg shadow-md hover:shadow-xl transition duration-300">
                    <div class="text-3xl text-accent-yellow mb-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.505-9-1.745M16 4V2M8 4V2M12 12a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zM5 16s2.5-3 7-3 7 3 7 3M5 16H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2v6a2 2 0 01-2 2h-2"></path></svg>
                    </div>
                    <h4 class="text-xl font-semibold text-primary-blue mb-1">Modern Workshops</h4>
                    <p class="text-sm text-secondary-gray">
                        We provide spacious, fully-equipped workshops and labs with the latest industrial machinery and technology, giving students hands-on experience that mirrors the current workplace.
                    </p>
                </div>

                <!-- Faculty Point -->
                <div class="bg-white p-5 rounded-lg shadow-md hover:shadow-xl transition duration-300">
                    <div class="text-3xl text-primary-blue mb-2">
                         <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h4 class="text-xl font-semibold text-primary-blue mb-1">Expert Faculty</h4>
                    <p class="text-sm text-secondary-gray">
                        Our instructors are industry veterans, bringing years of practical experience and specialized knowledge directly into the classroom, ensuring relevant and high-quality teaching.
                    </p>
                </div>
                
                <!-- Partnership Point -->
                <div class="bg-white p-5 rounded-lg shadow-md hover:shadow-xl transition duration-300">
                    <div class="text-3xl text-accent-yellow mb-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v4a1 1 0 01-1 1H3a1 1 0 01-1-1V7a1 1 0 011-1h4a1 1 0 011 1zm0 0V4a1 1 0 011-1h4a1 1 0 011 1v3m-4 10v4a1 1 0 001 1h4a1 1 0 001-1v-4m-4 0v-3a1 1 0 011-1h4a1 1 0 011 1v3m-4-7h6a2 2 0 012 2v2a2 2 0 01-2 2h-6a2 2 0 01-2-2V12a2 2 0 012-2z"></path></svg>
                    </div>
                    <h4 class="text-xl font-semibold text-primary-blue mb-1">Industry Partnerships</h4>
                    <p class="text-sm text-secondary-gray">
                        Strong ties with regional industries lead to internship opportunities, guest lectures, and high job placement rates for our graduating students.
                    </p>
                </div>
            </div>
        </div>

        <!-- 4. Leadership Introduction Section -->
        <div class="text-center p-6 md:p-8 bg-white rounded-xl shadow-lg border-2 border-primary-blue/20">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">
                Meet Our Dedicated Leadership
            </h2>
            <p class="text-lg text-secondary-gray max-w-3xl mx-auto">
                The success of NVTI Baddgama is guided by a visionary management team and supported by dedicated faculty members who are passionate about technical education and youth empowerment. We invite you to learn more about the people steering our commitment to excellence.
            </p>
        </div>
        
    </div>
<?php include_once('../include/footer.php'); ?>

