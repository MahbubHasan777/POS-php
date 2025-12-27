<?php
$role = $_SESSION['role'] ?? 'guest';
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Determine path prefixes
$role_path = ($current_dir === 'common') ? '../' . $role . '/' : '';
$common_path = ($current_dir === 'common') ? '' : '../common/';
// Logout is always in views/, so if we are in views/X/, ../logout.php reaches views/logout.php
$logout_path = '../logout.php';
?>
<div class="sidebar">
    <div class="logo" style="font-size: 1.5rem; margin-bottom: 2rem;">POS System</div>
    
    <ul style="list-style: none;">
        <?php if($role === 'super_admin'): ?>
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo $role_path; ?>dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Dashboard
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo $role_path; ?>shops.php" class="<?php echo $current_page == 'shops.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Manage Shops
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo $role_path; ?>subscriptions.php" class="<?php echo $current_page == 'subscriptions.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Subscription Plans
                </a>
            </li>
        <?php elseif($role === 'shop_admin'): ?>
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo $role_path; ?>dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Overview
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo $role_path; ?>products.php" class="<?php echo $current_page == 'products.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Inventory
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo $role_path; ?>categories.php" class="<?php echo $current_page == 'categories.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Categories & Brands
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo $role_path; ?>staff.php" class="<?php echo $current_page == 'staff.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Staff Management
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo $role_path; ?>reports.php" class="<?php echo $current_page == 'reports.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Reports & AI
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo $role_path; ?>coupons.php" class="<?php echo $current_page == 'coupons.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Vouchers & Coupons
                </a>
            </li>
        <?php elseif($role === 'cashier'): ?>
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo $role_path; ?>dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    POS Terminal
                </a>
            </li>
            <li style="margin-bottom: 0.5rem;">
                <a href="<?php echo $role_path; ?>history.php" class="<?php echo $current_page == 'history.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                    Sales History
                </a>
            </li>
        <?php endif; ?>

        <li style="margin-bottom: 0.5rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 0.5rem;">
            <a href="<?php echo $common_path; ?>notifications.php" class="<?php echo $current_page == 'notifications.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                Notifications
            </a>
        </li>

         <li style="margin-bottom: 0.5rem;">
            <a href="<?php echo $common_path; ?>profile.php" class="<?php echo $current_page == 'profile.php' ? 'active-link' : ''; ?>" style="display: block; padding: 0.75rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
                Profile & Password
            </a>
        </li>
        
        <li style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
            <a href="<?php echo $logout_path; ?>" style="display: block; padding: 0.75rem; color: #ef4444; text-decoration: none; border-radius: 0.5rem; transition: all 0.2s;">
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
