// code for nav bar start
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const closeDrawerBtn = document.querySelector('.close-drawer');
    const drawer = document.querySelector('.mobile-drawer');
    // Open drawer
    mobileMenuBtn.addEventListener('click', function() {
        drawer.classList.add('show');
        document.body.style.overflow = 'hidden';
    });
    // Close drawer
    function closeDrawer() {
        drawer.classList.remove('show');
        document.body.style.overflow = '';
    }
    closeDrawerBtn.addEventListener('click', closeDrawer);
});
// code for nav bar end

// back to top button code start 
document.addEventListener('DOMContentLoaded', function() {
  const backToTopButton = document.getElementById('backToTop');
  
  // Show/hide button based on scroll position
  window.addEventListener('scroll', function() {
    if (window.pageYOffset > 200) {
      backToTopButton.style.visibility = 'visible'; 
      backToTopButton.style.opacity = '1'; 
      backToTopButton.style.transform = 'translateY(0)'; 
    } else {
      backToTopButton.style.visibility = 'hidden'; 
      backToTopButton.style.opacity = '0'; 
      backToTopButton.style.transform = 'translateY(20px)'; 

    }
  });
  
  // Smooth scroll to top when clicked
  backToTopButton.addEventListener('click', function(e) {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
});
// back to top button code end 