document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('register-form');
    const errorContainer = document.getElementById('js-error-container');

    if (form) {
        form.addEventListener('submit', function (e) {
            let errors = [];
            const shopName = form.querySelector('[name="shop_name"]').value.trim();
            const username = form.querySelector('[name="username"]').value.trim();
            const email = form.querySelector('[name="email"]').value.trim();
            const phone = form.querySelector('[name="phone"]').value.trim();
            const password = form.querySelector('[name="password"]').value;

            if (shopName.length < 3) {
                errors.push("Shop name must be at least 3 characters and cannot be empty or just spaces");
            }

            if (username === '') {
                errors.push("Owner name cannot be empty or just spaces");
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                errors.push("Invalid email address");
            }

            if (phone.length < 10) {
                errors.push("Phone number must be at least 10 digits");
            }

            if (password.length < 6) {
                errors.push("Password must be at least 6 characters");
            }

            if (errors.length > 0) {
                e.preventDefault();
                errorContainer.innerHTML = errors.join('<br>');
                errorContainer.style.display = 'block';
                errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                errorContainer.style.display = 'none';
            }
        });
    }
});
