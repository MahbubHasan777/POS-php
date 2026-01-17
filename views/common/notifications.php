<?php
require_once '../../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$db->query("UPDATE notifications SET is_read = TRUE WHERE user_id = ?", [$user_id], "i");

$notifs = $db->query("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50", [$user_id], "i");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1>Notifications</h1>
            
            <div class="list-group" style="margin-top: 2rem;">
                <?php 
                $res = $notifs->get_result();
                if($res->num_rows == 0): 
                ?>
                    <p style="color: var(--text-gray);">No notifications.</p>
                <?php else: ?>
                    <?php 
                    while($n = $res->fetch_assoc()): 
                    ?>
                    <div style="background: var(--bg-card); padding: 1rem; border-radius: 0.5rem; margin-bottom: 0.75rem; border-left: 4px solid <?php echo $n['is_read'] ? 'var(--text-gray)' : 'var(--primary)'; ?>;">
                        <div style="font-weight: bold; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($n['title']); ?></div>
                        <div style="color: var(--text-gray); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($n['message']); ?></div>
                        <div style="font-size: 0.75rem; color: #6b7280;"><?php echo date('M d, Y h:i A', strtotime($n['created_at'])); ?></div>
                    </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
