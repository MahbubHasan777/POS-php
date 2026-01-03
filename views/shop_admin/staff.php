<?php
require_once '../../models/User.php';
requireRole('shop_admin');

$shop_id = $_SESSION['shop_id'];
$userModel = new User();

$editMode = false;
$editStaff = null;
$error = null;
$success = null;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_staff'])) {
        if ($userModel->exists($_POST['email'], $_POST['username'])) {
            $error = "Email or Username already exists.";
        } else {
            $userModel->create([
                'shop_id' => $shop_id,
                'role' => 'cashier',
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'full_name' => $_POST['full_name']
            ]);
            $success = "Cashier created successfully.";
        }
    } elseif (isset($_POST['update_staff'])) {
        $id = $_POST['id'];
        if ($userModel->exists($_POST['email'], $_POST['username'], $id)) {
            $error = "Email or Username already taken by another staff member.";
        } else {
            $userModel->update($id, $shop_id, [
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'full_name' => $_POST['full_name'],
                'password' => $_POST['password']
            ]);
            $success = "Cashier updated successfully.";
            
            // Redirect to clear edit mode
            header("Location: staff.php");
            exit;
        }
    } elseif (isset($_POST['delete_staff'])) {
        $result = $userModel->delete($_POST['id'], $shop_id);
        if ($result === true) {
            $success = "Cashier deleted successfully.";
        } else {
            $error = $result;
        }
    }
}

// Handle Edit Mode
if (isset($_GET['edit'])) {
    $editStaff = $userModel->getById($_GET['edit'], $shop_id);
    if ($editStaff) {
        $editMode = true;
    }
}

$staffMembers = $userModel->getShopStaff($shop_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Management</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1>Staff (Cashiers) Management</h1>
            
            <?php if($error): ?>
                <div style="background: rgba(239,68,68,0.1); color: #ef4444; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div style="background: rgba(52,211,153,0.1); color: #34d399; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" autocomplete="off" style="background: var(--bg-card); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <?php if($editMode): ?>
                    <input type="hidden" name="id" value="<?php echo $editStaff['id']; ?>">
                <?php endif; ?>
                
                <div>
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-input" required value="<?php echo $editMode ? htmlspecialchars($editStaff['full_name']) : ''; ?>" autocomplete="off">
                </div>
                <div>
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" required value="<?php echo $editMode ? htmlspecialchars($editStaff['username']) : ''; ?>" autocomplete="off">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" required value="<?php echo $editMode ? htmlspecialchars($editStaff['email']) : ''; ?>" autocomplete="off">
                </div>
                <div>
                    <label class="form-label">Password <?php echo $editMode ? '(Leave blank to keep current)' : ''; ?></label>
                    <input type="password" name="password" class="form-input" <?php echo $editMode ? '' : 'required'; ?> autocomplete="new-password">
                </div>

                <div style="grid-column: span 2; display: flex; gap: 1rem;">
                    <button type="submit" name="<?php echo $editMode ? 'update_staff' : 'create_staff'; ?>" class="btn-primary" style="width: auto;">
                        <?php echo $editMode ? 'Update Cashier' : 'Add Cashier'; ?>
                    </button>
                    <?php if($editMode): ?>
                         <a href="staff.php" style="padding: 0.75rem; color: var(--text-gray); text-decoration: none;">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($staff = $staffMembers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($staff['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($staff['username']); ?></td>
                            <td><?php echo htmlspecialchars($staff['email']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($staff['created_at'])); ?></td>
                            <td>
                                <a href="staff.php?edit=<?php echo $staff['id']; ?>" style="color: var(--primary); text-decoration: none; margin-right: 0.5rem;">Edit</a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this cashier?');">
                                    <input type="hidden" name="id" value="<?php echo $staff['id']; ?>">
                                    <button type="submit" name="delete_staff" style="color: #ef4444; background: none; border: none; cursor: pointer;">Delete</button>
                                </form>
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
