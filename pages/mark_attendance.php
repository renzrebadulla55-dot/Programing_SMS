<?php 
require_once '../includes/session.php';
require_once '../config/database.php';
include '../includes/header.php'; 

$role = $_SESSION['role'] ?? 'admin';
$user_id = $_SESSION['user_id'];

// Fetch subjects for dropdown based on role
if ($role === 'professor') {
    $subs_stmt = $conn->prepare("SELECT * FROM subjects WHERE professor_id = ? ORDER BY subject_name ASC");
    $subs_stmt->execute([$user_id]);
} else {
    $subs_stmt = $conn->prepare("SELECT * FROM subjects ORDER BY subject_name ASC");
    $subs_stmt->execute();
}
$subjects = $subs_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get filter parameters
$selected_subject_id = isset($_GET['subject_id']) ? $_GET['subject_id'] : '';
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$selected_section = isset($_GET['section']) ? $_GET['section'] : '';

$students = [];
$existing = [];
$already_submitted = false;
$subject_name = "";

if ($selected_subject_id && $selected_section) {
    // Get the year level of the selected subject
    $sub_info = $conn->prepare("SELECT subject_name, year_level FROM subjects WHERE id = ?");
    $sub_info->execute([$selected_subject_id]);
    $sub_data = $sub_info->fetch(PDO::FETCH_ASSOC);
    $subject_year = $sub_data['year_level'];
    $subject_name = $sub_data['subject_name'];

    // Fetch students matching the subject's year level AND selected section
    $query = "SELECT * FROM students WHERE year_level = ? AND section = ? ORDER BY last_name ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute([$subject_year, $selected_section]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check existing records for this specific date AND subject
    $ex_stmt = $conn->prepare("SELECT student_id, status FROM attendance WHERE attendance_date = ? AND subject_id = ?");
    $ex_stmt->execute([$selected_date, $selected_subject_id]);
    foreach ($ex_stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $existing[$row['student_id']] = $row['status'];
    }
    $already_submitted = count($existing) > 0;
}
?>

<div class="page-header">
    <h2 class="page-title">Mark Attendance</h2>
    <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Record student attendance accurately.</p>
</div>

<?php if (!$selected_subject_id || !$selected_section): ?>
    <!-- "Modal" popup feel for choosing class initially -->
    <div style="display: flex; justify-content: center; align-items: center; min-height: 50vh;">
        <div class="card" style="width: 100%; max-width: 500px; text-align: center; padding: 40px; box-shadow: 0 20px 40px rgba(0,0,0,0.08); border: 1px solid #E2E8F0;">
            <div style="margin-bottom: 20px;">
                <span style="font-size: 3rem;">🏫</span>
                <h3 style="margin-top: 15px; color: var(--primary);">Choose a Class to Start</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Please select your subject, date, and section.</p>
            </div>
            
            <form method="GET" style="display: flex; flex-direction: column; gap: 15px; text-align: left;">
                <div>
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 5px; display: block;">Subject</label>
                    <select name="subject_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
                        <option value="">-- Select Subject --</option>
                        <?php foreach($subjects as $sub): ?>
                            <option value="<?php echo $sub['id']; ?>"><?php echo htmlspecialchars($sub['subject_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 5px; display: block;">Date</label>
                    <input type="date" name="date" value="<?php echo htmlspecialchars($selected_date); ?>" 
                           min="<?php echo date('Y-m-d'); ?>" 
                           max="<?php echo date('Y-m-d'); ?>" 
                           required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
                </div>
                <div>
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 5px; display: block;">Section</label>
                    <select name="section" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
                        <option value="">-- Select Section --</option>
                        <?php 
                        $allowed_secs = ['A', 'B', 'C', 'D'];
                        if ($role === 'professor') {
                            $sec_stmt = $conn->prepare("SELECT DISTINCT section FROM schedules WHERE professor_id = ? ORDER BY section ASC");
                            $sec_stmt->execute([$user_id]);
                            $allowed_secs = $sec_stmt->fetchAll(PDO::FETCH_COLUMN);
                            if(empty($allowed_secs)) $allowed_secs = ['A', 'B', 'C', 'D'];
                        }
                        foreach($allowed_secs as $sec) echo "<option value='$sec'>Section $sec</option>";
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px; width: 100%; padding: 12px; font-size: 1rem;">Load Class List</button>
            </form>
        </div>
    </div>
<?php else: ?>

    <!-- Show active selection header -->
    <div style="background: #FFFFFF; border: 1px solid var(--border-color); padding: 20px; border-radius: 12px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Currently Recording for:</div>
            <div style="font-size: 1.2rem; font-weight: 700; color: var(--primary); margin-top: 5px;">
                <?php echo htmlspecialchars($subject_name) . " - Section " . htmlspecialchars($selected_section); ?>
            </div>
            <div style="font-size: 0.9rem; color: var(--text-main); margin-top: 5px;">
                Date: <strong><?php echo date('F d, Y', strtotime($selected_date)); ?></strong>
            </div>
        </div>
        <div>
            <a href="mark_attendance.php" class="btn btn-secondary">Change Class</a>
        </div>
    </div>

    <div class="card" style="margin-bottom: 30px;">
        
        <?php if($already_submitted): ?>
            <div style="background: #e0f2fe; color: #0369a1; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600;">
                ℹ️ Attendance for this class on this date has already been submitted. You are currently editing existing records.
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
            <div style="background: #F1F5F9; padding: 8px 15px; border-radius: 8px; font-weight: 600; color: var(--success);">Present: <span id="cnt-present">0</span></div>
            <div style="background: #F1F5F9; padding: 8px 15px; border-radius: 8px; font-weight: 600; color: var(--warning);">Late: <span id="cnt-late">0</span></div>
            <div style="background: #F1F5F9; padding: 8px 15px; border-radius: 8px; font-weight: 600; color: var(--danger);">Absent: <span id="cnt-absent">0</span></div>
            <div style="background: #F1F5F9; padding: 8px 15px; border-radius: 8px; font-weight: 600; color: var(--text-muted);">Excused: <span id="cnt-excused">0</span></div>
        </div>

        <?php if(count($students) > 0): ?>
        <form id="attendanceForm" action="../actions/save_attendance.php" method="POST">
            <!-- Pass hidden required data for saving -->
            <input type="hidden" name="subject_id" value="<?php echo $selected_subject_id; ?>">
            <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($selected_date); ?>">

            <table class="big-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>Attendance Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): 
                        $current_status = isset($existing[$s['id']]) ? $existing[$s['id']] : 'Present';
                    ?>
                    <tr>
                        <td><strong style="color: var(--primary);"><?php echo $s['student_no']; ?></strong></td>
                        <td><strong><?php echo $s['last_name'] . ", " . $s['first_name'] . " " . (isset($s['middle_initial']) ? $s['middle_initial'] : ''); ?></strong></td>
                        <td><span style="font-size: 0.85rem; color: var(--text-muted); border: 1px solid #ccc; padding: 2px 6px; border-radius: 4px;"><?php echo isset($s['enrollment_status']) ? $s['enrollment_status'] : 'Regular'; ?></span></td>
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
        <?php else: ?>
            <p style="color: var(--text-muted); text-align: center; padding: 30px; font-size: 1.1rem;">No students found for this subject's section.</p>
        <?php endif; ?>

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
        let elP = document.getElementById('cnt-present'); if(elP) elP.textContent = present;
        let elL = document.getElementById('cnt-late'); if(elL) elL.textContent = late;
        let elA = document.getElementById('cnt-absent'); if(elA) elA.textContent = absent;
        let elE = document.getElementById('cnt-excused'); if(elE) elE.textContent = excused;
    }

    let attForm = document.getElementById('attendanceForm');
    if(attForm) {
        attForm.addEventListener('change', updateCounts);
        updateCounts(); // Initial run
    }
    </script>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>