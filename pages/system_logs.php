<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';
include '../includes/header.php'; 

$query = "SELECT * FROM system_logs ORDER BY created_at DESC";
$stmt = $conn->query($query);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="page-header">
        <h2 class="page-title">System Logs</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Monitor recent administrative activities.</p>
    </div>

    <div class="card">
        <table class="big-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action Performed</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($logs) > 0): ?>
                    <?php foreach($logs as $log): ?>
                    <tr>
                        <td style="color: var(--text-muted); font-size: 0.9rem;"><?php echo date('M d, Y - h:i A', strtotime($log['created_at'])); ?></td>
                        <td><strong><?php echo htmlspecialchars($log['user_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($log['action_desc']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align: center; padding: 20px; color: var(--text-muted);">No system logs found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
