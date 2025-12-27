<?php
require_once '../../includes/db.php';
requireRole('shop_admin');

$shop_id = $_SESSION['shop_id'];
$message = "";

// --- DELETE HANDLING ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $del_id = $_POST['delete_id'];
    
    // 1. Get image to delete file
    $stmt = $db->query("SELECT image FROM products WHERE id = ? AND shop_id = ?", [$del_id, $shop_id], "ii");
    $img = $stmt->get_result()->fetch_assoc();
    
    // 2. Delete from DB
    $db->query("DELETE FROM products WHERE id = ? AND shop_id = ?", [$del_id, $shop_id], "ii");
    
    // 3. Delete file if exists
    if ($img && !empty($img['image'])) {
        $path = "../../uploads/" . $img['image'];
        if (file_exists($path)) {
            unlink($path);
        }
    }
    
    $message = "Product deleted successfully.";
}

// --- FILTER PARAMS ---
$search = $_GET['search'] ?? '';
$cat_id = $_GET['category'] ?? '';
$brand_id = $_GET['brand'] ?? '';
$min_p  = $_GET['min_price'] ?? '';
$max_p  = $_GET['max_price'] ?? '';

// --- BUILD QUERY ---
$sql = "SELECT p.*, c.name as cat_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.shop_id = ?";

$params = [$shop_id];
$types = "i";

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}
if ($cat_id) {
    $sql .= " AND p.category_id = ?";
    $params[] = $cat_id;
    $types .= "i";
}
if ($brand_id) {
    $sql .= " AND p.brand_id = ?";
    $params[] = $brand_id;
    $types .= "i";
}
if ($min_p !== '') {
    $sql .= " AND p.sell_price >= ?";
    $params[] = $min_p;
    $types .= "d";
}
if ($max_p !== '') {
    $sql .= " AND p.sell_price <= ?";
    $params[] = $max_p;
    $types .= "d";
}

$sql .= " ORDER BY p.id DESC";

// Execute
$stmt = $db->query($sql, $params, $types);
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close(); // Clean up

// --- DATA FOR DROPDOWNS ---
$cats = $db->query("SELECT * FROM categories WHERE shop_id = ?", [$shop_id], "i")->get_result()->fetch_all(MYSQLI_ASSOC);
$brands = $db->query("SELECT * FROM brands WHERE shop_id = ?", [$shop_id], "i")->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .filter-bar {
            background: var(--bg-card);
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-input {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #444;
            background: #2a3441;
            color: white;
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h1>Inventory</h1>
                <a href="product_form.php" class="btn-primary" style="width: auto; text-decoration: none;">+ Add Product</a>
            </div>

            <?php if($message): ?>
                <div style="padding: 10px; background: rgba(52, 211, 153, 0.2); color: #34d399; margin-bottom: 1rem; border-radius: 5px;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- FILTER FORM -->
            <form class="filter-bar" method="GET">
                <input type="text" name="search" class="filter-input" placeholder="Search Name/ID..." value="<?php echo htmlspecialchars($search); ?>">
                
                <select name="category" class="filter-input">
                    <option value="">All Categories</option>
                    <?php foreach($cats as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $cat_id == $c['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="brand" class="filter-input">
                    <option value="">All Brands</option>
                    <?php foreach($brands as $b): ?>
                        <option value="<?php echo $b['id']; ?>" <?php echo $brand_id == $b['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($b['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="number" name="min_price" class="filter-input" placeholder="Min Price" style="width: 100px;" value="<?php echo $min_p; ?>">
                <input type="number" name="max_price" class="filter-input" placeholder="Max Price" style="width: 100px;" value="<?php echo $max_p; ?>">

                <button type="submit" class="btn-primary" style="width: auto; padding: 8px 15px;">Filter</button>
                <a href="products.php" style="color: #aaa; text-decoration: none;">Reset</a>
            </form>

            <!-- TABLE -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Buy Price</th>
                            <th>Sell Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem; color: #888;">
                                    No products found matching your criteria.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($products as $p): ?>
                            <tr>
                                <td>
                                    <?php if($p['image'] && file_exists("../../uploads/" . $p['image'])): ?>
                                        <img src="../../uploads/<?php echo $p['image']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #333; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #666; border-radius: 4px;">No Img</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: bold;"><?php echo htmlspecialchars($p['name']); ?></div>
                                    <div style="font-size: 0.8rem; color: #888;">ID: #<?php echo $p['id']; ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($p['cat_name'] ?? 'Uncategorized'); ?></td>
                                <td><?php echo htmlspecialchars($p['brand_name'] ?? 'No Brand'); ?></td>
                                <td>$<?php echo number_format($p['buy_price'], 2); ?></td>
                                <td>$<?php echo number_format($p['sell_price'], 2); ?></td>
                                <td>
                                    <span style="color: <?php echo $p['stock_qty'] <= $p['alert_threshold'] ? '#f87171' : '#34d399'; ?>">
                                        <?php echo $p['stock_qty']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="product_form.php?id=<?php echo $p['id']; ?>" class="btn-primary" style="display: inline-block; width: auto; padding: 5px 10px; font-size: 0.8rem; margin-right: 5px;">Edit</a>
                                    
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $p['id']; ?>">
                                        <button type="submit" style="background: none; border: 1px solid #ef4444; color: #ef4444; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
