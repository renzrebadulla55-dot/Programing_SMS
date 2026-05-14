<?php 
require_once '../config/database.php';
include '../includes/header.php'; 

$query = "SELECT * FROM students ORDER BY last_name ASC";
$stmt = $conn->query($query);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); }
th { background-color: #F8FAFC; color: var(--primary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; }
.radio-group { display: flex; gap: 15px; }
.radio-label { display: flex; align-items: center; gap: 5px; font-size: 0.9rem; cursor: pointer; }
</style>

<div class="page-header">
    <h2 class="page-title">Mark Attendance</h2>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Recording for: <strong><?php echo date('F d, Y'); ?></strong></p>
</div>

<div class="card">
    <form action="../actions/save_attendance.php" method="POST">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Section</th>
                    <th>Attendance Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): ?>
                <tr>
                    <td><strong><?php echo $s['last_name'] . ", " . $s['first_name']; ?></strong></td>
                    <td><?php echo $s['section']; ?></td>
                    <td>
                        <div class="radio-group">
                            <label class="radio-label" style="color: var(--success);"><input type="radio" name="attendance[<?php echo $s['id']; ?>]" value="Present" checked> Present</label>
                            <label class="radio-label" style="color: var(--warning);"><input type="radio" name="attendance[<?php echo $s['id']; ?>]" value="Late"> Late</label>
                            <label class="radio-label" style="color: var(--danger);"><input type="radio" name="attendance[<?php echo $s['id']; ?>]" value="Absent"> Absent</label>
                            <label class="radio-label" style="color: var(--text-muted);"><input type="radio" name="attendance[<?php echo $s['id']; ?>]" value="Excused"> Excused</label>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 25px;">
            <button type="submit" class="btn btn-primary">Submit Daily Attendance</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>