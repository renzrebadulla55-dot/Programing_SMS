<?php
require_once '../includes/session.php';
require_once '../config/database.php';

// Get current filters (matches your search logic)
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

$query = "SELECT a.attendance_date, s.student_no, s.first_name, s.last_name, s.section, a.status 
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

$stmt = $conn->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// File headers for Excel download
$filename = "Attendance_Report_" . date('Y-m-d') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");

// Create the Excel Table structure
echo "Date\tStudent ID\tFirst Name\tLast Name\tSection\tStatus\n";
foreach ($data as $row) {
    echo implode("\t", array_values($row)) . "\n";
}
exit();
?>