// js/main.js

// Basic interactivity: Smooth scroll for anchors
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Example AJAX for form submissions (e.g., integrate with backend PHP)
// For login/register, you can add in login.php/register.php similarly
// This is a placeholder for submitting a service request form if added
function submitRequest(formData) {
    $.ajax({
        url: 'requests.php',
        type: 'POST',
        data: formData,
        success: function(response) {
            alert('Request submitted successfully!');
        },
        error: function() {
            alert('Error submitting request.');
        }
    });
}

// Add more JS for tracking status, reviews, etc., by fetching from reviews.php, requests.php via AJAX