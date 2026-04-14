/**
 * Event form validation
 * Handles fields: name, id_type, date, location, number_of_participants
 */
(function() {
    function initValidation() {
        const form = document.getElementById('eventForm') || document.querySelector('form[action*="/back/Event/"]');
        
        if (!form) {
            console.warn('Event validation script loaded but no event form found.');
            return;
        }

        console.log('Event validation active on form:', form.action);

        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Clean up old errors
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            form.querySelectorAll('input, select').forEach(el => el.style.borderColor = '#ccc');

            function showError(input, message) {
                const error = document.createElement('p');
                error.className = 'error-message';
                error.style.color = '#e74c3c';
                error.style.fontSize = '12px';
                error.style.marginTop = '4px';
                error.innerText = message;
                
                input.parentNode.appendChild(error);
                input.style.borderColor = '#e74c3c';
                
                // Clear error on input
                const clearError = () => {
                    input.style.borderColor = '#ccc';
                    if (error.parentNode) error.remove();
                };
                input.addEventListener('input', clearError, { once: true });
                input.addEventListener('change', clearError, { once: true });
                
                isValid = false;
            }

            // 1. Name: Alphabetic only, min 3 chars
            const name = form.querySelector('[name="name"]');
            if (name) {
                const val = name.value.trim();
                if (val.length < 3) {
                    showError(name, 'Name must be at least 3 characters.');
                } else if (!/^[A-Za-zÀ-ÿ\s]+$/.test(val)) {
                    showError(name, 'Name must contain only letters and spaces.');
                }
            }

            // 2. Type: Must be selected (dropdown)
            const idType = form.querySelector('[name="id_type"]');
            if (idType) {
                if (idType.value === "") {
                    showError(idType, 'Please select an event type.');
                }
            }

            // 3. Date: Future or today
            const date = form.querySelector('[name="date"]');
            if (date) {
                const val = date.value;
                if (!val) {
                    showError(date, 'Please select a date.');
                } else {
                    const d = new Date(val);
                    const today = new Date();
                    today.setHours(0,0,0,0);
                    if (d < today) {
                        showError(date, 'Date cannot be in the past.');
                    }
                }
            }

            // 4. Location: Min 3 chars
            const location = form.querySelector('[name="location"]');
            if (location) {
                if (location.value.trim().length < 3) {
                    showError(location, 'Location must be at least 3 characters.');
                }
            }

            // 5. Participants: Positive number
            const participants = form.querySelector('[name="number_of_participants"]');
            if (participants) {
                if (parseInt(participants.value) <= 0 || isNaN(parseInt(participants.value))) {
                    showError(participants, 'Participants must be at least 1.');
                }
            }

            if (!isValid) {
                e.preventDefault();
                console.log('Event submission blocked by validation');
            }
        });
    }

    // Try to init immediately and on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initValidation);
    } else {
        initValidation();
    }
})();
