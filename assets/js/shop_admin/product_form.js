document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('product-form');
    const errorContainer = document.getElementById('js-error-container');

    if (form) {
        form.addEventListener('submit', function (e) {
            let errors = [];

            const name = form.querySelector('[name="name"]').value.trim();
            const categoryId = form.querySelector('[name="category_id"]').value;
            const brandId = form.querySelector('[name="brand_id"]').value;
            const buyPrice = parseFloat(form.querySelector('[name="buy_price"]').value);
            const sellPrice = parseFloat(form.querySelector('[name="sell_price"]').value);
            const stockQty = parseFloat(form.querySelector('[name="stock_qty"]').value);
            const alertThreshold = parseFloat(form.querySelector('[name="alert_threshold"]').value);

            if (name === '') {
                errors.push("Product Name cannot be empty or just spaces.");
            }
            if (categoryId === "") {
                errors.push("Please select a Category.");
            }
            if (brandId === "") {
                errors.push("Please select a Brand.");
            }

            if (buyPrice < 0) {
                errors.push("Buy Price must be a non-negative number.");
            }
            if (sellPrice < 0) {
                errors.push("Sell Price must be a non-negative number.");
            }
            if (stockQty < 0) {
                errors.push("Current Stock must be a non-negative number.");
            }
            if (alertThreshold < 0) {
                errors.push("Low Stock Alert Filter must be a non-negative number.");
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
