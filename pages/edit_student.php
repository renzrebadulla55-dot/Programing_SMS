<?php 
require_once '../includes/session.php';
require_once '../config/database.php';
include '../includes/header.php'; 

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="page-header">
        <h2 class="page-title">Edit Registration Record</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Update student details.</p>
    </div>

    <div class="card">
        <form action="../actions/update_student.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label>Student Number (Read Only)</label>
                    <input type="text" name="student_no" value="<?php echo htmlspecialchars($student['student_no']); ?>" readonly style="background: #E5E7EB; font-weight: 700;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Middle Initial</label>
                    <input type="text" name="middle_initial" value="<?php echo htmlspecialchars(isset($student['middle_initial']) ? $student['middle_initial'] : ''); ?>" maxlength="2">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="Male" <?php echo (isset($student['gender']) && $student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo (isset($student['gender']) && $student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Year Level</label>
                    <select name="year_level" required>
                        <?php 
                        $yl = isset($student['year_level']) ? $student['year_level'] : '1';
                        for($i=1; $i<=4; $i++) {
                            $sel = ($yl == $i) ? 'selected' : '';
                            echo "<option value='$i' $sel>Year $i</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Section</label>
                    <select name="section" required>
                        <?php 
                        $all_secs = array_merge(range('A', 'Z'));
                        foreach(range('A', 'Z') as $c) $all_secs[] = "A".$c;
                        foreach($all_secs as $sec) {
                            $sel = ($student['section'] == $sec) ? 'selected' : '';
                            echo "<option value='$sec' $sel>$sec</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Enrollment Status</label>
                    <select name="enrollment_status" required>
                        <option value="Regular" <?php echo (isset($student['enrollment_status']) && $student['enrollment_status'] == 'Regular') ? 'selected' : ''; ?>>Regular</option>
                        <option value="Irregular" <?php echo (isset($student['enrollment_status']) && $student['enrollment_status'] == 'Irregular') ? 'selected' : ''; ?>>Irregular</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">Update Record</button>
                <a href="add_student.php" class="btn btn-secondary" style="margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>