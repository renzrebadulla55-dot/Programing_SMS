<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';
include '../includes/header.php'; 

$today = date('Y-m-d');

// Basic Stats
$count_students = $conn->query("SELECT COUNT(*) FROM students")->fetchColumn();
$present_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Present'")->fetchColumn();
$late_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Late'")->fetchColumn();
$absent_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Absent'")->fetchColumn();

// Recent Records
$recent_query = "SELECT a.*, s.first_name, s.last_name 
                 FROM attendance a 
                 JOIN students s ON a.student_id = s.id 
                 ORDER BY a.attendance_date DESC, a.id DESC 
                 LIMIT 5";
$recent_records = $conn->query($recent_query)->fetchAll(PDO::FETCH_ASSOC);

// Fetch Data for the Graph (Last 7 Days)
// We will generate an array of the last 7 dates
$dates = [];
for ($i = 6; $i >= 0; $i--) {
    $dates[] = date('Y-m-d', strtotime("-$i days"));
}

$present_data = [];
$late_data = [];
$absent_data = [];
$labels = [];

foreach ($dates as $d) {
    $labels[] = date('M d', strtotime($d));
    $p = $conn->query("SELECT COUNT(*) FROM attendance WHERE attendance_date = '$d' AND status = 'Present'")->fetchColumn();
    $l = $conn->query("SELECT COUNT(*) FROM attendance WHERE attendance_date = '$d' AND status = 'Late'")->fetchColumn();
    $a = $conn->query("SELECT COUNT(*) FROM attendance WHERE attendance_date = '$d' AND status = 'Absent'")->fetchColumn();
    $present_data[] = (int)$p;
    $late_data[] = (int)$l;
    $absent_data[] = (int)$a;
}
?>

<div class="dashboard-stats">
    <div class="stat-card" style="border-top: 4px solid var(--accent);">
        <div class="stat-title">TOTAL STUDENTS</div>
        <div class="stat-value"><?php echo $count_students; ?></div>
        <div class="stat-desc">Enrolled this term</div>
    </div>
    
    <div class="stat-card" style="border-top: 4px solid var(--success);">
        <div class="stat-title">PRESENT TODAY</div>
        <div class="stat-value"><?php echo $present_today; ?></div>
        <div class="stat-desc"><?php echo $count_students > 0 ? round(($present_today/$count_students)*100) : 0; ?>% attendance rate</div>
    </div>
    
    <div class="stat-card" style="border-top: 4px solid var(--warning);">
        <div class="stat-title">LATE TODAY</div>
        <div class="stat-value"><?php echo $late_today; ?></div>
        <div class="stat-desc">Tardiness records</div>
    </div>
    
    <div class="stat-card" style="border-top: 4px solid var(--danger);">
        <div class="stat-title">ABSENT TODAY</div>
        <div class="stat-value"><?php echo $absent_today; ?></div>
        <div class="stat-desc">Missing students</div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Chart Section -->
    <div class="card chart-card">
        <div class="chart-header">
            <h3>Weekly Attendance</h3>
            <span style="font-size: 0.75rem; color: var(--accent); font-weight: 600;">Last 7 days</span>
        </div>
        <div style="flex: 1; min-height: 250px; position: relative;">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <!-- Recent Records Section -->
    <div class="card recent-records-card">
        <div class="recent-records-header">
            <h3>Recent Records</h3>
            <a href="view_attendance.php" style="font-size: 0.75rem; color: var(--accent); text-decoration: none; font-weight: 600;">View all</a>
        </div>
        <div class="recent-records-list">
            <?php if (count($recent_records) > 0): ?>
                <?php foreach ($recent_records as $rec): ?>
                    <?php 
                        $status_class = 'status-' . strtolower($rec['status']);
                        $initials = substr($rec['first_name'], 0, 1) . substr($rec['last_name'], 0, 1);
                        // Randomize avatar color slightly based on id
                        $colors = ['#4452FE', '#0F9D58', '#F4B400', '#DB4437', '#9C27B0'];
                        $bgColor = $colors[$rec['student_id'] % 5];
                    ?>
                    <div class="record-item">
                        <div class="record-info">
                            <div class="record-avatar" style="background-color: <?php echo $bgColor; ?>;">
                                <?php echo strtoupper($initials); ?>
                            </div>
                            <div class="record-details">
                                <span class="record-name"><?php echo htmlspecialchars($rec['last_name'] . ', ' . $rec['first_name']); ?></span>
                                <span class="record-date"><?php echo date('M d', strtotime($rec['attendance_date'])); ?></span>
                            </div>
                        </div>
                        <div class="status-badge <?php echo $status_class; ?>">
                            <?php echo $rec['status']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; margin-top: 20px;">No recent records found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card quick-actions-card">
    <h3 style="color: var(--primary); font-size: 1.1rem;">Quick Actions</h3>
    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;">Use the shortcuts below to manage your system.</p>
    
    <div class="quick-actions-grid">
        <a href="mark_attendance.php" class="action-box">
            <span style="font-size: 1.5rem; margin-bottom: 5px;">📋</span>
            Mark Today's Attendance
        </a>
        <a href="add_student.php" class="action-box">
            <span style="font-size: 1.5rem; margin-bottom: 5px;">👤</span>
            Add New Student
        </a>
        <a href="view_attendance.php" class="action-box">
            <span style="font-size: 1.5rem; margin-bottom: 5px;">📊</span>
            View Attendance History
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    
    const labels = <?php echo json_encode($labels); ?>;
    const presentData = <?php echo json_encode($present_data); ?>;
    const lateData = <?php echo json_encode($late_data); ?>;
    const absentData = <?php echo json_encode($absent_data); ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Present',
                    data: presentData,
                    borderColor: '#0F9D58',
                    backgroundColor: 'rgba(15, 157, 88, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#0F9D58',
                    pointRadius: 4
                },
                {
                    label: 'Late',
                    data: lateData,
                    borderColor: '#F4B400',
                    backgroundColor: 'rgba(244, 180, 0, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#F4B400',
                    pointRadius: 4
                },
                {
                    label: 'Absent',
                    data: absentData,
                    borderColor: '#DB4437',
                    backgroundColor: 'rgba(219, 68, 55, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#DB4437',
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        color: '#9ca3af'
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    ticks: {
                        color: '#9ca3af'
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>