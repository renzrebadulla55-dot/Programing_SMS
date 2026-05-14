<?php 
require_once '../includes/session.php';
require_once '../config/database.php';
include '../includes/header.php'; 

<<<<<<< HEAD
$query = "SELECT * FROM students ORDER BY last_name ASC";
$stmt = $conn->query($query);
=======
$today = date('Y-m-d');
$today_label = date('F d, Y');

// Fetch all students
$stmt = $conn->query("SELECT * FROM students ORDER BY last_name ASC");
>>>>>>> 915aea506c89d11fadd177121a29ad1f57cdbecc
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check existing records for today
$ex_stmt = $conn->prepare("SELECT student_id, status FROM attendance WHERE attendance_date = ?");
$ex_stmt->execute([$today]);
$existing = [];
foreach ($ex_stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $existing[$row['student_id']] = $row['status'];
}

$already_submitted = count($existing) > 0;
$avatar_colors = ['#5046e5','#0ea5e9','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899'];
?>

<<<<<<< HEAD
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
=======
<div class="container">

    <!-- Header -->
    <div class="att-page-header">
        <div>
            <h2>Mark Attendance</h2>
            <p>Record today's attendance for all enrolled students.</p>
        </div>
        <span class="date-pill"><?php echo $today_label; ?></span>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="success-box">✔ Attendance saved successfully for <?php echo $today_label; ?>!</div>
    <?php endif; ?>

    <?php if ($already_submitted): ?>
    <div class="already-notice">
         Attendance for today has already been submitted. You can still update records below.
    </div>
    <?php endif; ?>

    <!-- Live Summary -->
    <div class="att-summary">
        <div class="att-stat s-total">
            <span class="sval"><?php echo count($students); ?></span>
            <span class="slabel">Total</span>
        </div>
        <div class="att-stat s-present">
            <span class="sval" id="cnt-present">0</span>
            <span class="slabel">Present</span>
        </div>
        <div class="att-stat s-late">
            <span class="sval" id="cnt-late">0</span>
            <span class="slabel">Late</span>
        </div>
        <div class="att-stat s-absent">
            <span class="sval" id="cnt-absent">0</span>
            <span class="slabel">Absent</span>
        </div>
    </div>

    <!-- Attendance Form -->
    <form action="../actions/save_attendance.php" method="POST" id="attendanceForm">
        <div class="card" style="padding:0;overflow:hidden;">
            <table>
                <thead>
                    <tr>
                        <th style="width:44px;">#</th>
                        <th>Student</th>
                        <th>Student No.</th>
                        <th>Section</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $i => $s):
                        $sid     = $s['id'];
                        $initials = strtoupper(substr($s['first_name'],0,1).substr($s['last_name'],0,1));
                        $color   = $avatar_colors[$i % count($avatar_colors)];
                        $current = $existing[$sid] ?? 'Present';
                    ?>
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px;"><?php echo $i + 1; ?></td>
                        <td>
                            <div class="s-cell">
                                <div class="s-avatar-sm" style="background:<?php echo $color; ?>">
                                    <?php echo $initials; ?>
                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:600;">
                                        <?php echo htmlspecialchars($s['last_name'].', '.$s['first_name']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-muted);font-size:12px;"><?php echo htmlspecialchars($s['student_no']); ?></td>
                        <td style="font-size:12px;"><?php echo htmlspecialchars($s['section']); ?></td>
                        <td>
                            <div class="att-options">
                                <div class="att-radio r-present">
                                    <input type="radio" name="attendance[<?php echo $sid; ?>]" id="p_<?php echo $sid; ?>" value="Present" <?php echo $current==='Present'?'checked':''; ?>>
                                    <label for="p_<?php echo $sid; ?>">P</label>
                                </div>
                                <div class="att-radio r-late">
                                    <input type="radio" name="attendance[<?php echo $sid; ?>]" id="l_<?php echo $sid; ?>" value="Late" <?php echo $current==='Late'?'checked':''; ?>>
                                    <label for="l_<?php echo $sid; ?>">L</label>
                                </div>
                                <div class="att-radio r-absent">
                                    <input type="radio" name="attendance[<?php echo $sid; ?>]" id="a_<?php echo $sid; ?>" value="Absent" <?php echo $current==='Absent'?'checked':''; ?>>
                                    <label for="a_<?php echo $sid; ?>">A</label>
                                </div>
                                <div class="att-radio r-excused">
                                    <input type="radio" name="attendance[<?php echo $sid; ?>]" id="e_<?php echo $sid; ?>" value="Excused" <?php echo $current==='Excused'?'checked':''; ?>>
                                    <label for="e_<?php echo $sid; ?>">E</label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Submit Bar -->
        <div class="submit-bar">
            <p class="legend">
                <strong>P</strong> = Present &nbsp;·&nbsp;
                <strong>L</strong> = Late &nbsp;·&nbsp;
                <strong>A</strong> = Absent &nbsp;·&nbsp;
                <strong>E</strong> = Excused
            </p>
            <div style="display:flex;gap:10px;">
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <?php echo $already_submitted ? 'Update Attendance' : 'Submit Attendance'; ?>
                </button>
            </div>
>>>>>>> 915aea506c89d11fadd177121a29ad1f57cdbecc
        </div>
    </form>

</div>

<script>
function updateCounts() {
    let present = 0, late = 0, absent = 0;
    document.querySelectorAll('input[type="radio"]:checked').forEach(r => {
        if (r.value === 'Present') present++;
        else if (r.value === 'Late') late++;
        else if (r.value === 'Absent') absent++;
    });
    document.getElementById('cnt-present').textContent = present;
    document.getElementById('cnt-late').textContent = late;
    document.getElementById('cnt-absent').textContent = absent;
}
document.getElementById('attendanceForm').addEventListener('change', updateCounts);
updateCounts();
</script>

<?php include '../includes/footer.php'; ?>