document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('newYearModal');
    const closeX = document.querySelector('.ny-close');
    const closeBtn = document.getElementById('ny-close-btn');

    // Check if the user has already seen the popup in this session
    if (!sessionStorage.getItem('newYearPopUpShown')) {
        setTimeout(() => {
            modal.style.display = 'flex';
            startConfetti();
        }, 1000); // Small delay for better UX
    }

    // Function to close the modal
    function closeModal() {
        modal.style.animation = 'fadeIn 0.3s reverse forwards';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
        sessionStorage.setItem('newYearPopUpShown', 'true');
    }

    closeX.onclick = closeModal;
    closeBtn.onclick = closeModal;

    // Close when clicking outside the content box
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    };

    // Confetti Animation Logic
    function startConfetti() {
        const colors = ['#d4af37', '#f4df4e', '#ffffff', '#ff4d4d', '#4d79ff'];
        for (let i = 0; i < 50; i++) {
            createConfetti(colors);
        }
    }

    function createConfetti(colors) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.width = Math.random() * 8 + 5 + 'px';
        confetti.style.height = confetti.style.width;
        
        const duration = Math.random() * 3 + 2;
        confetti.style.animation = `fall ${duration}s linear forwards`;
        
        document.body.appendChild(confetti);

        // Remove element after animation finishes
        setTimeout(() => {
            confetti.remove();
        }, duration * 1000);
    }
});