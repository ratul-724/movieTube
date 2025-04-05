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

// show movie from server code start here 
document.addEventListener('DOMContentLoaded', function () {
    const movieGrid = document.getElementById('movieGrid');

    // Fetch movies from the server
    fetch('php/fetch_approved_movies.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderMovies(data.movies);
        } else {
            movieGrid.innerHTML = `<p class="text-center text-danger">${data.message}</p>`;
        }
    })
    .catch(error => {
        console.error('Error fetching movies:', error);
        movieGrid.innerHTML = `<p class="text-center text-danger">Failed to load movies. Please try again later.</p>`;
    });

    // Render movies
    function renderMovies(approved_movies) {
        movieGrid.innerHTML = ''; 
        approved_movies.forEach(movie => {
            const movieCard = `
                <div class="col-md-2 col-6">
                    <div class="movie-card position-relative">
                        <a href="movie-detail.html?id=${movie.id}">
                            <img src="${movie.poster_link}" alt="${movie.title}" class="w-100">
                            <div class="rating d-flex align-items-center rounded-4">
                                <i class="fa-solid fa-star text-warning"></i>
                                <span class="text-white z-2" id="rating">0</span>
                            </div>
                            <div class="card-body h-100 w-100 text-white d-flex flex-column align-items-center justify-content-center text-center">
                                <h5 class="card-title">${movie.title}</h5>
                                <p class="card-text">${movie.genres}</p>
                                <span class="badge mb-2">HD</span>
                                ${movie.genres.split(',').map(genre => `<span class="badge m-1">${genre}</span>`).join('')}
                            </div>
                        </a>
                        <button class="favorite-btn position-absolute bottom-0 end-0 m-2 bg-transparent border-0 p-2" aria-label="Add to favorites">
                            <i class="fa-regular fa-heart text-white fs-5"></i>
                        </button>
                    </div>
                </div>
            `;
            movieGrid.insertAdjacentHTML('beforeend', movieCard);
        });
    }
});

// show movie from server code end here 