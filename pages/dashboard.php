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

// Graph data: last 7 days — get all dates first
$dates_stmt = $conn->query("SELECT DISTINCT attendance_date FROM attendance ORDER BY attendance_date DESC LIMIT 7");
$dates = array_reverse($dates_stmt->fetchAll(PDO::FETCH_COLUMN));

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
    ORDER BY a.attendance_date DESC, s.last_name ASC LIMIT 5");
$recent = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

$avatar_colors = ['#5046e5','#0ea5e9','#10b981','#0ea5e9','#ef4444','#8b5cf6'];
?>

<div class="container">

    <!-- Page Header -->
    <div class="dash-header">
        <div class="dash-header-left">
        </div>
    </div>

<!-- Stat Cards -->
<div class="stat-grid">
    <?php
    $cards = [
        [
            'icon'  => '',
            'label' => 'Total Students',
            'value' => $count_students,
            'sub'   => 'Enrolled this term',
            'color' => '#6366f1',
            'class' => 'c-primary',
            'delay' => 0,
        ],
        [
            'icon'  => '',
            'label' => 'Present Today',
            'value' => $present_today,
            'sub'   => ($count_students > 0 ? round(($present_today / $count_students) * 100) : 0) . '% attendance rate',
            'color' => '#22c55e',
            'class' => 'c-success',
            'delay' => 100,
        ],
        [
            'icon'  => '',
            'label' => 'Late Today',
            'value' => $late_today,
            'sub'   => 'Tardiness records',
            'color' => '#f59e0b',
            'class' => 'c-warning',
            'delay' => 200,
        ],
        [
            'icon'  => '',
            'label' => 'Absent Today',
            'value' => $absent_today,
            'sub'   => 'Missing students',
            'color' => '#ef4444',
            'class' => 'c-danger',
            'delay' => 300,
        ],
    ];

    foreach ($cards as $card): ?>
        <div
            class="stat-card <?php echo $card['class']; ?> stat-card-animated"
            data-value="<?php echo (int) $card['value']; ?>"
            data-color="<?php echo htmlspecialchars($card['color']); ?>"
            data-delay="<?php echo (int) $card['delay']; ?>"
            style="opacity:0; transform: translateY(24px);"
        >
            <!-- Background circle decoration -->
            <div class="stat-bg-circle" style="background: <?php echo htmlspecialchars($card['color']); ?>;"></div>

            <div class="stat-inner">
                <div class="stat-icon"><?php echo $card['icon']; ?></div>
                <div class="stat-label"><?php echo htmlspecialchars($card['label']); ?></div>
                <div class="stat-value" data-target="<?php echo (int) $card['value']; ?>">0</div>
                <div class="stat-sub"><?php echo htmlspecialchars($card['sub']); ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
.stat-card {
    position: relative;
    background: #fff;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #f3f4f6;
    border-top: none !important;
    cursor: default;
    overflow: hidden;
    transition: opacity 0.6s ease, transform 0.6s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

/* Background circle */
.stat-bg-circle {
    position: absolute;
    right: -2rem; bottom: -2rem;
    width: 8rem; height: 8rem;
    border-radius: 50%;
    opacity: 0.04;
    transition: transform 0.5s ease;
}

.stat-card:hover .stat-bg-circle {
    transform: scale(1.5);
}

.stat-inner {
    position: relative;
    z-index: 1;
}

.stat-icon {
    font-size: 1.875rem;
    margin-bottom: 0.75rem;
    display: inline-block;
    transition: transform 0.3s ease;
}

.stat-card:hover .stat-icon {
    transform: scale(1.15);
}

.stat-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 2.25rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.5rem;
    color: #1f2937;
    transition: color 0.3s ease;
}

.stat-sub {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Stat card animations ──
    document.querySelectorAll('.stat-card-animated').forEach(function (card) {
        const delay   = parseInt(card.dataset.delay) || 0;
        const color   = card.dataset.color;
        const target  = parseInt(card.dataset.value) || 0;
        const valueEl = card.querySelector('.stat-value');

        setTimeout(function () {
            card.style.opacity   = '1';
            card.style.transform = 'translateY(0)';

            const duration  = 800;
            const startTime = performance.now();

            function animate(currentTime) {
                const elapsed  = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const eased    = 1 - Math.pow(1 - progress, 3);
                valueEl.textContent = Math.round(eased * target);
                if (progress < 1) requestAnimationFrame(animate);
            }
            requestAnimationFrame(animate);
        }, delay);

        card.addEventListener('mouseenter', function () { valueEl.style.color = color; });
        card.addEventListener('mouseleave', function () { valueEl.style.color = '#1f2937'; });
    });

    // ── Quick Actions fade-in ──
    setTimeout(function () {
        const qa = document.getElementById('quickActionsCard');
        if (qa) qa.classList.add('visible');
    }, 700);

});
</script>

    <!-- Chart + Recent side by side -->
    <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:16px;margin-bottom:20px;">

        <!-- Attendance Chart -->
        <div class="chart-card">
            <div class="chart-card-header">
                <h3>Weekly Attendance</h3>
                <span class="chart-pill">Last 7 days</span>
            </div>
            <div style="height:220px;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Recent Records -->
        <div class="chart-card">
            <div class="chart-card-header">
                <h3>Recent Records</h3>
                <a href="view_attendance.php" style="font-size:12px;color:var(--primary);text-decoration:none;font-weight:600;">View all</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <?php if (count($recent) === 0): ?>
                    <p style="color:var(--text-muted);font-size:13px;">No records yet.</p>
                <?php endif; ?>
                <?php foreach ($recent as $i => $r):
                    $initials = strtoupper(substr($r['first_name'],0,1).substr($r['last_name'],0,1));
                    $color = $avatar_colors[$i % count($avatar_colors)];
                    $status_class = strtolower($r['status']);
                ?>
                <div style="display:flex;align-items:center;gap:10px;padding:8px;border-radius:8px;background:#f8f9fc;">
                    <div style="width:30px;height:30px;border-radius:50%;background:<?php echo $color;?>;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;flex-shrink:0;">
                        <?php echo $initials; ?>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;font-weight:600;color:var(--text-main);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?php echo $r['last_name'].', '.$r['first_name']; ?>
                        </div>
                        <div style="font-size:11px;color:var(--text-muted);"><?php echo date('M d', strtotime($r['attendance_date'])); ?></div>
                    </div>
                    <span class="status-badge status-<?php echo $status_class; ?>">
                        <?php echo $r['status']; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

<?php
$quick_actions = [
    [
        'href' => 'mark_attendance.php',
        'label' => "Mark Today's Attendance",
        'color' => '#3b82f6',
        'bg' => 'rgba(59,130,246,0.15)',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/></svg>',
    ],
    [
        'href' => 'add_student.php',
        'label' => 'Add New Student',
        'color' => '#10b981',
        'bg' => 'rgba(16,185,129,0.15)',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>',
    ],
    [
        'href' => 'view_attendance.php',
        'label' => 'View Attendance History',
        'color' => '#f59e0b',
        'bg' => 'rgba(245,158,11,0.15)',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>',
    ],
];
?>

<!-- Quick Actions -->
<div class="quick-actions-card" id="quickActionsCard">
    <h3>Quick Actions</h3>
    <p>Use the shortcuts below to manage your system.</p>
    <div class="actions-grid">
        <?php foreach ($quick_actions as $action): ?>
            <a href="<?php echo $action['href']; ?>" class="action-btn" style="--action-color: <?php echo $action['color']; ?>; --action-bg: <?php echo $action['bg']; ?>;">
                <div class="action-icon-wrap">
                    <?php echo $action['icon']; ?>
                </div>
                <?php echo htmlspecialchars($action['label']); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [
                {
                    label: 'Present',
                    data: <?php echo json_encode($present_counts); ?>,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.08)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#22c55e',
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Late',
                    data: <?php echo json_encode($late_counts); ?>,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.06)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#f59e0b',
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Absent',
                    data: <?php echo json_encode($absent_counts); ?>,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.06)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ef4444',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#9ca3af', font: { size: 11 } },
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    ticks: { color: '#9ca3af', font: { size: 11 } },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 16,
                        font: { size: 11, weight: '600' },
                        color: '#6b7280'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + ' students';
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>