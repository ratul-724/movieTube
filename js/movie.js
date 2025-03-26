document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('movieSliderTrack');
    const slides = document.querySelectorAll('.movie-slide');
    const prevBtn = document.getElementById('sliderPrev');
    const nextBtn = document.getElementById('sliderNext');
    const slideWidth = slides[0].getBoundingClientRect().width + 15; // including gap
    let currentPosition = 0;
    let visibleSlides = 5;
    let autoSlideInterval;
    let isAnimating = false;

    // Update visible slides based on screen size
    function updateVisibleSlides() {
        if (window.innerWidth >= 1400) {
            visibleSlides = 5;
        } else if (window.innerWidth >= 1200) {
            visibleSlides = 4;
        } else if (window.innerWidth >= 768) {
            visibleSlides = 3;
        } else if (window.innerWidth >= 576) {
            visibleSlides = 2;
        } else {
            visibleSlides = 1;
        }
    }
    // Move to specific slide
    function moveToSlide(position, instant = false) {
        if (isAnimating) return;
        isAnimating = true;
        
        if (instant) {
            track.style.transition = 'none';
        } else {
            track.style.transition = 'transform 0.5s ease';
        }
        track.style.transform = `translateX(-${position}px)`;
        currentPosition = position;
        
        if (!instant) {
            setTimeout(() => {
                isAnimating = false;
            }, 500);
        } else {
            isAnimating = false;
        }
    }
    // Next slide (moves by one slide)
    function nextSlide() {
        updateVisibleSlides();
        const maxPosition = (slides.length - visibleSlides) * slideWidth;
        
        if (currentPosition < maxPosition) {
            moveToSlide(currentPosition + slideWidth);
        } else {
            // When reaching end, instantly reset to start without animation
            moveToSlide(0, true);
            // Then move to first slide with animation
            setTimeout(() => {
                moveToSlide(slideWidth);
            }, 50);
        }
    }
    // Previous slide (moves by one slide)
    function prevSlide() {
        updateVisibleSlides();
        const maxPosition = (slides.length - visibleSlides) * slideWidth;
        
        if (currentPosition > 0) {
            moveToSlide(currentPosition - slideWidth);
        } else {
            // When at start, instantly go to end without animation
            moveToSlide(maxPosition, true);
            // Then move to previous slide with animation
            setTimeout(() => {
                moveToSlide(maxPosition - slideWidth);
            }, 50);
        }
    }
    // Initialize auto-slide (2 seconds)
    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, 3000); // Change every 2 seconds
    }
    // Stop auto-slide on hover
    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
    }
    // Event listeners
    nextBtn.addEventListener('click', function() {
        stopAutoSlide();
        nextSlide();
        startAutoSlide();
    });
    prevBtn.addEventListener('click', function() {
        stopAutoSlide();
        prevSlide();
        startAutoSlide();
    });
    track.addEventListener('mouseenter', stopAutoSlide);
    track.addEventListener('mouseleave', startAutoSlide);

    // Initialize
    updateVisibleSlides();
    startAutoSlide();
    
    // Handle window resize
    window.addEventListener('resize', function() {
        updateVisibleSlides();
        // Reset position to nearest valid position
        const maxPosition = Math.max(0, (slides.length - visibleSlides) * slideWidth);
        moveToSlide(Math.min(currentPosition, maxPosition), true);
    });
});



// Add active class to filter options when clicked
document.querySelectorAll('.filter-option').forEach(option => {
    option.addEventListener('click', function(e) {
        if (e.target.tagName !== 'INPUT') {
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            this.classList.toggle('bg-danger', checkbox.checked);
        }
    });
});




// Favorite button functionality start
document.addEventListener('DOMContentLoaded', function() {
    // Initialize from localStorage
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        const movieId = btn.closest('.movie-card').querySelector('img').alt;
        if (localStorage.getItem(`fav_${movieId}`)) {
            btn.classList.add('active');
            btn.querySelector('i').classList.replace('fa-regular', 'fa-solid');
        }
    });

    // Add click handlers
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const icon = this.querySelector('i');
            const movieId = this.closest('.movie-card').querySelector('img').alt;
            
            if (this.classList.contains('active')) {
                // Remove from favorites
                this.classList.remove('active');
                icon.classList.replace('fa-solid', 'fa-regular');
                localStorage.removeItem(`fav_${movieId}`);
                console.log('Removed from favorites:', movieId);
            } else {
                // Add to favorites
                this.classList.add('active');
                icon.classList.replace('fa-regular', 'fa-solid');
                localStorage.setItem(`fav_${movieId}`, 'true');
                console.log('Added to favorites:', movieId);
            }
        });
    });
});
// Favorite button functionality end