<?php
require_once '../../includes/db.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];

$search = $_GET['search'] ?? '';
$filter_cat = $_GET['category'] ?? '';

// Build Query
$sql = "SELECT p.*, c.name as cat_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        WHERE p.shop_id = ?";
$params = [$shop_id];
$types = "i";

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.id = ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $search; // Try exact ID match too
    $types .= "ss";
}
if ($filter_cat) {
    $sql .= " AND p.category_id = ?";
    $params[] = $filter_cat;
    $types .= "i";
}

$sql .= " ORDER BY p.id DESC";
$products = $db->query($sql, $params, $types);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory - Shop Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1>Inventory Management</h1>
                <a href="product_form.php" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.75rem 1.5rem;">+ Add Product</a>
            </div>

            <!-- Filters -->
            <form class="form-group" style="display: flex; gap: 1rem; background: var(--bg-card); padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
                <input type="text" name="search" class="form-input" placeholder="Search by name or ID..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="category" class="form-input">
                    <option value="">All Categories</option>
                    <?php 
                    $cats = $db->query("SELECT * FROM categories WHERE shop_id = ?", [$shop_id], "i");
                    while($c = $cats->get_result()->fetch_assoc()) {
                        $selected = $filter_cat == $c['id'] ? 'selected' : '';
                        echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn-primary" style="width: auto;">Filter</button>
            </form>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Img</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Buy Price</th>
                            <th>Sell Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($p = $products->get_result()->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if($p['image']): ?>
                                    <img src="../../uploads/<?php echo htmlspecialchars($p['image']); ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <div style="width: 40px; height: 40px; background: #333; border-radius: 4px;"></div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($p['name']); ?></td>
                            <td><?php echo htmlspecialchars($p['cat_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($p['brand_name'] ?? '-'); ?></td>
                            <td><?php echo $p['buy_price']; ?></td>
                            <td><?php echo $p['sell_price']; ?></td>
                            <td>
                                <span style="color: <?php echo $p['stock_qty'] <= $p['alert_threshold'] ? '#f87171' : '#34d399'; ?>">
                                    <?php echo $p['stock_qty']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="product_form.php?id=<?php echo $p['id']; ?>" style="color: var(--primary); text-decoration: none;">Edit</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
