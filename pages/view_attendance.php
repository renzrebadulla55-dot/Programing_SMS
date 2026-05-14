<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';
include '../includes/header.php'; 

$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
$filter_year = isset($_GET['year']) ? $_GET['year'] : '';
$filter_section = isset($_GET['section']) ? $_GET['section'] : '';

$query = "SELECT s.student_no, s.first_name, s.last_name, s.middle_initial, s.section, s.year_level, a.status, a.attendance_date 
          FROM attendance a 
          JOIN students s ON a.student_id = s.id 
          WHERE 1=1"; 

$params = [];

if (!empty($filter_date)) {
    $query .= " AND a.attendance_date = :date";
    $params[':date'] = $filter_date;
}

if (!empty($filter_year)) {
    $query .= " AND s.year_level = :year";
    $params[':year'] = $filter_year;
}

if (!empty($filter_section)) {
    $query .= " AND s.section = :section";
    $params[':section'] = $filter_section;
}

$query .= " ORDER BY a.attendance_date DESC, s.last_name ASC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_count = count($records);
$present_count = 0; $absent_count = 0; $late_excused = 0;

foreach ($records as $row) {
    if ($row['status'] == 'Present') $present_count++;
    elseif ($row['status'] == 'Absent') $absent_count++;
    else $late_excused++; 
}

$avg_attendance = ($total_count > 0) ? round(($present_count / $total_count) * 100, 1) : 0;
$show_avg = (!empty($filter_year) && !empty($filter_section));
?>

<div class="container">
    <div class="page-header">
        <h2 class="page-title">Attendance History</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Filter and review past attendance logs.</p>
    </div>

    <!-- Filters Card -->
    <div class="card" style="margin-bottom: 25px; background: #F8FAFC; border: 1px solid #E2E8F0;">
        <form method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
            <div style="flex: 1;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 5px; display: block;">Filter by Date</label>
                <input type="date" name="date" value="<?php echo $filter_date; ?>">
            </div>
            <div style="flex: 1;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 5px; display: block;">Year Level</label>
                <select name="year">
                    <option value="">All Years</option>
                    <option value="1" <?php if($filter_year=='1') echo 'selected'; ?>>1st Year</option>
                    <option value="2" <?php if($filter_year=='2') echo 'selected'; ?>>2nd Year</option>
                    <option value="3" <?php if($filter_year=='3') echo 'selected'; ?>>3rd Year</option>
                    <option value="4" <?php if($filter_year=='4') echo 'selected'; ?>>4th Year</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 5px; display: block;">Section</label>
                <select name="section">
                    <option value="">All Sections</option>
                    <?php 
                    $all_secs = array_merge(range('A', 'Z'));
                    foreach(range('A', 'Z') as $c) $all_secs[] = "A".$c;
                    foreach($all_secs as $sec) {
                        $sel = ($filter_section == $sec) ? 'selected' : '';
                        echo "<option value='$sec' $sel>$sec</option>";
                    }
                    ?>
                </select>
            </div>
            <div style="flex: 0; display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="view_attendance.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Summary Statistics -->
    <div style="display: grid; grid-template-columns: repeat(<?php echo $show_avg ? '5' : '4'; ?>, 1fr); gap: 20px; margin-bottom: 25px;">
        <div class="card" style="background: var(--primary); color: white; padding: 20px;">
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.9;">TOTAL RECORDS</div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-top: 5px;"><?php echo $total_count; ?></div>
        </div>
        <div class="card" style="background: var(--success); color: white; padding: 20px;">
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.9;">PRESENT</div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-top: 5px;"><?php echo $present_count; ?></div>
        </div>
        <div class="card" style="background: var(--warning); color: white; padding: 20px;">
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.9;">LATE / EXCUSED</div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-top: 5px;"><?php echo $late_excused; ?></div>
        </div>
        <div class="card" style="background: var(--danger); color: white; padding: 20px;">
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.9;">ABSENT</div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-top: 5px;"><?php echo $absent_count; ?></div>
        </div>
        <?php if($show_avg): ?>
        <div class="card" style="background: #0EA5E9; color: white; padding: 20px;">
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.9;">CLASS AVERAGE</div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-top: 5px;"><?php echo $avg_attendance; ?>%</div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: var(--text-main);">Attendance Log</h3>
            
            <div style="display: flex; gap: 15px; align-items: center;">
                <input type="text" id="nameSearch" placeholder="Search by name..." style="width: 250px; padding: 8px 12px; font-size: 0.9rem;" onkeyup="filterTable()">
                <a href="../actions/export_excel.php" class="btn btn-secondary" style="background-color: #10B981; color: white; border: none;">Export Excel</a>
                <button onclick="window.print()" class="btn btn-primary">Save as PDF</button>
            </div>
        </div>

        <table class="big-table" id="attendanceTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Year & Section</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($records) > 0): ?>
                    <?php foreach($records as $r): ?>
                    <tr class="data-row">
                        <td style="font-weight: 500; color: var(--text-muted);"><?php echo date('M d, Y', strtotime($r['attendance_date'])); ?></td>
                        <td><strong style="color: var(--primary);"><?php echo $r['student_no']; ?></strong></td>
                        <td class="student-name" style="font-weight: 600; font-size: 1.05rem;">
                            <?php echo htmlspecialchars($r['last_name'] . ", " . $r['first_name'] . " " . (isset($r['middle_initial']) ? $r['middle_initial'] : '')); ?>
                        </td>
                        <td style="font-weight: 600;">
                            <span style="background: #F3F4F6; padding: 4px 8px; border-radius: 6px; font-size: 0.9rem;">
                                <?php echo (isset($r['year_level']) ? $r['year_level'] : '1') . "-" . $r['section']; ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                                $status = $r['status'];
                                $color = 'var(--text-muted)'; 
                                if($status == 'Present') $color = 'var(--success)';
                                elseif($status == 'Absent') $color = 'var(--danger)';
                                elseif($status == 'Late') $color = 'var(--warning)';
                                
                                echo "<span style='color: $color; font-weight: 700; background: {$color}1A; padding: 6px 12px; border-radius: 20px; font-size: 0.9rem;'>$status</span>";
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px; font-size: 1.1rem;">No records found for those filters.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filterTable() {
    let input = document.getElementById("nameSearch").value.toLowerCase();
    let rows = document.querySelectorAll("#attendanceTable .data-row");

    rows.forEach(row => {
        let nameCell = row.querySelector(".student-name").textContent.toLowerCase();
        if (nameCell.includes(input)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>