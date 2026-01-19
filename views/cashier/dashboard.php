<?php
require_once '../../includes/db.php';
requireRole('cashier');

$cats = $db->query("SELECT * FROM categories WHERE shop_id = ?", [$_SESSION['shop_id']], "i")->get_result()->fetch_all(MYSQLI_ASSOC);
$brands = $db->query("SELECT * FROM brands WHERE shop_id = ?", [$_SESSION['shop_id']], "i")->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>POS Terminal</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .pos-grid {
            display: grid;
            grid-template-columns: 2.5fr 1fr;
            gap: 1.5rem;
            height: calc(100vh - 4rem);
            min-height: 0;
        }

        .product-area {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            height: 100%;
            overflow: hidden;
        }

        .cart-area {
            background: var(--bg-card);
            border-radius: 0.75rem;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255, 255, 255, 0.05);
            height: 100%;
            overflow: hidden;
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .cart-footer {
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .search-results {
            flex: 1;
            min-height: 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            overflow-y: auto;
            padding-right: 5px;
        }

        @media (min-width: 1200px) {
            .search-results {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .product-card {
            background: var(--bg-card);
            padding: 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: transform 0.1s;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
        }

        .product-card:active {
            transform: scale(0.98);
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        ] @media (max-width: 1024px) {
            .pos-grid {
                grid-template-columns: 1fr;
                height: auto;
                display: flex;
                flex-direction: column;
            }

            body {
                overflow: auto;
            }

            .product-area,
            .cart-area {
                height: auto;
                overflow: visible;
            }

            .search-results {
                min-height: 50vh;
                overflow-y: visible;
            }
        }
    </style>
</head>

<body>
    <div style="padding: 1rem; height: 100vh; overflow: hidden; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; align-items: center;">
            <div style="font-weight: bold; font-size: 1.25rem;">
                <a href="dashboard.php" style="color: white; text-decoration: none;">&larr; POS Terminal</a>
            </div>
            <div>
                Cashier: <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>
                <button onclick="showHeldOrders()"
                    style="margin-left: 1rem; padding: 0.5rem; background: var(--secondary); border: none; border-radius: 0.25rem; cursor: pointer; color: white;">Recall
                    Order</button>
                <a href="return.php"
                    style="margin-left: 0.5rem; padding: 0.5rem; background: #6366f1; border: none; border-radius: 0.25rem; text-decoration: none; color: white; display: inline-block;">Returns</a>
                <a href="../../logout.php"
                    style="margin-left: 0.5rem; padding: 0.5rem; background: #ef4444; border: none; border-radius: 0.25rem; text-decoration: none; color: white; display: inline-block;">Logout</a>
            </div>
        </div>

        <div class="pos-grid">
            <div class="product-area">
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <select id="catFilter" class="form-input" style="flex: 1;" onchange="applyFilters()">
                        <option value="">All Categories</option>
                        <?php foreach ($cats as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select id="brandFilter" class="form-input" style="flex: 1;" onchange="applyFilters()">
                        <option value="">All Brands</option>
                        <?php foreach ($brands as $b): ?>
                            <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['name']); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select id="sortFilter" class="form-input" style="flex: 1;" onchange="applyFilters()">
                        <option value="">Sort: Newest</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                        <option value="name_asc">Name: A-Z</option>
                    </select>
                </div>

                <input type="text" id="searchInput" class="form-input" placeholder="Scan Barcode or Search Product..."
                    autofocus>

                <div id="searchResults" class="search-results">
                    <!-- Products injected here -->
                    <div style="grid-column: 1/-1; text-align: center; color: var(--text-gray); margin-top: 2rem;">
                        Start typing to search...
                    </div>
                </div>
            </div>

            <div class="cart-area">
                <div class="cart-items" id="cartItems">

                </div>

                <div class="cart-footer">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Subtotal</span>
                        <span id="subTotal">৳0.00</span>
                    </div>

                    <div
                        style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: bold; margin-bottom: 1rem;">
                        <span>Total</span>
                        <span id="grandTotal">৳0.00</span>
                    </div>

                    <div style="margin-bottom: 1rem; display: flex; gap: 0.5rem;" id="voucherSection">
                        <input type="text" id="voucherCode" class="form-input" placeholder="Voucher Code"
                            style="padding: 0.5rem;">
                        <button onclick="applyVoucher()" class="btn-primary"
                            style="width: auto; padding: 0.5rem 1rem;">Apply</button>
                    </div>
                    <div id="appliedVoucher"
                        style="display: none; justify-content: space-between; color: var(--success); margin-bottom: 1rem; background: rgba(16, 185, 129, 0.1); padding: 0.5rem; border-radius: 4px;">
                        <span id="voucherName"></span>
                        <button onclick="removeVoucher()"
                            style="background: none; border: none; color: #ef4444; cursor: pointer;">&times;</button>
                    </div>

                    <button onclick="processCheckout()" class="btn-primary" style="margin-bottom: 0.5rem;">Proceed to
                        Payment</button>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                        <button onclick="holdOrder()"
                            style="padding: 0.75rem; background: #f59e0b; color: white; border: none; border-radius: 0.5rem; cursor: pointer;">Hold
                            Order</button>
                        <button onclick="clearCart()"
                            style="padding: 0.75rem; background: rgba(239, 68, 68, 0.2); color: #f87171; border: none; border-radius: 0.5rem; cursor: pointer;">Clear
                            Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="heldModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: var(--bg-card); padding: 2rem; border-radius: 0.75rem; width: 500px; max-width: 90%;">
            <h2 style="margin-bottom: 1rem;">Held Orders</h2>
            <div id="heldList"
                style="max-height: 400px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.75rem;"></div>
            <button onclick="document.getElementById('heldModal').style.display='none'"
                style="margin-top: 1rem; width: 100%; padding: 0.75rem; background: var(--text-gray); border: none; border-radius: 0.25rem; cursor: pointer;">Close</button>
        </div>
    </div>

    <script>
        function ajaxRequest(url, options = {}) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                const method = options.method || 'GET';
                xhr.open(method, url);

                if (options.headers) {
                    for (let key in options.headers) {
                        xhr.setRequestHeader(key, options.headers[key]);
                    }
                }

                xhr.onload = () => {
                    resolve({
                        ok: xhr.status >= 200 && xhr.status < 300,
                        status: xhr.status,
                        json: () => Promise.resolve(JSON.parse(xhr.responseText)),
                        text: () => Promise.resolve(xhr.responseText)
                    });
                };
                xhr.onerror = () => reject(new Error('Network Error'));

                xhr.send(options.body || null);
            });
        }

        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const cartItems = document.getElementById('cartItems');
        let cart = [];

        fetchCart();
        fetchProducts('');

        searchInput.addEventListener('input', (e) => {
            applyFilters();
        });

        function applyFilters() {
            fetchProducts(searchInput.value);
        }

        function fetchProducts(query) {
            const cat = document.getElementById('catFilter').value;
            const brand = document.getElementById('brandFilter').value;
            const sort = document.getElementById('sortFilter').value;

            ajaxRequest(`../../api/search_products.php?q=${query}&category_id=${cat}&brand_id=${brand}&sort=${sort}`)
                .then(res => res.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length === 0) {
                        searchResults.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--text-gray);">No products found</div>';
                        return;
                    }
                    data.forEach(prod => {
                        const div = document.createElement('div');
                        div.className = 'product-card';

                        let imgHtml = '';
                        if (prod.image) {
                            imgHtml = `<img src="../../uploads/${prod.image}" style="width: 100%; height: 120px; object-fit: cover; border-radius: 4px; margin-bottom: 0.5rem;">`;
                        } else {
                            imgHtml = `<div style="width: 100%; height: 120px; background: rgba(255,255,255,0.05); border-radius: 4px; margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: center; color: #666;">No Img</div>`;
                        }

                        div.innerHTML = `
                            ${imgHtml}
                            <div style="font-weight: bold; margin-bottom: 0.25rem;">${prod.name}</div>
                            <div style="color: var(--primary);">৳${prod.sell_price}</div>
                            <div style="font-size: 0.75rem; color: var(--text-gray); margin-bottom: 0.5rem;">Stock: ${prod.stock_qty}</div>
                            <button class="btn-primary" style="width: 100%; padding: 0.5rem; font-size: 0.9rem;">Add to Cart</button>
                        `;
                        div.onclick = (e) => {
                            // Simple click handler
                            addToCart(prod.id);
                        };
                        searchResults.appendChild(div);
                    });

                    if (data.length === 1 && query.length > 0 && query === data[0].id.toString()) {
                        addToCart(data[0].id);
                        searchInput.value = '';
                        fetchProducts('');
                    }
                });
        }

        function addToCart(id) {
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('id', id);

            ajaxRequest('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    cart = data.cart;
                    renderCart();
                });
        }

        function fetchCart() {
            ajaxRequest('../../api/cart_actions.php')
                .then(res => res.json())
                .then(data => {
                    cart = data.cart;
                    renderCart();
                });
        }

        function updateQty(id, qty) {
            const formData = new FormData();
            formData.append('action', 'update_qty');
            formData.append('id', id);
            formData.append('qty', qty);
            ajaxRequest('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    cart = data.cart;
                    renderCart();
                });
        }

        function clearCart() {
            const formData = new FormData();
            formData.append('action', 'clear');
            ajaxRequest('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    cart = data.cart;
                    renderCart();
                });
        }

        function renderCart() {
            cartItems.innerHTML = '';
            let subtotal = 0;

            cart.forEach(item => {
                subtotal += item.price * item.qty;
                const div = document.createElement('div');
                div.className = 'cart-item';
                div.innerHTML = `
                    <div>
                        <div style="font-weight: 500;">${item.name}</div>
                        <div style="font-size: 0.85rem; color: var(--text-gray);">$${item.price} x ${item.qty}</div>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <span style="font-weight: bold;">৳${(item.price * item.qty).toFixed(2)}</span>
                        <div style="display: flex; gap: 0.25rem;">
                             <button onclick="updateQty(${item.id}, ${item.qty - 1})" style="width: 24px; height: 24px;">-</button>
                             <button onclick="updateQty(${item.id}, ${item.qty + 1})" style="width: 24px; height: 24px;">+</button>
                        </div>
                    </div>
                `;
                cartItems.appendChild(div);
            });

            document.getElementById('subTotal').innerText = '৳' + subtotal.toFixed(2);

            let total = subtotal;
            if (discount) {
                document.getElementById('voucherSection').style.display = 'none';
                document.getElementById('appliedVoucher').style.display = 'flex';
                document.getElementById('voucherName').innerText = `Voucher (${discount.code}): -$${parseFloat(discount.amount).toFixed(2)}`;
                total -= parseFloat(discount.amount);
            } else {
                document.getElementById('voucherSection').style.display = 'flex';
                document.getElementById('appliedVoucher').style.display = 'none';
            }

            document.getElementById('grandTotal').innerText = '৳' + Math.max(0, total).toFixed(2);
        }

        function processCheckout() {
            if (cart.length === 0) {
                alert("Cart is empty");
                return;
            }
            window.location.href = 'checkout.php';
        }
        function holdOrder() {
            if (cart.length === 0) return;
            const customer = prompt("Customer Name (Optional):") || "Walk-in";

            const formData = new FormData();
            formData.append('action', 'hold');
            formData.append('customer', customer);

            ajaxRequest('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    cart = data.cart;
                    renderCart();
                });
        }

        function showHeldOrders() {
            const formData = new FormData();
            formData.append('action', 'list_held');

            ajaxRequest('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById('heldList');
                    list.innerHTML = '';
                    if (data.held.length === 0) {
                        list.innerHTML = '<div style="text-align:center;">No held orders</div>';
                    }
                    data.held.forEach(order => {
                        const div = document.createElement('div');
                        div.style.padding = '0.75rem';
                        div.style.background = 'rgba(255,255,255,0.05)';
                        div.style.borderRadius = '0.5rem';
                        div.style.display = 'flex';
                        div.style.justifyContent = 'space-between';
                        div.style.alignItems = 'center';
                        div.innerHTML = `
                            <div>
                                <div style="font-weight: bold;">${order.customer_name}</div>
                                <div style="font-size: 0.8rem; color: var(--text-gray);">${order.items_count} items - ${new Date(order.created_at).toLocaleTimeString()}</div>
                            </div>
                            <button onclick="recallOrder(${order.id})" class="btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Recall</button>
                        `;
                        list.appendChild(div);
                    });
                    document.getElementById('heldModal').style.display = 'flex';
                });
        }

        function recallOrder(id) {
            console.log("Recalling Order ID:", id);
            const formData = new FormData();
            formData.append('action', 'recall');
            formData.append('id', id);

            ajaxRequest('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    console.log("Recall Response:", data);
                    if (data.success) {
                        cart = data.cart;
                        discount = data.discount; // Update global discount
                        renderCart();
                        document.getElementById('heldModal').style.display = 'none';
                    } else {
                        alert("Failed to recall order. It may have been removed or ID is invalid.");
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Error processing recall.");
                });
        }

        let discount = null; // Global discount state

        function applyVoucher() {
            const code = document.getElementById('voucherCode').value;
            if (!code) return;

            const formData = new FormData();
            formData.append('action', 'apply_voucher');
            formData.append('code', code);

            ajaxRequest('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        discount = data.discount;
                        renderCart();
                        alert("Voucher Applied: -৳" + discount.amount);
                    } else {
                        alert(data.message);
                    }
                });
        }

        function removeVoucher() {
            const formData = new FormData();
            formData.append('action', 'remove_voucher');
            ajaxRequest('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    discount = null;
                    renderCart();
                });
        }

        const originalFetchCart = fetchCart;
        fetchCart = function () {
            ajaxRequest('../../api/cart_actions.php')
                .then(res => res.json())
                .then(data => {
                    cart = data.cart;
                    discount = data.discount;
                    renderCart();
                });
        }

    </script>
</body>

</html>