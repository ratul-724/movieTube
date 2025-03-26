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

// Toggle Search Bar
function toggleSearch() {
    const searchBar = document.getElementById('searchBar');
    if (searchBar.style.display === 'none' || searchBar.style.display === '') {
        searchBar.style.display = 'block';
    } else {
        searchBar.style.display = 'none';
    }
}

const searchInput = document.getElementById('search-input');
const clearBtn = document.getElementById('clear-btn');
// Show clear button when typing
searchInput.addEventListener('input', function() {
    clearBtn.style.display = this.value ? 'block' : 'none';
});
// Clear input when X is clicked
clearBtn.addEventListener('click', function() {
    searchInput.value = '';
    clearBtn.style.display = 'none';
    searchInput.focus();
});
// code for nav bar end

// back to top button code start 
document.addEventListener('DOMContentLoaded', function() {
  const backToTopButton = document.getElementById('backToTop');
  // Show button when scrolling down
  window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
      backToTopButton.classList.add('show');
    } else {
      backToTopButton.classList.remove('show');
    }
  });
  // Smooth scroll to top
  backToTopButton.addEventListener('click', function(e) {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
});
// back to top button code end 



