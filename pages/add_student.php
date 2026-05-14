<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';
include '../includes/header.php'; 

// Fetch current students grouped by year and section
$query = "SELECT * FROM students ORDER BY year_level ASC, section ASC, last_name ASC";
$stmt = $conn->query($query);
$all_students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group students by Year Level and Section
$grouped_students = [];
foreach ($all_students as $s) {
    $year = $s['year_level'];
    $section = $s['section'];
    if (!isset($grouped_students[$year])) {
        $grouped_students[$year] = [];
    }
    if (!isset($grouped_students[$year][$section])) {
        $grouped_students[$year][$section] = [];
    }
    $grouped_students[$year][$section][] = $s;
}

// Sort sections alphabetically within each year
foreach ($grouped_students as $year => $sections) {
    ksort($grouped_students[$year]);
}

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
            <div style="display: flex; flex-direction: column; gap: 20px; margin-bottom: 25px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="color: var(--text-main); margin: 0;">Student List by Section</h3>
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <div style="position: relative;">
                            <input type="text" id="studentSearch" placeholder="Search students..." 
                                   style="padding: 10px 15px 10px 40px; border-radius: 20px; border: 1px solid var(--border-color); font-size: 0.9rem; width: 300px;"
                                   onkeyup="filterGroupedStudents()">
                            <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); opacity: 0.5;">🔍</span>
                        </div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">Sorted by Year Level & Section</div>
                    </div>
                </div>

                <div class="year-filter-container" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button class="year-btn active" onclick="filterByYear('all', this)" style="padding: 8px 20px; border-radius: 25px; border: 1px solid var(--primary); background: var(--primary); color: white; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">All Students</button>
                    <button class="year-btn" onclick="filterByYear('1', this)" style="padding: 8px 20px; border-radius: 25px; border: 1px solid var(--border-color); background: white; color: var(--text-main); cursor: pointer; font-weight: 600; transition: all 0.3s ease;">1st Year</button>
                    <button class="year-btn" onclick="filterByYear('2', this)" style="padding: 8px 20px; border-radius: 25px; border: 1px solid var(--border-color); background: white; color: var(--text-main); cursor: pointer; font-weight: 600; transition: all 0.3s ease;">2nd Year</button>
                    <button class="year-btn" onclick="filterByYear('3', this)" style="padding: 8px 20px; border-radius: 25px; border: 1px solid var(--border-color); background: white; color: var(--text-main); cursor: pointer; font-weight: 600; transition: all 0.3s ease;">3rd Year</button>
                    <button class="year-btn" onclick="filterByYear('4', this)" style="padding: 8px 20px; border-radius: 25px; border: 1px solid var(--border-color); background: white; color: var(--text-main); cursor: pointer; font-weight: 600; transition: all 0.3s ease;">4th Year</button>
                </div>
            </div>

            <?php if(count($grouped_students) > 0): ?>
                <?php foreach($grouped_students as $year => $sections): ?>
                    <div class="year-group" data-year="<?php echo $year; ?>" style="margin-bottom: 30px;">
                        <div style="background: var(--primary); color: white; padding: 10px 20px; border-radius: 8px; font-weight: 700; margin-bottom: 15px; display: inline-block;">
                            YEAR LEVEL: <?php echo $year; ?>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(450px, 1fr)); gap: 20px;">
                            <?php foreach($sections as $sec => $students): ?>
                                <div class="section-card" data-section="<?php echo $sec; ?>" style="background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; padding: 15px; border-left: 5px solid var(--primary); transition: all 0.3s ease;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px solid var(--border-color); padding-bottom: 5px;">
                                        <h4 style="margin: 0; color: var(--text-main);">Section <?php echo $sec; ?></h4>
                                        <span style="background: rgba(79, 70, 229, 0.1); color: var(--primary); padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">
                                            <span class="sec-count"><?php echo count($students); ?></span> Students
                                        </span>
                                    </div>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                        <thead>
                                            <tr style="text-align: left; color: var(--text-muted);">
                                                <th style="padding: 5px;">ID No.</th>
                                                <th style="padding: 5px;">Last Name</th>
                                                <th style="padding: 5px;">M.I.</th>
                                                <th style="padding: 5px;">First Name</th>
                                                <th style="padding: 5px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="student-tbody">
                                            <?php foreach($students as $s): ?>
                                            <tr class="student-row" data-name="<?php echo strtolower($s['last_name'] . ' ' . $s['first_name']); ?>" style="border-top: 1px solid rgba(0,0,0,0.03);">
                                                <td style="padding: 8px 5px; font-weight: 600; color: var(--primary);"><?php echo $s['student_no']; ?></td>
                                                <td style="padding: 8px 5px; font-weight: 600;"><?php echo htmlspecialchars($s['last_name']); ?></td>
                                                <td style="padding: 8px 5px; font-weight: 600;"><?php echo htmlspecialchars($s['middle_initial']); ?></td>
                                                <td style="padding: 8px 5px; font-weight: 600;"><?php echo htmlspecialchars($s['first_name']); ?></td>
                                                <td style="padding: 8px 5px;">
                                                    <a href="edit_student.php?id=<?php echo $s['id']; ?>" style="color: var(--text-muted); text-decoration: none; font-size: 1.1rem; opacity: 0.7; transition: opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.7" title="Edit">
                                                        ✎
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; color: var(--text-muted); padding: 40px;">No students enrolled yet.</div>
            <?php endif; ?>
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

    function filterByYear(year, btn) {
        // Update button styles
        document.querySelectorAll('.year-btn').forEach(b => {
            b.style.background = 'white';
            b.style.color = 'var(--text-main)';
            b.style.borderColor = 'var(--border-color)';
        });
        btn.style.background = 'var(--primary)';
        btn.style.color = 'white';
        btn.style.borderColor = 'var(--primary)';

        // Filter year groups
        const groups = document.querySelectorAll('.year-group');
        groups.forEach(group => {
            if (year === 'all' || group.getAttribute('data-year') === year) {
                group.style.display = "";
            } else {
                group.style.display = "none";
            }
        });
    }

    function filterGroupedStudents() {
        const input = document.getElementById('studentSearch').value.toLowerCase();
        const sectionCards = document.querySelectorAll('.section-card');

        sectionCards.forEach(card => {
            const rows = card.querySelectorAll('.student-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                if (name.includes(input)) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            });

            // Update visible student count for the section
            card.querySelector('.sec-count').textContent = visibleCount;

            // Hide the entire section card if no students match
            if (visibleCount === 0) {
                card.style.display = "none";
            } else {
                card.style.display = "";
            }
        });
    }

    document.addEventListener('DOMContentLoaded', updateStudentNo);
</script>

<?php include '../includes/footer.php'; ?>