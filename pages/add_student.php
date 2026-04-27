<?php 
require_once '../includes/session.php'; // The Gatekeeper
require_once '../config/database.php';
include '../includes/header.php'; 

// Fetch current students to display in the table below the form
$query = "SELECT * FROM students ORDER BY id DESC";
$stmt = $conn->query($query);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="card">
        <h2>Add New Student</h2>
        <p>Fill out the form below to enroll a student in the system.</p>
        <hr>

        <?php if(isset($_GET['success'])): ?>
            <div class="success-box">
                ✔ Student added successfully!
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="error-box">
                ✖ Error: Student Number already exists.
            </div>
        <?php endif; ?>

        <form action="../actions/save_student.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Student Number</label>
                    <input type="text" name="student_no" placeholder="e.g. 2024-0001" required>
                </div>

                <div class="form-group">
                    <label>Section/Grade</label>
                    <input type="text" name="section" placeholder="e.g. 3-L" required>
                </div>

                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" required>
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Student Record</button>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>

    <div class="card card-student-list">
        <h3>Enrolled Students List</h3>
        <table>
            <thead>
                <tr>
                    <th>ID No.</th>
                    <th>Name</th>
                    <th>Section</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $s): ?>
                <tr>
                    <td><?php echo $s['student_no']; ?></td>
                    <td><?php echo $s['last_name'] . ", " . $s['first_name']; ?></td>
                    <td><?php echo $s['section']; ?></td>
                    <td>
                        <a href="edit_student.php?id=<?php echo $s['id']; ?>" class="edit">Edit</a>
                        <a href="../actions/delete_student.php?id=<?php echo $s['id']; ?>" 
                           onclick="return confirm('Are you sure? This will also delete their attendance records.');" 
                           class="delete">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>