<?php
// 1. Header එක include කිරීම
include_once('../include/header.php');

// 2. YouTube URL එකක් embed URL එකක් බවට පත් කරන ශ්‍රිතය
function getYouTubeEmbedUrl($url)
{
    if (empty($url)) {
        return '';
    }
    preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|c)\/))([a-zA-Z0-9\-\_]{11})(&[a-zA-Z0-9\-\_]+)?/", $url, $matches);
    if (isset($matches[1])) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    return '';
}
?>

<main class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">

        <h1 class="text-4xl font-bold text-center text-primary mb-2">Our Success Stories</h1>
        
        <div class="mb-2 text-center">
            <a href="submit_story.php"
                class="inline-block px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
                <i class="fas fa-plus mr-2"></i> Submit Your Story
            </a>
        </div>


        <?php
        // 3. දත්ත ගබඩාවෙන් Success Stories සහ අදාළ පාඨමාලාවේ නම (Course Name) ලබා ගැනීම
        $stories = [];
        if (isset($con)) {
            // 'is_active = 1' ලෙස සකසා ඇති කථා පමණක් තෝරා ගැනීම
            // 'course' table එක සමග LEFT JOIN කිරීම
            $sql = "SELECT s.*, c.course_name 
                    FROM success_stories s 
                    LEFT JOIN course c ON s.course_id = c.id 
                    WHERE s.is_active = 1 
                    ORDER BY s.name ASC";
            
            $result = $con->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $stories[] = $row;
                }
            }
        }
        ?>

        <?php if (!empty($stories)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <?php foreach ($stories as $story): ?>
                    <div
                        class="bg-gray-200 rounded-lg shadow-xl overflow-hidden flex flex-col transition-transform duration-300 hover:scale-105 ">

                        <?php
                        $embedUrl = getYouTubeEmbedUrl($story['youtube_url']);

                        if (!empty($embedUrl)):
                        ?>
                            <div class="aspect-video w-full">
                                <iframe class="w-full h-full" src="<?php echo htmlspecialchars($embedUrl); ?>" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            </div>
                        <?php
                        elseif (!empty($story['image_path'])):
                        ?>
                            <img src="../<?php echo htmlspecialchars($story['image_path']); ?>"
                                alt="<?php echo htmlspecialchars($story['name']); ?>" class="w-full h-56 object-cover">
                        <?php else: ?>
                            <img src="../images/logo/NVTI_logo.png" alt="NVTI Baddegama"
                                class="w-full h-56 object-contain p-4 bg-gray-100">
                        <?php endif; ?>

                        <div class="p-6 flex-grow flex flex-col">
                            <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                                <?php echo htmlspecialchars($story['name']); ?>
                            </h3>
                            
                            <p class="text-primary font-bold text-lg mb-1">
                                <?php echo htmlspecialchars($story['position']); ?>
                            </p>
                            
                            <p class="text-gray-700 font-medium text-md mb-3">
                                <?php echo htmlspecialchars($story['company_name']); ?>
                            </p>

                            <?php if (!empty($story['course_name'])): ?>
                                <p class="text-sm font-semibold text-indigo-700 bg-indigo-200 px-3 py-1 rounded-full inline-block mb-3">
                                    <i class="fas fa-graduation-cap mr-1.5"></i>
                                    <?php echo htmlspecialchars($story['course_name']); ?>
                                </p>
                            <?php endif; ?>

                            <p class="text-gray-700 text-sm mb-4 flex-grow">
                                "<?php echo nl2br(htmlspecialchars($story['description'])); ?>"
                            </p>

                            <?php if (!empty($story['contact_details'])): ?>
                                <p class="text-gray-600 text-sm font-medium mt-auto pt-4 border-t border-gray-100">
                           
                                    <i class="fas fa-phone-alt mr-1.5"></i>  <?php echo htmlspecialchars($story['contact_details']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500 text-xl">
                No success stories have been added yet. Please check back later.
            </p>
        <?php endif; ?>

    </div>
</main>

<?php
// 6. Footer එක include කිරීම
include_once('../include/footer.php');
?>