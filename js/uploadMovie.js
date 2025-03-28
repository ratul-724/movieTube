document.addEventListener('DOMContentLoaded', function() {

    // Only run on upload page
    if (!document.getElementById('movieUploadForm')) return;
    // Add null checks
    if (!yearSelect || !posterInput) {
        console.log('Not on upload page - skipping initialization');
        return;
    }

  // Configuration
  const API_ENDPOINT = 'php/uploadMovie.php';
  const MAX_FILE_SIZE_MB = 5000; // 5GB
  const ALLOWED_MOVIE_TYPES = ['mp4', 'mkv', 'mov', 'avi'];
  const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png'];

  // DOM Elements
  const form = document.getElementById('movieUploadForm');
  const formSections = document.querySelectorAll('.form-section');
  const progressSteps = document.querySelectorAll('.step');
  const yearSelect = document.getElementById('release-year');
  const posterInput = document.getElementById('poster-image');
  const posterPreview = document.querySelector('.image-preview');
  const movieFileInput = document.getElementById('movie-file');
  const movieFileInfo = document.querySelector('.file-info');
  
  let currentSection = 0;
  
  // Initialize year dropdown
  function initYearDropdown() {
      try {
          const currentYear = new Date().getFullYear();
          yearSelect.innerHTML = '<option value="">Select Year</option>';
          
          for (let year = currentYear; year >= 1900; year--) {
              const option = document.createElement('option');
              option.value = year;
              option.textContent = year;
              yearSelect.appendChild(option);
          }
      } catch (error) {
          console.error('Year dropdown init failed:', error);
          showError('Failed to initialize form. Please refresh.');
      }
  }
  
  // File validation
  function isValidFile(file, allowedTypes, maxSizeMB) {
      const extension = file.name.split('.').pop().toLowerCase();
      const isValidType = allowedTypes.includes(extension);
      const isValidSize = file.size <= maxSizeMB * 1024 * 1024;
      
      return {
          valid: isValidType && isValidSize,
          errors: [
              !isValidType && `Invalid file type. Allowed: ${allowedTypes.join(', ')}`,
              !isValidSize && `File too large. Max: ${maxSizeMB}MB`
          ].filter(Boolean)
      };
  }

  // File upload handlers
  function setupFileUploads() {
      // Poster image preview
      posterInput.addEventListener('change', function(e) {
          try {
              if (e.target.files.length > 0) {
                  const file = e.target.files[0];
                  const validation = isValidFile(file, ALLOWED_IMAGE_TYPES, 10);
                  
                  if (!validation.valid) {
                      showError(validation.errors.join(', '));
                      e.target.value = '';
                      return;
                  }

                  const reader = new FileReader();
                  
                  reader.onload = function(event) {
                      posterPreview.innerHTML = `<img src="${event.target.result}" alt="Poster Preview">`;
                      posterPreview.style.display = 'block';
                  };
                  
                  reader.onerror = () => {
                      throw new Error('Failed to read image file');
                  };
                  
                  reader.readAsDataURL(file);
              }
          } catch (error) {
              console.error('Poster upload error:', error);
              showError('Failed to process poster image');
          }
      });
      
      // Movie file info display
      movieFileInput.addEventListener('change', function(e) {
          try {
              if (e.target.files.length > 0) {
                  const file = e.target.files[0];
                  const validation = isValidFile(file, ALLOWED_MOVIE_TYPES, MAX_FILE_SIZE_MB);
                  
                  if (!validation.valid) {
                      showError(validation.errors.join(', '));
                      e.target.value = '';
                      movieFileInfo.textContent = '';
                      return;
                  }

                  const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                  movieFileInfo.textContent = `${file.name} (${fileSize} MB)`;
              }
          } catch (error) {
              console.error('Movie file error:', error);
              showError('Failed to process movie file');
          }
      });
  }

  // Form navigation
  function setupNavigation() {
      document.querySelectorAll('.next-btn').forEach(button => {
          button.addEventListener('click', function() {
              if (validateSection(currentSection)) {
                  switchSection(currentSection + 1);
              }
          });
      });
      
      document.querySelectorAll('.prev-btn').forEach(button => {
          button.addEventListener('click', function() {
              switchSection(currentSection - 1);
          });
      });
  }

  function switchSection(newSection) {
      formSections[currentSection].classList.remove('active');
      progressSteps[currentSection].classList.remove('active');
      
      currentSection = newSection;
      
      formSections[currentSection].classList.add('active');
      progressSteps[currentSection].classList.add('active');
      
      if (currentSection === 3) {
          updateReviewSection();
      }
  }

  // Form validation
  function validateSection(sectionIndex) {
      try {
          const section = formSections[sectionIndex];
          const requiredInputs = section.querySelectorAll('[required]');
          let isValid = true;
          
          requiredInputs.forEach(input => {
              if (!input.value.trim()) {
                  input.classList.add('error');
                  isValid = false;
              } else {
                  input.classList.remove('error');
              }
          });
          
          // Media files validation
          if (sectionIndex === 1) {
              const movieFile = movieFileInput.files[0];
              const posterFile = posterInput.files[0];
              
              if (!movieFile) {
                  movieFileInput.closest('.file-upload-box').classList.add('error');
                  isValid = false;
              } else {
                  movieFileInput.closest('.file-upload-box').classList.remove('error');
              }
              
              if (!posterFile) {
                  posterInput.closest('.file-upload-box').classList.add('error');
                  isValid = false;
              } else {
                  posterInput.closest('.file-upload-box').classList.remove('error');
              }
          }
          
          if (!isValid) {
              showError('Please complete all required fields');
          }
          
          return isValid;
      } catch (error) {
          console.error('Validation error:', error);
          showError('Validation failed. Please check your inputs.');
          return false;
      }
  }

  // Update review section
  function updateReviewSection() {
      try {
          document.getElementById('review-title').textContent = 
              `Title: ${document.getElementById('title').value}`;
          document.getElementById('review-description').textContent = 
              `Description: ${document.getElementById('description').value || 'None'}`;
          document.getElementById('review-year-duration').textContent = 
              `${document.getElementById('release-year').value} | ${document.getElementById('duration').value} minutes`;
          
          const movieFile = movieFileInput.files[0];
          document.getElementById('review-movie-file').textContent = 
              `Movie File: ${movieFile ? movieFile.name : 'None'}`;
          
          const posterFile = posterInput.files[0];
          document.getElementById('review-poster').textContent = 
              `Poster Image: ${posterFile ? posterFile.name : 'None'}`;
          
          document.getElementById('review-trailer').textContent = 
              `Trailer Link: ${document.getElementById('trailer-link').value || 'None'}`;
          
          document.getElementById('review-type-rating').textContent = 
              `Type: ${document.getElementById('movie-type').value} | Rating: ${document.getElementById('rating').value}`;
          
          const selectedGenres = Array.from(document.querySelectorAll('input[name="genres[]"]:checked'))
              .map(checkbox => checkbox.nextElementSibling.textContent)
              .join(', ');
          document.getElementById('review-genres').textContent = 
              `Genres: ${selectedGenres || 'None'}`;
          
          document.getElementById('review-directors').textContent = 
              `Directors: ${document.getElementById('directors').value || 'None'}`;
          document.getElementById('review-cast').textContent = 
              `Cast: ${document.getElementById('cast').value || 'None'}`;
          document.getElementById('review-language').textContent = 
              `Language: ${document.getElementById('language').value}`;
      } catch (error) {
          console.error('Review update failed:', error);
          showError('Failed to generate review');
      }
  }

  // Form submission
  async function handleFormSubmit(e) {
      e.preventDefault();
      
      try {
          if (!validateSection(currentSection)) return;
          if (!document.getElementById('terms-agreement').checked) {
              showError('You must agree to the terms');
              return;
          }

          const submitBtn = form.querySelector('.submit-btn');
          submitBtn.disabled = true;
          submitBtn.textContent = 'Uploading...';
          
          const formData = new FormData(form);
          
          // Add debug info
          console.log('Submitting form with:', {
              title: formData.get('title'),
              files: {
                  movie: formData.get('movie_file').name,
                  poster: formData.get('poster_image').name
              }
          });

          const response = await fetch(API_ENDPOINT, {
              method: 'POST',
              body: formData
          });
          
          if (!response.ok) {
              const error = await response.text();
              throw new Error(error || 'Server error');
          }
          
          const result = await response.json();
          
          if (!result.success) {
              throw new Error(result.message || 'Upload failed');
          }

          alert('Success! Movie submitted for review.');
          resetForm();
      } catch (error) {
          console.error('Upload failed:', {
              error: error,
              message: error.message,
              stack: error.stack
          });
          showError(`Upload failed: ${error.message}`);
      } finally {
          const submitBtn = form.querySelector('.submit-btn');
          if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = 'Submit Movie';
          }
      }
  }

  // Reset form
  function resetForm() {
      form.reset();
      posterPreview.style.display = 'none';
      posterPreview.innerHTML = '';
      movieFileInfo.textContent = '';
      
      // Remove error classes
      document.querySelectorAll('.error').forEach(el => {
          el.classList.remove('error');
      });
      
      switchSection(0);
  }

  // Show error message
  function showError(message) {
      const errorDiv = document.getElementById('error-message') || createErrorElement();
      errorDiv.textContent = message;
      errorDiv.style.display = 'block';
      
      setTimeout(() => {
          errorDiv.style.display = 'none';
      }, 5000);
  }

  function createErrorElement() {
      const errorDiv = document.createElement('div');
      errorDiv.id = 'error-message';
      errorDiv.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          padding: 15px;
          background: #e50914;
          color: white;
          border-radius: 5px;
          display: none;
          z-index: 1000;
      `;
      document.body.appendChild(errorDiv);
      return errorDiv;
  }

  // Initialize everything
  function init() {
      try {
          initYearDropdown();
          setupFileUploads();
          setupNavigation();
          form.addEventListener('submit', handleFormSubmit);
          createErrorElement();
      } catch (error) {
          console.error('Initialization failed:', error);
          showError('System error. Please refresh.');
      }
  }

  init();
});