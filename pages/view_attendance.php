<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';
include '../includes/header.php'; 

$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

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

<style>
.summary-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; }
.summary-card { padding: 15px 20px; border-radius: 10px; color: white; display: flex; flex-direction: column; justify-content: center; }
.summary-card small { font-size: 0.75rem; font-weight: 600; opacity: 0.9; }
.summary-card .value { font-size: 2rem; font-weight: 700; margin-top: 5px; }
.sc-total { background-color: var(--primary); }
.sc-present { background-color: var(--success); }
.sc-absent { background-color: var(--danger); }
.sc-late { background-color: var(--warning); }
.filter-form { display: flex; gap: 15px; align-items: flex-end; }
.filter-form > div { flex: 1; display: flex; flex-direction: column; }
.filter-form label { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 5px; }
.filter-form input { padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); }
th { background-color: #F8FAFC; color: var(--primary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; }
@media print {
    .sidebar, .filter-card, .print-btn, .export-btn { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 0 !important; }
}
</style>

<div class="page-header">
    <h2 class="page-title">Attendance History</h2>
</div>

<div class="card filter-card" style="margin-bottom: 25px;">
    <form method="GET" class="filter-form">
        <div>
            <label>Search Student</label>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name or Student ID...">
        </div>
        <div>
            <label>Filter by Date</label>
            <input type="date" name="date" value="<?php echo $filter_date; ?>">
        </div>
        <div style="flex: 0; display: flex; gap: 10px; flex-direction: row; align-items: center;">
            <button type="submit" class="btn btn-primary" style="height: 42px;">Filter</button>
            <a href="view_attendance.php" class="btn btn-secondary" style="height: 42px; line-height: 22px;">Reset</a>
        </div>
    </form>
</div>

<div class="summary-cards">
    <div class="summary-card sc-total">
        <small>TOTAL RECORDS</small>
        <div class="value"><?php echo $total_count; ?></div>
    </div>
    <div class="summary-card sc-present">
        <small>PRESENT</small>
        <div class="value"><?php echo $present_count; ?></div>
    </div>
    <div class="summary-card sc-absent">
        <small>ABSENT</small>
        <div class="value"><?php echo $absent_count; ?></div>
    </div>
    <div class="summary-card sc-late">
        <small>LATE / EXCUSED</small>
        <div class="value"><?php echo $late_excused; ?></div>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="color: var(--primary);">Attendance Log</h3>
        <div style="display: flex; gap: 10px;">
            <a href="../actions/export_excel.php?search=<?php echo urlencode($search); ?>&date=<?php echo $filter_date; ?>" class="btn btn-success export-btn" style="background-color: #0F9D58; color: white;">Export Excel</a>
            <button onclick="window.print()" class="btn btn-primary print-btn">Save as PDF</button>
        </div>
    </div>

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
                    <td><strong><?php echo $r['student_no']; ?></strong></td>
                    <td><?php echo $r['last_name'] . ", " . $r['first_name']; ?></td>
                    <td><?php echo $r['section']; ?></td>
                    <td>
                        <?php 
                            $status = $r['status'];
                            $color = 'var(--text-muted)'; 
                            if($status == 'Present') $color = 'var(--success)';
                            elseif($status == 'Absent') $color = 'var(--danger)';
                            elseif($status == 'Late') $color = 'var(--warning)';
                            
                            echo "<span style='color: $color; font-weight: 600;'>$status</span>";
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">No records found for those filters.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>