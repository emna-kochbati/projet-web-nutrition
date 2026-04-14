document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('eventTypeForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Remove existing errors
            const existingErrors = form.querySelectorAll('.error-message');
            existingErrors.forEach(err => err.remove());
            
            const labelInput = form.querySelector('[name="label"]');
            
            if (labelInput) {
                const val = labelInput.value.trim();
                
                // Alphabetic only check
                if (val.length < 3) {
                    showError(labelInput, 'Label must be at least 3 characters long.');
                } else if (!/^[A-Za-zÀ-ÿ\s]+$/.test(val)) {
                    showError(labelInput, 'Label must contain ONLY letters and spaces.');
                }
            }

            function showError(input, message) {
                const error = document.createElement('p');
                error.className = 'error-message';
                error.style.color = '#e74c3c';
                error.style.fontSize = '12px';
                error.style.marginTop = '5px';
                error.innerText = message;
                input.parentNode.appendChild(error);
                input.style.borderColor = '#e74c3c';
                
                input.addEventListener('input', function() {
                    input.style.borderColor = '#ccc';
                    error.remove();
                }, { once: true });
                
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});
