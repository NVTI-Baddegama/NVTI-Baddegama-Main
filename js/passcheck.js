
// අදාළ HTML elements තෝරාගැනීම
const olPassYes = document.getElementById('olPassYes');
const olPassNo = document.getElementById('olPassNo');
const olSubjectsContainer = document.getElementById('olSubjectsContainer');

// div එක hide/show කරන function එක
function toggleSubjectFieldsVisibility() {
    // 'Yes' radio button එක තෝරා ඇත්නම්
    if (olPassYes.checked) {
        // 'hidden' class එක ඉවත් කර div එක පෙන්වන්න
        olSubjectsContainer.classList.remove('hidden');
    } else {
        // 'hidden' class එක එකතු කර div එක සඟවන්න
        olSubjectsContainer.classList.add('hidden');
    }
}

// Radio buttons click කළ විට function එක ක්‍රියාත්මක කරන්න
olPassYes.addEventListener('change', toggleSubjectFieldsVisibility);
olPassNo.addEventListener('change', toggleSubjectFieldsVisibility);
