<?php
$role = $_SESSION['role'] ?? 'guest';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="logo" style="font-size: 1.5rem; margin-bottom: 2rem;">POS System</div>
    
    <ul style="list-style: none;">
        <?php if($role === 'super_admin'): ?>
            <li style="margin-bottom: 0.5rem;">
                <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Dashboard
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="shops.php" class="<?php echo $current_page == 'shops.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Manage Shops
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="subscriptions.php" class="<?php echo $current_page == 'subscriptions.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Subscription Plans
                </a>
            </li>
        <?php elseif($role === 'shop_admin'): ?>
            <li style="margin-bottom: 0.5rem;">
                <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Overview
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="products.php" class="<?php echo $current_page == 'products.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Inventory
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="categories.php" class="<?php echo $current_page == 'categories.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Categories & Brands
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="staff.php" class="<?php echo $current_page == 'staff.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Staff Management
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="reports.php" class="<?php echo $current_page == 'reports.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Reports & AI
                </a>
            </li>
        <?php elseif($role === 'cashier'): ?>
            <li style="margin-bottom: 0.5rem;">
                <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    POS Terminal
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="history.php" class="<?php echo $current_page == 'history.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Sales History
                </a>
            </li>
        <?php endif; ?>

        <li style="margin-bottom: 0.5rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 0.5rem;">
            <a href="../common/notifications.php" class="<?php echo $current_page == 'notifications.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                Notifications
            </a>
        </li>
        
        <li style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
            <a href="../logout.php" style="display: block; padding: 0.75rem; color: #ef4444; text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                Logout
            </a>
        </li>
    </ul>

    <style>
        .active-link {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary) !important;
            border-left: 3px solid var(--primary);
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }
    </style>
</div>
