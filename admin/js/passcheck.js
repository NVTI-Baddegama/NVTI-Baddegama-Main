// අදාළ HTML elements තෝරාගැනීම
    const olPassYes = document.getElementById('olPassYes');
    const olPassNo = document.getElementById('olPassNo');
    const olSubjectsContainer = document.getElementById('olSubjectsContainer'); // අලුතින් එකතු කළ div එක

    // Select boxes අඩංගු div එක hide/show කරන function එක
    function toggleSubjectFieldsVisibility() {
        // 'Yes' radio button එක check කර ඇත්නම්
        if (olPassYes.checked) {
            // 'hidden' class එක ඉවත් කර div එක පෙන්වන්න
            olSubjectsContainer.classList.remove('hidden');
        } else {
            // 'hidden' class එක එකතු කර div එක සඟවන්න
            olSubjectsContainer.classList.add('hidden');
        }
    }

    // Radio buttons වල 'change' event එකට සවන් දීම
    olPassYes.addEventListener('change', toggleSubjectFieldsVisibility);
    olPassNo.addEventListener('change', toggleSubjectFieldsVisibility);