<?php
require_once '../../models/Category.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];
$catModel = new Category();
$brandModel = new Brand();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $catModel->create($shop_id, $_POST['name']);
    }
    if (isset($_POST['add_brand'])) {
        $brandModel->create($shop_id, $_POST['name']);
    }
    if (isset($_POST['delete_category'])) {
        $catModel->delete($_POST['id'], $shop_id);
    }
    if (isset($_POST['delete_brand'])) {
        $brandModel->delete($_POST['id'], $shop_id);
    }
}

$categories = $catModel->getAll($shop_id);
$brands = $brandModel->getAll($shop_id);
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
                                <?php while($cat = $categories->fetch_assoc()): ?>
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
                                <?php while($brand = $brands->fetch_assoc()): ?>
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
