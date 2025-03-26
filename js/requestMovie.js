// Form submission handling
document.getElementById('movieRequestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form values
    const movieName = document.getElementById('movieName').value;
    const releaseYear = document.getElementById('releaseYear').value;
    const movieType = document.getElementById('movieType').value;
    const additionalNotes = document.getElementById('additionalNotes').value;
    const userEmail = document.getElementById('userEmail').value;
    
    // Get selected qualities
    const qualities = [];
    document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
        qualities.push(checkbox.value);
    });
    
    // Here you would typically send this data to your server
    console.log('Request submitted:', {
        movieName,
        releaseYear,
        movieType,
        qualities,
        additionalNotes,
        userEmail
    });
    
    // Show success message (in a real app, you'd want something more sophisticated)
    alert('Your request has been submitted successfully! We\'ll notify you when it\'s available.');
    
    // Reset form
    this.reset();
});
