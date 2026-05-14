<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';
include '../includes/header.php'; 

// Fetch current students
$query = "SELECT * FROM students ORDER BY id DESC";
$stmt = $conn->query($query);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the next auto-incrementing ID for the student number counter
$count_query = "SELECT COUNT(*) as total FROM students";
$count_stmt = $conn->query($count_query);
$total_students = $count_stmt->fetchColumn();
$next_counter = str_pad($total_students + 1, 4, '0', STR_PAD_LEFT);
?>

<div class="container">
    <div class="page-header">
        <h2 class="page-title">Registration</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Add new students to the system.</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 30px;">
        <div class="card">
            
            <?php if(isset($_GET['success'])): ?>
                <div style="background: #D1FAE5; color: #065F46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">✔ Student registered successfully!</div>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <div style="background: #FEE2E2; color: #991B1B; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">✖ Error: <?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <form action="../actions/save_student.php" method="POST">
                
                <h3 style="color: var(--primary); margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Academic Info</h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
                    <div class="form-group">
                        <label>Admission Year</label>
                        <select id="adm_year" onchange="updateStudentNo()">
                            <?php 
                            $current_year = date("Y");
                            for($y = 2023; $y <= $current_year + 1; $y++) {
                                $sel = ($y == $current_year) ? 'selected' : '';
                                echo "<option value='$y' $sel>$y</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Student Number (Auto)</label>
                        <input type="text" id="display_student_no" readonly style="background: #E5E7EB; color: #4B5563; font-weight: 700;">
                        <input type="hidden" name="student_no" id="real_student_no">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
                    <div class="form-group">
                        <label>Year Level</label>
                        <select name="year_level" required>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Section</label>
                        <select name="section" required>
                            <?php 
                            // Generate A-Z
                            foreach(range('A', 'Z') as $char) echo "<option value='$char'>$char</option>";
                            // Generate AA-AZ if needed
                            foreach(range('A', 'Z') as $char) echo "<option value='A$char'>A$char</option>";
                            ?>
                        </select>
                    </div>
                </div>

                <h3 style="color: var(--primary); margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Personal Info</h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Middle Initial</label>
                        <input type="text" name="middle_initial" maxlength="2" placeholder="e.g. M.">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 10px; width: 66%;">
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Enrollment Status</label>
                        <select name="enrollment_status" required>
                            <option value="Regular">Regular</option>
                            <option value="Irregular">Irregular</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 25px; display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-primary">Save Student Record</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <div class="card">
            <h3 style="color: var(--text-main); margin-bottom: 15px;">Currently Enrolled Students</h3>
            <table class="big-table">
                <thead>
                    <tr>
                        <th>ID No.</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Year & Section</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($students) > 0): ?>
                        <?php foreach($students as $s): ?>
                        <tr>
                            <td><strong style="color: var(--primary);"><?php echo $s['student_no']; ?></strong></td>
                            <td>
                                <strong><?php echo htmlspecialchars($s['last_name'] . ", " . $s['first_name'] . " " . $s['middle_initial']); ?></strong>
                            </td>
                            <td><?php echo isset($s['gender']) ? $s['gender'] : '-'; ?></td>
                            <td>
                                <span style="background: var(--bg-color); padding: 5px 10px; border-radius: 6px; font-weight: 600; font-size: 0.85rem;">
                                    <?php 
                                        $yl = isset($s['year_level']) ? $s['year_level'] : '?';
                                        echo $yl . "-" . $s['section']; 
                                    ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_student.php?id=<?php echo $s['id']; ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 20px;">No students enrolled yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const nextCounter = "<?php echo $next_counter; ?>";
    
    function updateStudentNo() {
        const year = document.getElementById('adm_year').value;
        const studentNo = year + "-" + nextCounter;
        document.getElementById('display_student_no').value = studentNo;
        document.getElementById('real_student_no').value = studentNo;
    }

    document.addEventListener('DOMContentLoaded', updateStudentNo);
</script>

<?php include '../includes/footer.php'; ?>