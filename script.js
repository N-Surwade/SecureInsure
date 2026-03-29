// JavaScript for Insurance Policy Management System
// Form validation and interactive features

// Form validation function
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            showError(input, 'This field is required');
            isValid = false;
        } else {
            clearError(input);
        }

        // Email validation
        if (input.type === 'email' && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                showError(input, 'Please enter a valid email address');
                isValid = false;
            }
        }

        // Phone validation
        if (input.name === 'phone' && input.value) {
            const phoneRegex = /^\d{10,15}$/;
            if (!phoneRegex.test(input.value.replace(/\D/g, ''))) {
                showError(input, 'Please enter a valid phone number (10-15 digits)');
                isValid = false;
            }
        }
    });

    return isValid;
}

// Show error message
function showError(input, message) {
    clearError(input);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    errorDiv.style.color = 'red';
    errorDiv.style.fontSize = '0.8rem';
    errorDiv.style.marginTop = '0.25rem';
    input.parentNode.appendChild(errorDiv);
    input.style.borderColor = 'red';
}

// Clear error message
function clearError(input) {
    const errorDiv = input.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
    input.style.borderColor = '#ccc';
}

// Modal functionality for policy details
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}



// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Form validation on submit
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form.id)) {
                e.preventDefault();
            }
        });
    });

    // AJAX Claim Form Handler
    const claimForm = document.getElementById('claim-form');
    if (claimForm) {
        claimForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('claim-submit');
            const resultDiv = document.getElementById('claim-result');
            const formData = new FormData(claimForm);
            
            // Show loading
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;
            claimForm.classList.add('loading');
            resultDiv.innerHTML = '';
            
            fetch('process_claim.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.innerHTML = `<div class="${data.success ? 'success-message' : 'error-message'}">${data.message}${data.success && data.claim_id ? ' Claim ID: ' + data.claim_id : ''}</div>`;
            })
            .catch(error => {
                resultDiv.innerHTML = `<div class="error-message">Network error. Please try again.</div>`;
            })
            .finally(() => {
                submitBtn.textContent = 'Submit Claim';
                submitBtn.disabled = false;
                claimForm.classList.remove('loading');
            });
        });
    }

    // Close modal buttons
    const closeButtons = document.querySelectorAll('.close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            modal.style.display = 'none';
        });
    });
});
