// Simple page switching logic
document.addEventListener('DOMContentLoaded', function() {
    // Show login page by default
    document.getElementById('login-page').style.display = 'block';
    
    // Register link click handler
    document.querySelector('.register-link')?.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('login-page').style.display = 'none';
        document.getElementById('register-page').style.display = 'block';
    });
    
    // Login link click handler
    document.querySelector('.login-link')?.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('register-page').style.display = 'none';
        document.getElementById('forgot-password').style.display = 'none';
        document.getElementById('login-page').style.display = 'block';
    });
    
    // Forgot password link click handler
    document.querySelector('a[href="#forgot-password"]')?.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('login-page').style.display = 'none';
        document.getElementById('forgot-password').style.display = 'block';
    });
    
    // Form submission handlers (would be connected to your backend in a real app)
    document.getElementById('loginForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        // Here you would validate and send to your backend
        // For demo, we'll just show the dashboard
        document.getElementById('login-page').style.display = 'none';
        document.getElementById('user-dashboard').style.display = 'block';
    });
    
    document.getElementById('registerForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        // Here you would validate and send to your backend
        // For demo, we'll just show the dashboard
        document.getElementById('register-page').style.display = 'none';
        document.getElementById('user-dashboard').style.display = 'block';
    });
    
    document.getElementById('forgotPasswordForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Password reset link sent to your email!');
        document.getElementById('forgot-password').style.display = 'none';
        document.getElementById('login-page').style.display = 'block';
    });
});
