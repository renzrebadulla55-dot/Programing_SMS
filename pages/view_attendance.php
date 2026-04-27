<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';
include '../includes/header.php'; 

// 1. Get filter values from the URL
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

// 2. Build the dynamic SQL Query
$query = "SELECT s.student_no, s.first_name, s.last_name, s.section, a.status, a.attendance_date 
          FROM attendance a 
          JOIN students s ON a.student_id = s.id 
          WHERE 1=1"; 

$params = [];

if (!empty($search)) {
    $query .= " AND (s.first_name LIKE :search OR s.last_name LIKE :search OR s.student_no LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($filter_date)) {
    $query .= " AND a.attendance_date = :date";
    $params[':date'] = $filter_date;
}

$query .= " ORDER BY a.attendance_date DESC, s.last_name ASC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Calculate Summary Stats
$total_count = count($records);
$present_count = 0;
$absent_count = 0;
$late_excused = 0;

foreach ($records as $row) {
    if ($row['status'] == 'Present') $present_count++;
    elseif ($row['status'] == 'Absent') $absent_count++;
    else $late_excused++; 
}
?>

<div class="container">
    <div class="card no-print">
        <form method="GET" class="filter-form">
            <div>
                <label>Search Student</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name or Student ID...">
            </div>
            <div>
                <label>Filter by Date</label>
                <input type="date" name="date" value="<?php echo $filter_date; ?>">
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="view_attendance.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="card">
    <div class="print-only">
        <h1>Student Monitoring System</h1>
        <p>Attendance Report - Generated on <?php echo date('M d, Y'); ?></p>
        <hr>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card total">
            <small>TOTAL</small>
            <div class="value"><?php echo $total_count; ?></div>
        </div>
        <div class="summary-card present">
            <small>PRESENT</small>
            <div class="value"><?php echo $present_count; ?></div>
        </div>
        <div class="summary-card absent">
            <small>ABSENT</small>
            <div class="value"><?php echo $absent_count; ?></div>
        </div>
        <div class="summary-card late-excused">
            <small>LATE/EXCUSED</small>
            <div class="value"><?php echo $late_excused; ?></div>
        </div>
    </div>

    <!-- Export Buttons -->
    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 20px;">
        <a href="../actions/export_excel.php?search=<?php echo $search; ?>&date=<?php echo $filter_date; ?>" 
           class="btn btn-success">Export Excel</a>
        <button onclick="window.print()" class="btn btn-primary">Save as PDF</button>
    </div>

    <!-- Attendance Table -->
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Section</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($records) > 0): ?>
                <?php foreach($records as $r): ?>
                <tr>
                    <td><?php echo date('M d, Y', strtotime($r['attendance_date'])); ?></td>
                    <td><?php echo $r['student_no']; ?></td>
                    <td><?php echo $r['last_name'] . ", " . $r['first_name']; ?></td>
                    <td><?php echo $r['section']; ?></td>
                    <td>
                        <?php 
                            $status = $r['status'];
                            $color = 'var(--success)'; 
                            if($status == 'Absent') $color = 'var(--danger)';
                            elseif($status == 'Late' || $status == 'Excused') $color = '#f59e0b';
                            
                            echo "<strong style='color: $color;'>$status</strong>";
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" align="center">No records found for those filters.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>