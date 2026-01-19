document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('password-form');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const errorContainer = document.getElementById('js-error-container');

    if (form) {
        form.addEventListener('submit', function (e) {
            errorContainer.textContent = '';
            errorContainer.style.display = 'none';

            if (newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                errorContainer.textContent = 'New passwords do not match.';
                errorContainer.style.display = 'block';

                errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
});
