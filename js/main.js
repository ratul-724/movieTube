// code for counter start 
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.count');
    const speed = 200; // The lower the faster
    
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-target');
        // const count = +counter.innerText;
        const increment = target / speed;
        const updateCount = () => {
        const current = +counter.innerText;
        
        if(current < target) {
            counter.innerText = Math.ceil(current + increment);
            setTimeout(updateCount, 1);
        } else {
            counter.innerText = target;
        }
        };
        
        // Start counting when element is in viewport
        const observer = new IntersectionObserver((entries) => {
        if(entries[0].isIntersecting) {
            updateCount();
            observer.unobserve(counter);
        }
        });
        
        observer.observe(counter);
    });
});
// code for counter end 

// for favorite button code satrt here 
document.querySelectorAll('.favorite-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.toggle('active');
        const icon = this.querySelector('i');
        
        if (this.classList.contains('active')) {
            icon.classList.replace('fa-regular', 'fa-solid');
            // Here you would add to favorites (AJAX call to your backend)
            console.log('Added to favorites');
        } else {
            icon.classList.replace('fa-solid', 'fa-regular');
            // Here you would remove from favorites
            console.log('Removed from favorites');
        }
    });
});
// for favorite button code end here 