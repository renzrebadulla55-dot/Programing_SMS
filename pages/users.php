<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

include '../includes/header.php'; 

$query = "SELECT * FROM users ORDER BY id ASC";
$stmt = $conn->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 class="page-title">User Management</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Manage Admins and Professors.</p>
        </div>
        <button onclick="document.getElementById('addUserModal').style.display='flex'" class="btn btn-primary" style="background: var(--primary); border: none;">+ Add New User</button>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div style="background: #D1FAE5; color: #065F46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">✔ <?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div style="background: #FEE2E2; color: #991B1B; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">✖ Error: <?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <div class="card">
        <table class="big-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><strong style="color: var(--text-main);"><?php echo htmlspecialchars($u['username']); ?></strong></td>
                    <td>
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; <?php echo ($u['role'] === 'admin') ? 'background: #FEF3C7; color: #D97706;' : 'background: #E0F2FE; color: #0284C7;'; ?>">
                            <?php echo strtoupper($u['role']); ?>
                        </span>
                    </td>
                    <td style="display: flex; gap: 10px;">
                        <button onclick="openEditModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['role']); ?>')" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;">Edit Role</button>
                        <a href="system_logs.php?username=<?php echo urlencode($u['username']); ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; color: #10B981; border-color: #10B981;">View Logs</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 10000; justify-content: center; align-items: center;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);" onclick="document.getElementById('addUserModal').style.display='none'"></div>
    <div style="position: relative; background: white; padding: 30px; border-radius: 12px; width: 100%; max-width: 400px; z-index: 10001;">
        <h3 style="margin-top: 0; color: var(--text-main);">Add New User</h3>
        <form action="../actions/save_user.php" method="POST">
            <div style="margin-bottom: 15px;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 5px;">Username</label>
                <input type="text" name="username" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 5px;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 5px;">Role</label>
                <select name="role" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
                    <option value="professor">Professor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" name="action" value="add" class="btn btn-primary" style="flex: 1;">Create User</button>
                <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" class="btn btn-secondary" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 10000; justify-content: center; align-items: center;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);" onclick="document.getElementById('editUserModal').style.display='none'"></div>
    <div style="position: relative; background: white; padding: 30px; border-radius: 12px; width: 100%; max-width: 400px; z-index: 10001;">
        <h3 style="margin-top: 0; color: var(--text-main);">Edit User Role</h3>
        <form action="../actions/save_user.php" method="POST">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div style="margin-bottom: 20px;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 5px;">Role</label>
                <select name="role" id="edit_role" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
                    <option value="professor">Professor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" name="action" value="edit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
                <button type="button" onclick="document.getElementById('editUserModal').style.display='none'" class="btn btn-secondary" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, currentRole) {
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_role').value = currentRole;
    document.getElementById('editUserModal').style.display = 'flex';
}
</script>

<?php include '../includes/footer.php'; ?>
