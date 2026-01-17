<?php
require_once '../../includes/db.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];
$message = "";
$error = "";

$product = null;
if (isset($_GET['id'])) {
    $stmt = $db->query("SELECT * FROM products WHERE id = ? AND shop_id = ?", [$_GET['id'], $shop_id], "ii");
    $product = $stmt->get_result()->fetch_assoc();
}

$cats = $db->query("SELECT * FROM categories WHERE shop_id = ?", [$shop_id], "i")->get_result()->fetch_all(MYSQLI_ASSOC);
$brands = $db->query("SELECT * FROM brands WHERE shop_id = ?", [$shop_id], "i")->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $cat_id = !empty($_POST['category_id']) ? $_POST['category_id'] : NULL;
    $brand_id = !empty($_POST['brand_id']) ? $_POST['brand_id'] : NULL;
    $buy = $_POST['buy_price'];
    $sell = $_POST['sell_price'];
    $stock = $_POST['stock_qty'];
    $alert = $_POST['alert_threshold'];
    
    $imagePath = $product['image'] ?? null;
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = "../../uploads/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . "." . $ext;
        $targetFile = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $filename;
        } else {
            $error = "Failed to upload image. Check folder permissions.";
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] != 4) {
         $error = "Image upload error code: " . $_FILES['image']['error'];
    }

    if (!$error) {
        if ($product) {
            $db->query("UPDATE products SET name=?, category_id=?, brand_id=?, buy_price=?, sell_price=?, stock_qty=?, alert_threshold=?, image=? WHERE id=? AND shop_id=?", 
                [$name, $cat_id, $brand_id, $buy, $sell, $stock, $alert, $imagePath, $product['id'], $shop_id], "siidddissi");
        } else {
            $db->query("INSERT INTO products (shop_id, name, category_id, brand_id, buy_price, sell_price, stock_qty, alert_threshold, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$shop_id, $name, $cat_id, $brand_id, $buy, $sell, $stock, $alert, $imagePath], "isiidddis");
        }
        
        redirect('products.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $product ? 'Edit' : 'Add'; ?> Product</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1><?php echo $product ? 'Edit' : 'Add New'; ?> Product</h1>
            
            <?php if($error): ?>
                <div style="padding: 10px; background: rgba(239, 68, 68, 0.2); color: #f87171; border-radius: 5px; margin-bottom: 1rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

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
                            <?php foreach($cats as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo ($product['category_id']??'')==$c['id']?'selected':''; ?>>
                                    <?php echo htmlspecialchars($c['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-input">
                            <option value="">Select Brand</option>
                            <?php foreach($brands as $b): ?>
                                <option value="<?php echo $b['id']; ?>" <?php echo ($product['brand_id']??'')==$b['id']?'selected':''; ?>>
                                    <?php echo htmlspecialchars($b['name']); ?>
                                </option>
                            <?php endforeach; ?>
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
                        <div style="margin-top: 10px;">
                            <img src="../../uploads/<?php echo $product['image']; ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                            <p style="color: #888; font-size: 0.8rem;">Current Image</p>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 1rem;">Save Product</button>
                <a href="products.php" class="btn-primary" style="background: transparent; border: 1px solid #666; width: auto; display: inline-block; text-align: center; margin-left: 10px;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
