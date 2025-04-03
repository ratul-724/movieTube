$(document).ready(function() {
    // Populate year dropdown
    const currentYear = new Date().getFullYear();
    const yearSelect = $('#release-year');
    for (let year = currentYear; year >= 1900; year--) {
        yearSelect.append(`<option value="${year}">${year}</option>`);
    }

    // Form navigation
    let currentStep = 1;
    const totalSteps = 4;

    $('.next-btn').click(function() {
        if (validateStep(currentStep)) {
            currentStep++;
            updateForm();
        }
    });

    $('.prev-btn').click(function() {
        currentStep--;
        updateForm();
    });

    function updateForm() {
        $('.form-section').removeClass('active');
        $(`#${getStepId(currentStep)}`).addClass('active');
        
        // Update progress bar
        $('.step').removeClass('active');
        $(`.step:nth-child(${currentStep})`).addClass('active');
        
        // Update review section if we're on the last step
        if (currentStep === 4) {
            updateReviewSection();
        }
    }

    function getStepId(step) {
        switch(step) {
            case 1: return 'basic-info';
            case 2: return 'media-details';
            case 3: return 'additional-info';
            case 4: return 'review-submit';
            default: return 'basic-info';
        }
    }

    function validateStep(step) {
        let isValid = true;
        
        if (step === 1) {
            if (!$('#title').val()) {
                $('#title').focus();
                alert('Please enter a movie title');
                isValid = false;
            } else if (!$('#release-year').val()) {
                $('#release-year').focus();
                alert('Please select a release year');
                isValid = false;
            } else if (!$('#minutes').val() && !$('#hours').val()) {
                $('#minutes').focus();
                alert('Please enter duration (at least minutes)');
                isValid = false;
            }
        } else if (step === 2) {
            if (!$('#drive-link').val()) {
                $('#drive-link').focus();
                alert('Please enter Google Drive link');
                isValid = false;
            } else if (!$('#poster-link').val()) {
                $('#poster-link').focus();
                alert('Please enter poster image URL');
                isValid = false;
            }
        } else if (step === 3) {
            if (!$('#movie-type').val()) {
                $('#movie-type').focus();
                alert('Please select movie type');
                isValid = false;
            } else if (!$('#quality').val()) {
                $('#quality').focus();
                alert('Please select quality');
                isValid = false;
            } else if ($('input[name="genres[]"]:checked').length === 0) {
                $('input[name="genres[]"]').first().focus();
                alert('Please select at least one genre');
                isValid = false;
            } else if (!$('#language').val()) {
                $('#language').focus();
                alert('Please select language');
                isValid = false;
            }
        }
        
        return isValid;
    }

    function updateReviewSection() {
        // Basic Info
        $('#review-title').html(`<strong>Title:</strong> ${$('#title').val()}`);
        $('#review-description').html(`<strong>Description:</strong> ${$('#description').val() || 'Not provided'}`);
        $('#review-year').html(`<strong>Release Year:</strong> ${$('#release-year').val()}`);
        
        const hours = $('#hours').val() || 0;
        const minutes = $('#minutes').val() || 0;
        $('#review-duration').html(`<strong>Duration:</strong> ${formatDuration(hours, minutes)}`);
        
        // Media Details
        $('#review-drive-link').html(`<strong>Drive Link:</strong> <a href="${$('#drive-link').val()}" target="_blank">View Link</a>`);
        $('#review-poster').html(`<strong>Poster:</strong> <a href="${$('#poster-link').val()}" target="_blank">View Image</a>`);
        
        if ($('#trailer-link').val()) {
            $('#review-trailer').html(`<strong>Trailer:</strong> <a href="${$('#trailer-link').val()}" target="_blank">Watch Trailer</a>`);
        } else {
            $('#review-trailer').html('<strong>Trailer:</strong> Not provided');
        }
        
        // Additional Info
        $('#review-type-quality').html(`<strong>Type/Quality:</strong> ${$('#movie-type').find('option:selected').text()} | ${$('#quality').find('option:selected').text()}`);
        
        const genres = [];
        $('input[name="genres[]"]:checked').each(function() {
            genres.push($(this).val());
        });
        $('#review-genres').html(`<strong>Genres:</strong> ${genres.join(', ')}`);
        
        $('#review-directors').html(`<strong>Directors:</strong> ${$('#directors').val() || 'Not provided'}`);
        $('#review-cast').html(`<strong>Cast:</strong> ${$('#cast').val() || 'Not provided'}`);
        $('#review-language').html(`<strong>Language:</strong> ${$('#language').find('option:selected').text()}`);
    }

    function formatDuration(hours, minutes) {
        hours = hours || 0;
        minutes = minutes || 0;
        
        let duration = '';
        if (hours > 0) duration += `${hours}h `;
        if (minutes > 0) duration += `${minutes}min`;
        return duration || '0min';
    }

    // Form submission
    $('#movieUploadForm').submit(function(e) {
        e.preventDefault();
        
        if (!validateStep(4)) return;
        if (!$('#terms-agreement').is(':checked')) {
            alert('Please agree to the terms of service');
            return;
        }
        
        // Get duration values
        const hours = parseInt($('#hours').val()) || 0;
        const minutes = parseInt($('#minutes').val()) || 0;
        const durationFormatted = formatDuration(hours, minutes);
        const durationMinutes = (hours * 60) + minutes;
        
        // Add to form data
        const formData = $(this).serializeArray();
        formData.push({name: 'duration_formatted', value: durationFormatted});
        formData.push({name: 'duration_minutes', value: durationMinutes});
        
        // Submit via AJAX
        $('.submit-btn').html('<i class="fas fa-spinner fa-spin me-2"></i> Submitting...').prop('disabled', true);
        
        $.ajax({
            url: 'php/uploadMovie.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    // Reset form
                    $('#movieUploadForm')[0].reset();
                    $('.step').removeClass('active');
                    $('.step:first').addClass('active');
                    $('.form-section').removeClass('active');
                    $('#basic-info').addClass('active');
                    currentStep = 1;
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Request failed: ';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage += response.message || error;
                } catch (e) {
                    errorMessage += xhr.responseText || error;
                }
                alert(errorMessage);
            },
            complete: function() {
                $('.submit-btn').html('<i class="fas fa-upload me-2"></i> Submit Movie').prop('disabled', false);
            }
        });
    });
});
