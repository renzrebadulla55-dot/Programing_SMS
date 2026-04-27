<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';
include '../includes/header.php'; 

// 1. Fetch Basic Stats
$today = date('Y-m-d');

// Count Total Students
$count_students = $conn->query("SELECT COUNT(*) FROM students")->fetchColumn();

// Count Unique Students Present Today
$present_today = $conn->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE attendance_date = '$today' AND status = 'Present'")->fetchColumn();

// 2. Fetch Data for the Graph (Last 7 Days of Presence)
$graph_query = "SELECT attendance_date, COUNT(*) as count 
                FROM attendance 
                WHERE status = 'Present' 
                GROUP BY attendance_date 
                ORDER BY attendance_date DESC 
                LIMIT 7";
$graph_stmt = $conn->query($graph_query);
$graph_rows = $graph_stmt->fetchAll(PDO::FETCH_ASSOC);

// Format data for Chart.js (reverse so it goes left-to-right)
$labels = [];
$counts = [];
foreach(array_reverse($graph_rows) as $row) {
    $labels[] = date('M d', strtotime($row['attendance_date']));
    $counts[] = (int)$row['count'];
}
?>

<div class="container">
    <div>
        <h2>Dashboard Overview</h2>
        <p>Welcome back, <?php echo $_SESSION['username']; ?>!</p>
    </div>

    <div class="grid">
        <div class="card" style="border-left-color: var(--primary);">
            <div>Total Enrolled Students</div>
            <div class="stat"><?php echo $count_students; ?></div>
        </div>

        <div class="card" style="border-left-color: var(--success);">
            <div>Students Present Today</div>
            <div class="stat"><?php echo $present_today; ?></div>
        </div>
    </div>

    <div class="card">
        <h3>Attendance</h3>
        <div style="height: 350px; width: 100%;">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <div class="card">
        <h3>Quick Actions</h3>
        <p>Use the buttons below to quickly manage your system.</p>
        <div class="quick-actions">
            <a href="mark_attendance.php" class="btn btn-primary">Mark Today's Attendance</a>
            <a href="add_student.php" class="btn btn-secondary">Add New Student</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    
    // PHP arrays converted to JS arrays
    const labels = <?php echo json_encode($labels); ?>;
    const dataCounts = <?php echo json_encode($counts); ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Students Present',
                data: dataCounts,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(0, 255, 76, 0.2)',
                borderWidth: 3,
                tension: 0.4, // Curvy lines
                fill: true,
                pointBackgroundColor: '#4f46e5',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                    display: false // We don't need the legend for a single dataset
                }
            }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>