<?php 
require_once '../includes/session.php';
require_once '../config/database.php';
include '../includes/header.php'; 

$today = date('Y-m-d');

$query = "SELECT * FROM students ORDER BY last_name ASC";
$stmt = $conn->query($query);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check existing records for today
$ex_stmt = $conn->prepare("SELECT student_id, status FROM attendance WHERE attendance_date = ?");
$ex_stmt->execute([$today]);
$existing = [];
foreach ($ex_stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $existing[$row['student_id']] = $row['status'];
}

$already_submitted = count($existing) > 0;
?>

<div class="page-header">
    <h2 class="page-title">Mark Attendance</h2>
    <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Recording for: <strong><?php echo date('F d, Y'); ?></strong></p>
</div>

<div class="card" style="margin-bottom: 30px;">
    
    <?php if($already_submitted): ?>
        <div style="background: #e0f2fe; color: #0369a1; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600;">
            ℹ️ Attendance for today has already been submitted. You are currently editing existing records.
        </div>
    <?php endif; ?>

    <div style="display: flex; gap: 15px; margin-bottom: 20px;">
        <div style="background: #F1F5F9; padding: 8px 15px; border-radius: 8px; font-weight: 600; color: var(--success);">Present: <span id="cnt-present">0</span></div>
        <div style="background: #F1F5F9; padding: 8px 15px; border-radius: 8px; font-weight: 600; color: var(--warning);">Late: <span id="cnt-late">0</span></div>
        <div style="background: #F1F5F9; padding: 8px 15px; border-radius: 8px; font-weight: 600; color: var(--danger);">Absent: <span id="cnt-absent">0</span></div>
        <div style="background: #F1F5F9; padding: 8px 15px; border-radius: 8px; font-weight: 600; color: var(--text-muted);">Excused: <span id="cnt-excused">0</span></div>
    </div>

    <form id="attendanceForm" action="../actions/save_attendance.php" method="POST">
        <table class="big-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Year & Section</th>
                    <th>Attendance Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): 
                    // Determine pre-selected status
                    $current_status = isset($existing[$s['id']]) ? $existing[$s['id']] : 'Present';
                ?>
                <tr>
                    <td><strong><?php echo $s['last_name'] . ", " . $s['first_name'] . " " . (isset($s['middle_initial']) ? $s['middle_initial'] : ''); ?></strong></td>
                    <td>
                        <span style="background: var(--bg-color); padding: 5px 10px; border-radius: 6px; font-weight: 600; font-size: 0.85rem;">
                            <?php echo (isset($s['year_level']) ? $s['year_level'] : '1') . "-" . $s['section']; ?>
                        </span>
                    </td>
                    <td>
                        <div class="radio-group" style="display: flex; gap: 20px;">
                            <label class="radio-label" style="display:flex; align-items:center; gap:8px; cursor:pointer; color: var(--success); font-weight: 600;">
                                <input type="radio" style="transform: scale(1.3); cursor: pointer;" name="attendance[<?php echo $s['id']; ?>]" value="Present" <?php echo ($current_status == 'Present') ? 'checked' : ''; ?>> Present
                            </label>
                            <label class="radio-label" style="display:flex; align-items:center; gap:8px; cursor:pointer; color: var(--warning); font-weight: 600;">
                                <input type="radio" style="transform: scale(1.3); cursor: pointer;" name="attendance[<?php echo $s['id']; ?>]" value="Late" <?php echo ($current_status == 'Late') ? 'checked' : ''; ?>> Late
                            </label>
                            <label class="radio-label" style="display:flex; align-items:center; gap:8px; cursor:pointer; color: var(--danger); font-weight: 600;">
                                <input type="radio" style="transform: scale(1.3); cursor: pointer;" name="attendance[<?php echo $s['id']; ?>]" value="Absent" <?php echo ($current_status == 'Absent') ? 'checked' : ''; ?>> Absent
                            </label>
                            <label class="radio-label" style="display:flex; align-items:center; gap:8px; cursor:pointer; color: var(--text-muted); font-weight: 600;">
                                <input type="radio" style="transform: scale(1.3); cursor: pointer;" name="attendance[<?php echo $s['id']; ?>]" value="Excused" <?php echo ($current_status == 'Excused') ? 'checked' : ''; ?>> Excused
                            </label>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary"><?php echo $already_submitted ? "Update Attendance" : "Save Attendance Records"; ?></button>
        </div>
    </form>

</div>

<script>
function updateCounts() {
    let present = 0, late = 0, absent = 0, excused = 0;
    document.querySelectorAll('input[type="radio"]:checked').forEach(r => {
        if (r.value === 'Present') present++;
        else if (r.value === 'Late') late++;
        else if (r.value === 'Absent') absent++;
        else if (r.value === 'Excused') excused++;
    });
    document.getElementById('cnt-present').textContent = present;
    document.getElementById('cnt-late').textContent = late;
    document.getElementById('cnt-absent').textContent = absent;
    document.getElementById('cnt-excused').textContent = excused;
}

document.getElementById('attendanceForm').addEventListener('change', updateCounts);
// Run once on load to set initial counts
updateCounts();
</script>

<?php include '../includes/footer.php'; ?>