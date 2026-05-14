<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';
include '../includes/header.php'; 

$today = date('Y-m-d');

// Total Students
$count_students = $conn->query("SELECT COUNT(*) FROM students")->fetchColumn();

// Present Today
$present_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Present'")->fetchColumn();

// Late Today
$late_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Late'")->fetchColumn();

// Absent Today
$absent_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Absent'")->fetchColumn();

// Graph data: last 7 days
$dates = [];
for ($i = 6; $i >= 0; $i--) {
    $dates[] = date('Y-m-d', strtotime("-$i days"));
}

$labels = [];
$present_counts = [];
$late_counts    = [];
$absent_counts  = [];

foreach ($dates as $date) {
    $labels[] = date('M d', strtotime($date));
    $p = $conn->prepare("SELECT COUNT(*) FROM attendance WHERE attendance_date = ? AND status = 'Present'");
    $p->execute([$date]); $present_counts[] = (int)$p->fetchColumn();
    $l = $conn->prepare("SELECT COUNT(*) FROM attendance WHERE attendance_date = ? AND status = 'Late'");
    $l->execute([$date]); $late_counts[] = (int)$l->fetchColumn();
    $a = $conn->prepare("SELECT COUNT(*) FROM attendance WHERE attendance_date = ? AND status = 'Absent'");
    $a->execute([$date]); $absent_counts[] = (int)$a->fetchColumn();
}

// Recent attendance records (last 5)
$recent_stmt = $conn->query("SELECT s.first_name, s.last_name, s.section, a.status, a.attendance_date
    FROM attendance a JOIN students s ON a.student_id = s.id
    ORDER BY a.attendance_date DESC, a.id DESC LIMIT 5");
$recent = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container">

    <!-- Page Header -->
    <div class="dash-header">
        <h2 class="page-title">Analytics & Status</h2>
        <div class="header-time" id="live-time">
            <?php echo date('l, F j, Y'); ?> - <span></span>
        </div>
    </div>

    <!-- Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 30px;">
        <?php
        $cards = [
            ['icon' => '👨‍🎓', 'label' => 'Total Students', 'value' => $count_students, 'color' => 'var(--primary)'],
            ['icon' => '✅', 'label' => 'Present Today', 'value' => $present_today, 'color' => 'var(--success)'],
            ['icon' => '⏱️', 'label' => 'Late Today', 'value' => $late_today, 'color' => 'var(--warning)'],
            ['icon' => '❌', 'label' => 'Absent Today', 'value' => $absent_today, 'color' => 'var(--danger)'],
        ];

        foreach ($cards as $card): ?>
            <div class="card" style="border-top: 5px solid <?php echo $card['color']; ?>; display: flex; align-items: center; gap: 20px; padding: 20px;">
                <div style="font-size: 2.5rem;"><?php echo $card['icon']; ?></div>
                <div>
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;"><?php echo $card['label']; ?></div>
                    <div style="font-size: 2.2rem; font-weight: 700; color: var(--text-main); line-height: 1.1; margin-top: 5px;"><?php echo $card['value']; ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Chart + Recent side by side -->
    <div style="display:grid;grid-template-columns:2fr 1.2fr;gap:24px;margin-bottom:30px;">

        <!-- Attendance Chart -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 1.1rem; color: var(--text-main);">Weekly Attendance Overview</h3>
                <span style="font-size: 0.8rem; background: #F3F4F6; padding: 5px 10px; border-radius: 6px; font-weight: 600;">Last 7 Days</span>
            </div>
            <div style="height: 250px; position: relative;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Recent Records -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="font-size: 1.1rem; color: var(--text-main);">Recent Activity </h3>
                <a href="view_attendance.php" style="font-size: 0.85rem; color: var(--primary); text-decoration: none; font-weight: 600;">View All</a>
            </div>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php if (count($recent) === 0): ?>
                    <p style="color:var(--text-muted); font-size: 0.9rem;">No records yet.</p>
                <?php endif; ?>
                <?php foreach ($recent as $r):
                    $status_color = 'var(--text-muted)';
                    if($r['status'] == 'Present') $status_color = 'var(--success)';
                    if($r['status'] == 'Late') $status_color = 'var(--warning)';
                    if($r['status'] == 'Absent') $status_color = 'var(--danger)';
                ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px;">
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem; color: var(--text-main);"><?php echo htmlspecialchars($r['last_name'].', '.$r['first_name']); ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 3px;"><?php echo date('M d, Y', strtotime($r['attendance_date'])); ?></div>
                    </div>
                    <div style="font-weight: 700; font-size: 0.85rem; color: <?php echo $status_color; ?>; background: <?php echo $status_color; ?>1A; padding: 5px 12px; border-radius: 20px;">
                        <?php echo $r['status']; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- Quick Actions -->
    <div class="card">
        <h3 style="font-size: 1.1rem; margin-bottom: 15px;">Quick Actions</h3>
        <div style="display: flex; gap: 15px;">
            <a href="mark_attendance.php" class="btn btn-primary" style="flex: 1;">📋 Mark Daily Attendance</a>
            <a href="add_student.php" class="btn btn-secondary" style="flex: 1; border-color: var(--primary); color: var(--primary);">👤 Register New Student</a>
            <a href="system_logs.php" class="btn btn-secondary" style="flex: 1;">⚙️ View System Logs</a>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Live Time
    function updateTime() {
        const now = new Date();
        document.querySelector('#live-time span').textContent = now.toLocaleTimeString();
    }
    setInterval(updateTime, 1000);
    updateTime();

    // Chart
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [
                {
                    label: 'Present',
                    data: <?php echo json_encode($present_counts); ?>,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3, tension: 0.4, fill: true
                },
                {
                    label: 'Late',
                    data: <?php echo json_encode($late_counts); ?>,
                    borderColor: '#F59E0B',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 3, tension: 0.4, fill: true
                },
                {
                    label: 'Absent',
                    data: <?php echo json_encode($absent_counts); ?>,
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3, tension: 0.4, fill: true
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>