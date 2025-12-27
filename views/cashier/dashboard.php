<?php
require_once '../../includes/db.php';
requireRole('cashier');
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
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            height: calc(100vh - 4rem); /* Adjust for header if any */
        }
        .product-area {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .cart-area {
            background: var(--bg-card);
            border-radius: 0.75rem;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }
        .cart-footer {
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .search-results {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            overflow-y: auto;
        }
        .product-card {
            background: var(--bg-card);
            padding: 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: transform 0.1s;
            border: 1px solid rgba(255,255,255,0.05);
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
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
    </style>
</head>
<body>
    <div style="padding: 1rem; height: 100vh; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; align-items: center;">
            <div style="font-weight: bold; font-size: 1.25rem;">
                <a href="dashboard.php" style="color: white; text-decoration: none;">&larr; POS Terminal</a>
            </div>
            <div>
                Cashier: <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>
                <button onclick="showHeldOrders()" style="margin-left: 1rem; padding: 0.5rem; background: var(--secondary); border: none; border-radius: 0.25rem; cursor: pointer; color: white;">Recall Order</button>
                <a href="return.php" style="margin-left: 0.5rem; padding: 0.5rem; background: #6366f1; border: none; border-radius: 0.25rem; text-decoration: none; color: white; display: inline-block;">Returns</a>
            </div>
        </div>

        <div class="pos-grid">
            <!-- Left: Search & Products -->
            <div class="product-area">
                <input type="text" id="searchInput" class="form-input" placeholder="Scan Barcode or Search Product..." autofocus>
                
                <div id="searchResults" class="search-results">
                    <!-- Products injected here -->
                    <div style="grid-column: 1/-1; text-align: center; color: var(--text-gray); margin-top: 2rem;">
                        Start typing to search...
                    </div>
                </div>
            </div>

            <!-- Right: Cart -->
            <div class="cart-area">
                <div class="cart-items" id="cartItems">
                    <!-- Cart items injected here -->
                </div>
                
                <div class="cart-footer">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Subtotal</span>
                        <span id="subTotal">$0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Tax (5%)</span>
                        <span id="taxAmount">$0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: bold; margin-bottom: 1rem;">
                        <span>Total</span>
                        <span id="grandTotal">$0.00</span>
                    </div>
                    
                    <button onclick="processCheckout()" class="btn-primary" style="margin-bottom: 0.5rem;">Proceed to Payment</button>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                        <button onclick="holdOrder()" style="padding: 0.75rem; background: #f59e0b; color: white; border: none; border-radius: 0.5rem; cursor: pointer;">Hold Order</button>
                        <button onclick="clearCart()" style="padding: 0.75rem; background: rgba(239, 68, 68, 0.2); color: #f87171; border: none; border-radius: 0.5rem; cursor: pointer;">Clear Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Held Orders Modal -->
    <div id="heldModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: var(--bg-card); padding: 2rem; border-radius: 0.75rem; width: 500px; max-width: 90%;">
            <h2 style="margin-bottom: 1rem;">Held Orders</h2>
            <div id="heldList" style="max-height: 400px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.75rem;"></div>
            <button onclick="document.getElementById('heldModal').style.display='none'" style="margin-top: 1rem; width: 100%; padding: 0.75rem; background: var(--text-gray); border: none; border-radius: 0.25rem; cursor: pointer;">Close</button>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const cartItems = document.getElementById('cartItems');
        let cart = [];

        // Init
        fetchCart();

        // Search Listener
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value;
            if (query.length < 1) {
                searchResults.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--text-gray);">Start typing...</div>';
                return;
            }

            fetch(`../../api/search_products.php?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if(data.length === 0) {
                        searchResults.innerHTML = '<div style="grid-column: 1/-1; text-align: center;">No products found</div>';
                        return;
                    }
                    data.forEach(prod => {
                        const div = document.createElement('div');
                        div.className = 'product-card';
                        div.innerHTML = `
                            <div style="font-weight: bold; margin-bottom: 0.25rem;">${prod.name}</div>
                            <div style="color: var(--primary);">$${prod.sell_price}</div>
                            <div style="font-size: 0.75rem; color: var(--text-gray);">Stock: ${prod.stock_qty}</div>
                        `;
                        div.onclick = () => addToCart(prod.id);
                        searchResults.appendChild(div);
                    });
                    
                    // Auto-add if exact barcode match (1 result)
                    if(data.length === 1 && query === data[0].id.toString()) {
                         addToCart(data[0].id);
                         searchInput.value = ''; // Clear for next scan
                    }
                });
        });

        function addToCart(id) {
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('id', id);

            fetch('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    cart = data.cart;
                    renderCart();
                });
        }

        function fetchCart() {
            fetch('../../api/cart_actions.php') // Default gets cart
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
             fetch('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    cart = data.cart;
                    renderCart();
                });
        }

        function clearCart() {
             const formData = new FormData();
            formData.append('action', 'clear');
             fetch('../../api/cart_actions.php', { method: 'POST', body: formData })
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
                        <span style="font-weight: bold;">$${(item.price * item.qty).toFixed(2)}</span>
                        <div style="display: flex; gap: 0.25rem;">
                             <button onclick="updateQty(${item.id}, ${item.qty - 1})" style="width: 24px; height: 24px;">-</button>
                             <button onclick="updateQty(${item.id}, ${item.qty + 1})" style="width: 24px; height: 24px;">+</button>
                        </div>
                    </div>
                `;
                cartItems.appendChild(div);
            });

            const tax = subtotal * 0.05;
            const grand = subtotal + tax;

            document.getElementById('subTotal').innerText = '$' + subtotal.toFixed(2);
            document.getElementById('taxAmount').innerText = '$' + tax.toFixed(2);
            document.getElementById('grandTotal').innerText = '$' + grand.toFixed(2);
        }

        function processCheckout() {
            if(cart.length === 0) {
                alert("Cart is empty");
                return;
            }
            window.location.href = 'checkout.php';
        }
        function holdOrder() {
            if(cart.length === 0) return;
            const customer = prompt("Customer Name (Optional):") || "Walk-in";
            
            const formData = new FormData();
            formData.append('action', 'hold');
            formData.append('customer', customer);
            
            fetch('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    cart = data.cart;
                    renderCart();
                });
        }

        function showHeldOrders() {
            const formData = new FormData();
            formData.append('action', 'list_held');
            
            fetch('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById('heldList');
                    list.innerHTML = '';
                    if(data.held.length === 0) {
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
            const formData = new FormData();
            formData.append('action', 'recall');
            formData.append('id', id);
            
            fetch('../../api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    cart = data.cart;
                    renderCart();
                    document.getElementById('heldModal').style.display = 'none';
                });
        }
    </script>
</body>
</html>
