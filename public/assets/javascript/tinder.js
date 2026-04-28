
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.tinder-card');
    const likeButtons = document.querySelectorAll('.like-btn');
    const dislikeButtons = document.querySelectorAll('.dislike-btn');
    const tinderContainer = document.querySelector('.tinder-container');
    const endMessage = document.querySelector('.end-message');
    const actionButtons = document.querySelector('.card-actions');

    let currentCardIndex = 0;
    let isAnimating = false;

    // Initialize card positions
    function initCards() {
        cards.forEach((card, index) => {
            card.style.display = 'flex';
            card.style.opacity = '1';
            card.style.transition = 'transform 0.3s ease';

            // Reset any animations
            card.style.animation = 'none';

            // Set z-index and position based on stack order
            const stackIndex = index - currentCardIndex;
            if (stackIndex === 0) {
                card.style.zIndex = cards.length;
                card.style.transform = 'translateY(0) scale(1)';
            } else if (stackIndex > 0 ) {
                const capped = Math.min(stackIndex, 12);
                card.style.zIndex = cards.length - stackIndex;
                card.style.transform = `translateY(${stackIndex * 10}px) scale(${1 - (stackIndex * 0.03)})`;
            } 
        });
    }

    initCards();

    // Handle swipe animation
    function handleSwipe(direction) {
        if (isAnimating || currentCardIndex >= cards.length) return;

        isAnimating = true;
        const currentCard = cards[currentCardIndex];
        const likeElement = currentCard.querySelector('.like');
        const dislikeElement = currentCard.querySelector('.dislike');

        // Show like/dislike feedback
        if (direction === 'right') {
            likeElement.style.opacity = '1';
            currentCard.style.animation = 'swipeRight 0.5s forwards';
        } else {
            dislikeElement.style.opacity = '1';
            currentCard.style.animation = 'swipeLeft 0.5s forwards';
        }

        // After animation completes
        setTimeout(() => {
            currentCard.style.display = 'none';
            currentCardIndex++;

            // Reset feedback icons
            likeElement.style.opacity = '0';
            dislikeElement.style.opacity = '0';

            // Show end message if no more cards
            if (currentCardIndex >= cards.length) {
                endMessage.style.display = 'block';
                actionButtons.style.display = 'none';
            } else {
                // Reinitialize remaining cards
                initCards();
            }

            isAnimating = false;
        }, 500);
    }

    // Button event listeners
    likeButtons.forEach(btn => {
        btn.addEventListener('click', () => handleSwipe('right'));
    });

    dislikeButtons.forEach(btn => {
        btn.addEventListener('click', () => handleSwipe('left'));
    });

    // Touch/mouse swipe handling
    let touchStartX = 0;
    let touchEndX = 0;
    let isDragging = false;
    let dragStartX = 0;
    let dragMoveX = 0;

    cards.forEach(card => {
        // Touch events for mobile
        card.addEventListener('touchstart', (e) => {
            if (parseInt(card.dataset.index) !== currentCardIndex) return;
            touchStartX = e.touches[0].clientX;
            card.style.transition = 'none';
        });

        card.addEventListener('touchmove', (e) => {
            if (parseInt(card.dataset.index) !== currentCardIndex) return;
            const touchX = e.touches[0].clientX;
            const deltaX = touchX - touchStartX;
            const rotate = deltaX * 0.1;

            card.style.transform = `translateX(${deltaX}px) rotate(${rotate}deg) translateY(0) scale(1)`;

            // Show like/dislike feedback
            const likeElement = card.querySelector('.like');
            const dislikeElement = card.querySelector('.dislike');
            if (deltaX > 50) {
                likeElement.style.opacity = Math.min(1, deltaX / 100);
                dislikeElement.style.opacity = '0';
            } else if (deltaX < -50) {
                dislikeElement.style.opacity = Math.min(1, Math.abs(deltaX) / 100);
                likeElement.style.opacity = '0';
            }
        });

        card.addEventListener('touchend', (e) => {
            if (parseInt(card.dataset.index) !== currentCardIndex) return;
            touchEndX = e.changedTouches[0].clientX;
            handleSwipeGesture();
            card.style.transition = 'transform 0.3s ease';
        });

        // Mouse events for desktop
        card.addEventListener('mousedown', (e) => {
            if (parseInt(card.dataset.index) !== currentCardIndex) return;
            isDragging = true;
            dragStartX = e.clientX;
            card.style.transition = 'none';
        });
    });

    document.addEventListener('mousemove', (e) => {
        if (!isDragging || isAnimating || currentCardIndex >= cards.length) return;

        const currentCard = cards[currentCardIndex];
        dragMoveX = e.clientX - dragStartX;
        const rotate = dragMoveX * 0.1;
        currentCard.style.transform = `translateX(${dragMoveX}px) rotate(${rotate}deg) translateY(0) scale(1)`;

        // Show like/dislike feedback
        const likeElement = currentCard.querySelector('.like');
        const dislikeElement = currentCard.querySelector('.dislike');
        if (dragMoveX > 50) {
            likeElement.style.opacity = Math.min(1, dragMoveX / 100);
            dislikeElement.style.opacity = '0';
        } else if (dragMoveX < -50) {
            dislikeElement.style.opacity = Math.min(1, Math.abs(dragMoveX) / 100);
            likeElement.style.opacity = '0';
        }
    });

    document.addEventListener('mouseup', () => {
        if (!isDragging) return;
        isDragging = false;

        const currentCard = cards[currentCardIndex];
        currentCard.style.transition = 'transform 0.3s ease';

        if (dragMoveX > 100) {
            handleSwipe('right');
        } else if (dragMoveX < -100) {
            handleSwipe('left');
        } else {
            // Return to original position
            currentCard.style.transform = 'translateY(0) rotate(0deg) scale(1)';
            currentCard.querySelector('.like').style.opacity = '0';
            currentCard.querySelector('.dislike').style.opacity = '0';
        }

        dragMoveX = 0;
    });

    function handleSwipeGesture() {
        const deltaX = touchEndX - touchStartX;
        if (deltaX < -100) {
            handleSwipe('left');
        } else if (deltaX > 100) {
            handleSwipe('right');
        } else {
            // Return to original position
            const currentCard = cards[currentCardIndex];
            currentCard.style.transform = 'translateY(0) rotate(0deg) scale(1)';
            currentCard.querySelector('.like').style.opacity = '0';
            currentCard.querySelector('.dislike').style.opacity = '0';
        }
    }
});
