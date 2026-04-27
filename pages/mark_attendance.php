<?php 
require_once '../config/database.php';
include '../includes/header.php'; 

// Fetch students from the database
$query = "SELECT * FROM students ORDER BY last_name ASC";
$stmt = $conn->query($query);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="padding: 20px;">
    <h2>Mark Attendance (<?php echo date('F d, Y'); ?>)</h2>
    <form action="../actions/save_attendance.php" method="POST">
        <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse;">
            <tr style="background: #f4f4f4;">
                <th>Student Name</th>
                <th>Status</th>
            </tr>
            <?php foreach ($students as $s): ?>
            <tr>
                <td><?php echo $s['last_name'] . ", " . $s['first_name']; ?></td>
                <td>
                    <input type="radio" name="attendance[<?php echo $s['id']; ?>]" value="Present" checked> P
                    <input type="radio" name="attendance[<?php echo $s['id']; ?>]" value="Absent"> A
                    <input type="radio" name="attendance[<?php echo $s['id']; ?>]" value="Late"> L
                    <input type="radio" name="attendance[<?php echo $s['id']; ?>]" value="Excused"> E
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer;">
            Submit Daily Attendance
        </button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>