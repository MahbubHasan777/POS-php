<?php
require_once '../../includes/db.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = $_POST['name'];
        $db->query("INSERT INTO categories (shop_id, name) VALUES (?, ?)", [$shop_id, $name], "is");
    }
    if (isset($_POST['add_brand'])) {
        $name = $_POST['name'];
        $db->query("INSERT INTO brands (shop_id, name) VALUES (?, ?)", [$shop_id, $name], "is");
    }
    if (isset($_POST['delete_category'])) {
        $id = $_POST['id'];
        $db->query("DELETE FROM categories WHERE id = ? AND shop_id = ?", [$id, $shop_id], "ii");
    }
    if (isset($_POST['delete_brand'])) {
        $id = $_POST['id'];
        $db->query("DELETE FROM brands WHERE id = ? AND shop_id = ?", [$id, $shop_id], "ii");
    }
    // Update logic could go here similar to Add
}

$cat_stmt = $db->query("SELECT * FROM categories WHERE shop_id = ? ORDER BY id DESC", [$shop_id], "i");
$categories_res = $cat_stmt->get_result();

$brand_stmt = $db->query("SELECT * FROM brands WHERE shop_id = ? ORDER BY id DESC", [$shop_id], "i");
$brands_res = $brand_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories & Brands</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .split-view {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        .form-inline {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1>Structure Management</h1>
            
            <div class="split-view">
                <!-- Categories Section -->
                <div>
                    <h2>Categories</h2>
                    <form method="POST" class="form-inline">
                        <input type="text" name="name" class="form-input" placeholder="New Category Name" required>
                        <button type="submit" name="add_category" class="btn-primary" style="width: auto;">Add</button>
                    </form>
                    
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Name</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php 
                                while($cat = $categories_res->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                            <button type="submit" name="delete_category" style="color: #ef4444; background: none; border: none; cursor: pointer;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Brands Section -->
                <div>
                    <h2>Brands</h2>
                    <form method="POST" class="form-inline">
                        <input type="text" name="name" class="form-input" placeholder="New Brand Name" required>
                        <button type="submit" name="add_brand" class="btn-primary" style="width: auto;">Add</button>
                    </form>
                    
                     <div class="table-container">
                        <table>
                            <thead><tr><th>Name</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php 
                                while($brand = $brands_res->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($brand['name']); ?></td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?php echo $brand['id']; ?>">
                                            <button type="submit" name="delete_brand" style="color: #ef4444; background: none; border: none; cursor: pointer;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
