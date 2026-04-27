<?php 
require_once '../includes/session.php';
require_once '../config/database.php';
include '../includes/header.php'; 

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <h2>Edit Student Record</h2>
        <form action="../actions/update_student.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Student Number</label>
                    <input type="text" name="student_no" value="<?php echo $student['student_no']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Section</label>
                    <input type="text" name="section" value="<?php echo $student['section']; ?>" required>
                </div>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?php echo $student['first_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?php echo $student['last_name']; ?>" required>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Update Record</button>
                <a href="add_student.php" class="btn" style="background:rgb(245, 246, 243); color:rgb(247, 21, 21); margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>
</div>