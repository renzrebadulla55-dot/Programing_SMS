<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';
include '../includes/header.php'; 

$today = date('Y-m-d');
$role = $_SESSION['role'] ?? 'admin';
$user_id = $_SESSION['user_id'];

// Determine Greeting based on PH Time
date_default_timezone_set('Asia/Manila');
$hour = date('H');
if ($hour < 12) $greeting = "Good Morning";
elseif ($hour < 18) $greeting = "Good Afternoon";
else $greeting = "Good Evening";

$show_banner = false;
if (isset($_SESSION['just_logged_in']) && $_SESSION['just_logged_in']) {
    $show_banner = true;
    unset($_SESSION['just_logged_in']);
}

if ($role === 'admin') {
    // ADMIN DASHBOARD
    $count_students = $conn->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $present_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Present'")->fetchColumn();
    $late_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Late'")->fetchColumn();
    $absent_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Absent'")->fetchColumn();
    
    // Graph
    $dates = []; for ($i = 6; $i >= 0; $i--) $dates[] = date('Y-m-d', strtotime("-$i days"));
    $labels = []; $present_counts = []; $late_counts = []; $absent_counts = [];
    foreach ($dates as $date) {
        $labels[] = date('M d', strtotime($date));
        $present_counts[] = (int)$conn->query("SELECT COUNT(*) FROM attendance WHERE attendance_date = '$date' AND status = 'Present'")->fetchColumn();
        $late_counts[] = (int)$conn->query("SELECT COUNT(*) FROM attendance WHERE attendance_date = '$date' AND status = 'Late'")->fetchColumn();
        $absent_counts[] = (int)$conn->query("SELECT COUNT(*) FROM attendance WHERE attendance_date = '$date' AND status = 'Absent'")->fetchColumn();
    }
    
    $recent_stmt = $conn->query("SELECT s.first_name, s.last_name, a.status, a.attendance_date, sub.subject_name FROM attendance a JOIN students s ON a.student_id = s.id LEFT JOIN subjects sub ON a.subject_id = sub.id ORDER BY a.id DESC LIMIT 5");
} else {
    // PROFESSOR DASHBOARD
    // Get their subjects
    $sub_stmt = $conn->prepare("SELECT id FROM subjects WHERE professor_id = ?");
    $sub_stmt->execute([$user_id]);
    $my_subjects = $sub_stmt->fetchAll(PDO::FETCH_COLUMN);
    $sub_in = implode(',', empty($my_subjects) ? [0] : $my_subjects);

    // Total Students across their subjects (distinct by year_level)
    $count_students = $conn->query("SELECT COUNT(DISTINCT s.id) FROM students s JOIN subjects sub ON s.year_level = sub.year_level WHERE sub.id IN ($sub_in)")->fetchColumn();
    $present_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Present' AND subject_id IN ($sub_in)")->fetchColumn();
    $late_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Late' AND subject_id IN ($sub_in)")->fetchColumn();
    $absent_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Absent' AND subject_id IN ($sub_in)")->fetchColumn();
    
    $dates = []; for ($i = 6; $i >= 0; $i--) $dates[] = date('Y-m-d', strtotime("-$i days"));
    $labels = []; $present_counts = []; $late_counts = []; $absent_counts = [];
    foreach ($dates as $date) {
        $labels[] = date('M d', strtotime($date));
        $present_counts[] = (int)$conn->query("SELECT COUNT(*) FROM attendance WHERE attendance_date = '$date' AND status = 'Present' AND subject_id IN ($sub_in)")->fetchColumn();
        $late_counts[] = (int)$conn->query("SELECT COUNT(*) FROM attendance WHERE attendance_date = '$date' AND status = 'Late' AND subject_id IN ($sub_in)")->fetchColumn();
        $absent_counts[] = (int)$conn->query("SELECT COUNT(*) FROM attendance WHERE attendance_date = '$date' AND status = 'Absent' AND subject_id IN ($sub_in)")->fetchColumn();
    }
    
    $recent_stmt = $conn->query("SELECT s.first_name, s.last_name, a.status, a.attendance_date, sub.subject_name FROM attendance a JOIN students s ON a.student_id = s.id JOIN subjects sub ON a.subject_id = sub.id WHERE sub.id IN ($sub_in) ORDER BY a.id DESC LIMIT 5");
}

$recent = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container" style="position: relative;">

    <!-- Welcome Login Banner -->
    <?php if($show_banner): ?>
    <div id="welcome-banner" style="position: fixed; top: 30px; left: 50%; transform: translateX(-50%); z-index: 9999; background: #10B981; color: white; padding: 15px 30px; border-radius: 50px; font-weight: 700; box-shadow: 0 10px 25px rgba(16,185,129,0.3); animation: slideDown 0.5s ease forwards, slideUp 0.5s ease 4s forwards;">
        👋 <?php echo $greeting . ", " . ucfirst($role); ?>! Login success.
    </div>
    <style>
        @keyframes slideDown { from { top: -50px; opacity: 0; } to { top: 30px; opacity: 1; } }
        @keyframes slideUp { from { top: 30px; opacity: 1; } to { top: -50px; opacity: 0; visibility: hidden; } }
    </style>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="dash-header">
        <h2 class="page-title"><?php echo ($role === 'admin') ? "System Analytics" : "My Classes Analytics"; ?></h2>
        <div class="header-time" id="live-time">
            <?php echo date('l, F j, Y'); ?> - <span></span>
        </div>
    </div>

    <!-- Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 30px;">
        <?php
        $cards = [
            ['icon' => '👨‍🎓', 'label' => ($role==='admin' ? 'Total Students' : 'My Students'), 'value' => $count_students, 'color' => 'var(--primary)'],
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
                <h3 style="font-size: 1.1rem; color: var(--text-main);">Recent Activity</h3>
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
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 3px;"><?php echo htmlspecialchars($r['subject_name'] ?? 'General') . " • " . date('M d', strtotime($r['attendance_date'])); ?></div>
                    </div>
                    <div style="font-weight: 700; font-size: 0.85rem; color: <?php echo $status_color; ?>; background: <?php echo $status_color; ?>1A; padding: 5px 12px; border-radius: 20px;">
                        <?php echo $r['status']; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
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