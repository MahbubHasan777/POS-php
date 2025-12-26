<?php
require_once '../../includes/db.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];

$product = null;
if (isset($_GET['id'])) {
    $stmt = $db->query("SELECT * FROM products WHERE id = ? AND shop_id = ?", [$_GET['id'], $shop_id], "ii");
    $product = $stmt->get_result()->fetch_assoc();
}

$categories = $db->query("SELECT * FROM categories WHERE shop_id = ?", [$shop_id], "i");
$brands = $db->query("SELECT * FROM brands WHERE shop_id = ?", [$shop_id], "i");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $cat_id = $_POST['category_id'] ?: NULL;
    $brand_id = $_POST['brand_id'] ?: NULL;
    $buy = $_POST['buy_price'];
    $sell = $_POST['sell_price'];
    $stock = $_POST['stock_qty'];
    $alert = $_POST['alert_threshold'];
    
    // Image Upload
    $imagePath = $product['image'] ?? null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "../../uploads/" . $filename);
        $imagePath = $filename;
    }

    if ($product) {
        // Update
        $db->query("UPDATE products SET name=?, category_id=?, brand_id=?, buy_price=?, sell_price=?, stock_qty=?, alert_threshold=?, image=? WHERE id=? AND shop_id=?", 
            [$name, $cat_id, $brand_id, $buy, $sell, $stock, $alert, $imagePath, $product['id'], $shop_id], "siidddissi");
    } else {
        // Insert
        $db->query("INSERT INTO products (shop_id, name, category_id, brand_id, buy_price, sell_price, stock_qty, alert_threshold, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$shop_id, $name, $cat_id, $brand_id, $buy, $sell, $stock, $alert, $imagePath], "isiidddis");
    }
    
    redirect('products.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $product ? 'Edit' : 'Add'; ?> Product</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1><?php echo $product ? 'Edit' : 'Add New'; ?> Product</h1>
            
            <form method="POST" enctype="multipart/form-data" style="max-width: 600px; background: var(--bg-card); padding: 2rem; border-radius: 1rem;">
                <div class="form-group">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-input" required value="<?php echo $product['name'] ?? ''; ?>">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-input">
                            <option value="">Select Category</option>
                            <?php 
                            $categories->data_seek(0);
                            while($c = $categories->get_result()->fetch_assoc()): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo ($product['category_id']??'')==$c['id']?'selected':''; ?>>
                                    <?php echo htmlspecialchars($c['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-input">
                            <option value="">Select Brand</option>
                            <?php 
                            $brands->data_seek(0);
                            while($b = $brands->get_result()->fetch_assoc()): ?>
                                <option value="<?php echo $b['id']; ?>" <?php echo ($product['brand_id']??'')==$b['id']?'selected':''; ?>>
                                    <?php echo htmlspecialchars($b['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Buy Price</label>
                        <input type="number" step="0.01" name="buy_price" class="form-input" required value="<?php echo $product['buy_price'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sell Price</label>
                        <input type="number" step="0.01" name="sell_price" class="form-input" required value="<?php echo $product['sell_price'] ?? ''; ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Current Stock</label>
                        <input type="number" name="stock_qty" class="form-input" required value="<?php echo $product['stock_qty'] ?? '0'; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Low Stock Alert Filter</label>
                        <input type="number" name="alert_threshold" class="form-input" value="<?php echo $product['alert_threshold'] ?? '5'; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-input" accept="image/*">
                    <?php if(isset($product['image']) && $product['image']): ?>
                        <p style="margin-top: 0.5rem; color: var(--text-gray);">Current: <?php echo $product['image']; ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 1rem;">Save Product</button>
            </form>
        </div>
    </div>
</body>
</html>
