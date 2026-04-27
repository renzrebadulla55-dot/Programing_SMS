<?php 
require_once '../includes/session.php';
require_once '../config/database.php';
include '../includes/header.php'; 

$id = $_GET['id'];

// 1. Fetch Student Details
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

// 2. Fetch Attendance Statistics
$stats_stmt = $conn->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent,
    SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late
    FROM attendance WHERE student_id = ?");
$stats_stmt->execute([$id]);
$stats = $stats_stmt->fetch();

// Calculate Percentage
$attendance_rate = ($stats['total'] > 0) ? round(($stats['present'] / $stats['total']) * 100) : 0;

// 3. Fetch Full Attendance History for this student
$history_stmt = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? ORDER BY attendance_date DESC");
$history_stmt->execute([$id]);
$history = $history_stmt->fetchAll();
?>

<div class="container">
    <div class="card" style="display: flex; align-items: center; gap: 30px; border-bottom: 4px solid var(--primary);">
        <div style="width: 100px; height: 100px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem;">
            👤
        </div>
        <div>
            <h1 style="margin: 0;"><?php echo $student['last_name'] . ", " . $student['first_name']; ?></h1>
            <p style="color: var(--text-muted); margin-top: 5px;">ID: <?php echo $student['student_no']; ?> | Section: <?php echo $student['section']; ?></p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 20px;">
        <div class="card" style="text-align: center;">
            <small style="color: var(--text-muted);">Attendance Rate</small>
            <div style="font-size: 1.5rem; font-weight: bold; color: var(--primary);"><?php echo $attendance_rate; ?>%</div>
        </div>
        <div class="card" style="text-align: center;">
            <small style="color: var(--text-muted);">Present</small>
            <div style="font-size: 1.5rem; font-weight: bold; color: var(--success);"><?php echo $stats['present']; ?></div>
        </div>
        <div class="card" style="text-align: center;">
            <small style="color: var(--text-muted);">Late</small>
            <div style="font-size: 1.5rem; font-weight: bold; color: #f59e0b;"><?php echo $stats['late']; ?></div>
        </div>
        <div class="card" style="text-align: center;">
            <small style="color: var(--text-muted);">Absent</small>
            <div style="font-size: 1.5rem; font-weight: bold; color: var(--danger);"><?php echo $stats['absent']; ?></div>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Personal Attendance Log</h3>
        <table style="margin-top: 15px;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($history as $row): ?>
                <tr>
                    <td><?php echo date('F d, Y', strtotime($row['attendance_date'])); ?></td>
                    <td>
                        <strong style="color: <?php 
                            if($row['status'] == 'Present') echo 'var(--success)';
                            elseif($row['status'] == 'Absent') echo 'var(--danger)';
                            else echo '#f59e0b';
                        ?>">
                            <?php echo $row['status']; ?>
                        </strong>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>