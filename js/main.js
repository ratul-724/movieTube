
document.addEventListener('DOMContentLoaded', function() {
const counters = document.querySelectorAll('.count');
const speed = 100; // The lower the faster

counters.forEach(counter => {
    const target = +counter.getAttribute('data-target');
    const count = +counter.innerText;
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