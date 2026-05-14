<?php
require_once '../includes/session.php';
require_once '../config/database.php';

$role = $_SESSION['role'] ?? 'admin';
$user_id = $_SESSION['user_id'];

// Get allowed subjects for professor
$allowed_subject_ids = [];
if ($role === 'professor') {
    $subs_stmt = $conn->prepare("SELECT id FROM subjects WHERE professor_id = ?");
    $subs_stmt->execute([$user_id]);
    $allowed_subject_ids = $subs_stmt->fetchAll(PDO::FETCH_COLUMN);
    if (empty($allowed_subject_ids)) $allowed_subject_ids[] = 0;
}

$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
$filter_subject = isset($_GET['subject_id']) ? $_GET['subject_id'] : '';
$filter_year = isset($_GET['year']) ? $_GET['year'] : '';
$filter_section = isset($_GET['section']) ? $_GET['section'] : '';

$query = "SELECT s.student_no, s.first_name, s.last_name, s.section, s.year_level, a.status, a.attendance_date, sub.subject_name 
          FROM attendance a 
          JOIN students s ON a.student_id = s.id 
          LEFT JOIN subjects sub ON a.subject_id = sub.id
          WHERE 1=1"; 

$params = [];

if (!empty($filter_date)) {
    $query .= " AND a.attendance_date = ?";
    $params[] = $filter_date;
}
if (!empty($filter_year)) {
    $query .= " AND s.year_level = ?";
    $params[] = $filter_year;
}
if (!empty($filter_section)) {
    $query .= " AND s.section = ?";
    $params[] = $filter_section;
}
if (!empty($filter_subject)) {
    if ($role === 'professor' && !in_array($filter_subject, $allowed_subject_ids)) {
        $query .= " AND a.subject_id = 0";
    } else {
        $query .= " AND a.subject_id = ?";
        $params[] = $filter_subject;
    }
} else if ($role === 'professor') {
    $in_clause = implode(',', $allowed_subject_ids);
    $query .= " AND a.subject_id IN ($in_clause)";
}

$query .= " ORDER BY a.attendance_date DESC, s.last_name ASC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Attendance_Report_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "Date\tStudent ID\tName\tSubject\tYear/Section\tStatus\n";
foreach($records as $r) {
    echo $r['attendance_date'] . "\t" . 
         $r['student_no'] . "\t" . 
         $r['last_name'] . ", " . $r['first_name'] . "\t" . 
         $r['subject_name'] . "\t" . 
         $r['year_level'] . "-" . $r['section'] . "\t" . 
         $r['status'] . "\n";
}
exit();
?>