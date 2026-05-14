<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';

// Only professors should access this
if ($_SESSION['role'] !== 'professor') {
    header("Location: dashboard.php");
    exit();
}

include '../includes/header.php'; 

$user_id = $_SESSION['user_id'];

// Fetch Schedule
$stmt = $conn->prepare("SELECT * FROM schedules WHERE professor_id = ? ORDER BY start_time ASC");
$stmt->execute([$user_id]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Map schedules by day
$schedule_map = [
    'Monday' => [], 'Tuesday' => [], 'Wednesday' => [], 
    'Thursday' => [], 'Friday' => [], 'Saturday' => []
];

foreach ($schedules as $s) {
    $schedule_map[$s['day_of_week']][] = $s;
}
?>

<style>
.cal-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 15px;
    margin-top: 20px;
}
.cal-day {
    background: #F8FAFC;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    min-height: 500px;
}
.cal-header {
    text-align: center;
    padding: 15px 0;
    border-bottom: 1px solid var(--border-color);
    font-weight: 700;
    color: var(--primary);
    background: #FFFFFF;
    border-radius: 12px 12px 0 0;
}
.cal-body {
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.class-block {
    background: #FFFFFF;
    border-left: 4px solid var(--primary);
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    transition: transform 0.2s;
}
.class-block:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}
.class-title {
    font-weight: 700;
    color: var(--text-main);
    font-size: 0.95rem;
    margin-bottom: 5px;
}
.class-time {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-bottom: 8px;
    font-weight: 600;
}
.class-sec {
    display: inline-block;
    background: #E0F2FE;
    color: #0284C7;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 700;
}
</style>

<div class="container">
    <div class="page-header">
        <h2 class="page-title">Schedule Overview</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Your weekly assigned classes and subjects.</p>
    </div>

    <div class="cal-grid">
        <?php foreach ($schedule_map as $day => $classes): ?>
            <div class="cal-day">
                <div class="cal-header"><?php echo strtoupper($day); ?></div>
                <div class="cal-body">
                    <?php if (empty($classes)): ?>
                        <div style="text-align: center; color: var(--text-muted); font-size: 0.85rem; margin-top: 20px;">No Classes</div>
                    <?php else: ?>
                        <?php foreach ($classes as $c): ?>
                            <div class="class-block">
                                <div class="class-title"><?php echo htmlspecialchars($c['subject_name']); ?></div>
                                <div class="class-time">
                                    🕒 <?php echo date('h:i A', strtotime($c['start_time'])) . ' - ' . date('h:i A', strtotime($c['end_time'])); ?>
                                </div>
                                <div class="class-sec">Sec <?php echo htmlspecialchars($c['section']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
