<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';
include '../includes/header.php'; 

// Fetch current students
$query = "SELECT * FROM students ORDER BY id DESC";
$stmt = $conn->query($query);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-group label { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 5px; }
.form-group input { width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; }
.form-group input:focus { outline: none; border-color: var(--accent); }
.success-box { background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
.error-box { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); }
th { background-color: #F8FAFC; color: var(--primary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; }
.action-link { font-size: 0.85rem; text-decoration: none; padding: 4px 8px; border-radius: 4px; }
.action-link.edit { background: #e0f2fe; color: #0284c7; }
.action-link.delete { background: #fee2e2; color: #dc2626; margin-left: 5px; }
</style>

<div class="page-header">
    <h2 class="page-title">Enroll Student</h2>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 30px;">
    <div class="card">
        <h3 style="color: var(--primary); margin-bottom: 5px;">Add New Student</h3>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 20px;">Fill out the form below to enroll a student in the system.</p>
        
        <?php if(isset($_GET['success'])): ?>
            <div class="success-box">✔ Student added successfully!</div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="error-box">✖ Error: <?php echo htmlspecialchars($_GET['error']); ?></div>
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
            <div style="margin-top: 25px; display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Save Student Record</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <div class="card">
        <h3 style="color: var(--primary); margin-bottom: 10px;">Enrolled Students List</h3>
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
                    <td><strong><?php echo $s['student_no']; ?></strong></td>
                    <td><?php echo $s['last_name'] . ", " . $s['first_name']; ?></td>
                    <td><?php echo $s['section']; ?></td>
                    <td>
                        <a href="edit_student.php?id=<?php echo $s['id']; ?>" class="action-link edit">Edit</a>
                        <a href="../actions/delete_student.php?id=<?php echo $s['id']; ?>" 
                           onclick="return confirm('Are you sure? This will also delete their attendance records.');" 
                           class="action-link delete">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>