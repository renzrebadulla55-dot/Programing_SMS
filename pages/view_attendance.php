<?php 
require_once '../includes/session.php'; 
require_once '../config/database.php';
include '../includes/header.php'; 

$role = $_SESSION['role'] ?? 'admin';
$user_id = $_SESSION['user_id'];

// Fetch subjects based on role
if ($role === 'professor') {
    $subs_stmt = $conn->prepare("SELECT * FROM subjects WHERE professor_id = ? ORDER BY subject_name ASC");
    $subs_stmt->execute([$user_id]);
} else {
    $subs_stmt = $conn->query("SELECT * FROM subjects ORDER BY subject_name ASC");
    $subs_stmt->execute();
}
$subjects = $subs_stmt->fetchAll(PDO::FETCH_ASSOC);

// Map allowed subjects for professor
$allowed_subject_ids = [];
$default_subject_id = '';
$allowed_sections = ['A', 'B', 'C', 'D'];

if ($role === 'professor') {
    foreach ($subjects as $s) $allowed_subject_ids[] = $s['id'];
    if (empty($allowed_subject_ids)) $allowed_subject_ids[] = 0; // Prevent empty IN clause
    
    // Get first subject of the week
    $first_sub_stmt = $conn->prepare("SELECT subject_name FROM schedules WHERE professor_id = ? ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), start_time ASC LIMIT 1");
    $first_sub_stmt->execute([$user_id]);
    $first_subject_name = $first_sub_stmt->fetchColumn();
    
    if ($first_subject_name) {
        $sub_id_stmt = $conn->prepare("SELECT id FROM subjects WHERE subject_name = ?");
        $sub_id_stmt->execute([$first_subject_name]);
        $default_subject_id = $sub_id_stmt->fetchColumn() ?: '';
    }

    // Get specific sections this professor handles
    $sec_stmt = $conn->prepare("SELECT DISTINCT section FROM schedules WHERE professor_id = ? ORDER BY section ASC");
    $sec_stmt->execute([$user_id]);
    $allowed_sections = $sec_stmt->fetchAll(PDO::FETCH_COLUMN);
    if(empty($allowed_sections)) $allowed_sections = ['A', 'B', 'C', 'D'];
}

$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
$filter_year = isset($_GET['year']) ? $_GET['year'] : '';
$filter_section = isset($_GET['section']) ? $_GET['section'] : '';
$filter_subject = isset($_GET['subject_id']) ? $_GET['subject_id'] : ($role === 'professor' ? $default_subject_id : '');

$query = "SELECT s.student_no, s.first_name, s.last_name, s.middle_initial, s.section, s.year_level, a.status, a.attendance_date, sub.subject_name 
          FROM attendance a 
          JOIN students s ON a.student_id = s.id 
          LEFT JOIN subjects sub ON a.subject_id = sub.id
          WHERE 1=1"; 

$params = [];

if (!empty($filter_date)) {
    $query .= " AND a.attendance_date = :date";
    $params[':date'] = $filter_date;
}
if (!empty($filter_year)) {
    $query .= " AND s.year_level = :year";
    $params[':year'] = $filter_year;
}
if (!empty($filter_section)) {
    $query .= " AND s.section = :section";
    $params[':section'] = $filter_section;
}
if (!empty($filter_subject)) {
    // Make sure professor can only filter their own
    if ($role === 'professor' && !in_array($filter_subject, $allowed_subject_ids)) {
        $query .= " AND a.subject_id = 0"; // Force no results
    } else {
        $query .= " AND a.subject_id = :subject_id";
        $params[':subject_id'] = $filter_subject;
    }
} else {
    // If no subject filter, restrict to allowed subjects for professor
    if ($role === 'professor') {
        $in_clause = implode(',', $allowed_subject_ids);
        $query .= " AND a.subject_id IN ($in_clause)";
    }
}

$query .= " ORDER BY a.attendance_date DESC, s.last_name ASC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_count = count($records);
$present_count = 0; $absent_count = 0; $late_excused = 0;

foreach ($records as $row) {
    if ($row['status'] == 'Present') $present_count++;
    elseif ($row['status'] == 'Absent') $absent_count++;
    else $late_excused++; 
}

$avg_attendance = ($total_count > 0) ? round(($present_count / $total_count) * 100, 1) : 0;
// Show average if subject or (year + section) is selected
$show_avg = (!empty($filter_subject) || (!empty($filter_year) && !empty($filter_section)));
?>

<div class="container">
    <div class="page-header">
        <h2 class="page-title">Attendance Reports</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Filter, review, and generate official attendance reports.</p>
    </div>

    <!-- Filters Card -->
    <div class="card" style="margin-bottom: 25px; background: #F8FAFC; border: 1px solid #E2E8F0;">
        <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 150px;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 5px; display: block;">Filter by Date</label>
                <input type="date" name="date" value="<?php echo $filter_date; ?>">
            </div>
            <div style="flex: 1.5; min-width: 200px;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 5px; display: block;">Subject</label>
                <select name="subject_id">
                    <?php if($role === 'admin'): ?><option value="">All Subjects</option><?php endif; ?>
                    <?php foreach($subjects as $sub): ?>
                        <option value="<?php echo $sub['id']; ?>" <?php if($filter_subject == $sub['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($sub['subject_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if ($role === 'admin'): ?>
            <div style="flex: 1; min-width: 150px;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 5px; display: block;">Year Level</label>
                <select name="year">
                    <option value="">All Years</option>
                    <option value="1" <?php if($filter_year=='1') echo 'selected'; ?>>1st Year</option>
                    <option value="2" <?php if($filter_year=='2') echo 'selected'; ?>>2nd Year</option>
                    <option value="3" <?php if($filter_year=='3') echo 'selected'; ?>>3rd Year</option>
                    <option value="4" <?php if($filter_year=='4') echo 'selected'; ?>>4th Year</option>
                </select>
            </div>
            <?php endif; ?>

            <div style="flex: 1; min-width: 150px;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 5px; display: block;">Section</label>
                <select name="section">
                    <option value="">All Sections</option>
                    <?php 
                    foreach($allowed_sections as $sec) {
                        $sel = ($filter_section == $sec) ? 'selected' : '';
                        echo "<option value='$sec' $sel>$sec</option>";
                    }
                    ?>
                </select>
            </div>
            <div style="flex: 0; display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="view_attendance.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Summary Statistics -->
    <div style="display: grid; grid-template-columns: repeat(<?php echo $show_avg ? '5' : '4'; ?>, 1fr); gap: 20px; margin-bottom: 25px;">
        <div class="card" style="background: var(--primary); color: white; padding: 20px;">
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.9;">TOTAL RECORDS</div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-top: 5px;"><?php echo $total_count; ?></div>
        </div>
        <div class="card" style="background: var(--success); color: white; padding: 20px;">
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.9;">PRESENT</div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-top: 5px;"><?php echo $present_count; ?></div>
        </div>
        <div class="card" style="background: var(--warning); color: white; padding: 20px;">
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.9;">LATE / EXCUSED</div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-top: 5px;"><?php echo $late_excused; ?></div>
        </div>
        <div class="card" style="background: var(--danger); color: white; padding: 20px;">
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.9;">ABSENT</div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-top: 5px;"><?php echo $absent_count; ?></div>
        </div>
        <?php if($show_avg): ?>
        <div class="card" style="background: #0EA5E9; color: white; padding: 20px;">
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.9;">CLASS AVERAGE</div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-top: 5px;"><?php echo $avg_attendance; ?>%</div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: var(--text-main);">Attendance Log</h3>
            
            <div style="display: flex; gap: 15px; align-items: center;">
                <input type="text" id="nameSearch" placeholder="Search by name..." style="width: 250px; padding: 8px 12px; font-size: 0.9rem;" onkeyup="filterTable()">
                <a href="../actions/export_excel.php?date=<?php echo urlencode($filter_date); ?>&subject_id=<?php echo urlencode($filter_subject); ?>&year=<?php echo urlencode($filter_year); ?>&section=<?php echo urlencode($filter_section); ?>" class="btn btn-secondary" style="background-color: #10B981; color: white; border: none;">Export Excel</a>
                <button onclick="openPdfPreview()" class="btn btn-primary" style="background-color: #EF4444; border: none;">Generate PDF</button>
            </div>
        </div>

        <table class="big-table" id="attendanceTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Subject</th>
                    <th>Year & Sec</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($records) > 0): ?>
                    <?php foreach($records as $r): ?>
                    <tr class="data-row">
                        <td style="font-weight: 500; color: var(--text-muted);"><?php echo date('M d, Y', strtotime($r['attendance_date'])); ?></td>
                        <td><strong style="color: var(--primary);"><?php echo $r['student_no']; ?></strong></td>
                        <td class="student-name" style="font-weight: 600; font-size: 1.05rem;">
                            <?php echo htmlspecialchars($r['last_name'] . ", " . $r['first_name'] . " " . (isset($r['middle_initial']) ? $r['middle_initial'] : '')); ?>
                        </td>
                        <td style="font-weight: 600; color: var(--text-main);">
                            <?php echo htmlspecialchars($r['subject_name'] ?? 'General'); ?>
                        </td>
                        <td style="font-weight: 600;">
                            <span style="background: #F3F4F6; padding: 4px 8px; border-radius: 6px; font-size: 0.9rem;">
                                <?php echo (isset($r['year_level']) ? $r['year_level'] : '1') . "-" . $r['section']; ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                                $status = $r['status'];
                                $color = 'var(--text-muted)'; 
                                if($status == 'Present') $color = 'var(--success)';
                                elseif($status == 'Absent') $color = 'var(--danger)';
                                elseif($status == 'Late') $color = 'var(--warning)';
                                
                                echo "<span style='color: $color; font-weight: 700; background: {$color}1A; padding: 6px 12px; border-radius: 20px; font-size: 0.9rem;'>$status</span>";
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px; font-size: 1.1rem;">No records found for those filters.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filterTable() {
    let input = document.getElementById("nameSearch").value.toLowerCase();
    let rows = document.querySelectorAll("#attendanceTable .data-row");

    rows.forEach(row => {
        let nameCell = row.querySelector(".student-name").textContent.toLowerCase();
        if (nameCell.includes(input)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>

<!-- PDF Generation Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<!-- PDF PREVIEW MODAL -->
<div id="pdfModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 10000; justify-content: center; align-items: center;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);" onclick="closePdfPreview()"></div>
    <div style="position: relative; background: #f1f5f9; padding: 25px; border-radius: 12px; width: 90%; max-width: 900px; height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.25); z-index: 10001; animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;">
        
        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 15px;">
            <h3 style="margin: 0; font-family: 'Sora', sans-serif; color: var(--text-main);">Report Preview</h3>
            <div style="display: flex; gap: 10px;">
                <button onclick="closePdfPreview()" class="btn btn-secondary">Cancel</button>
                <button onclick="downloadPdf()" class="btn btn-primary" style="background-color: #EF4444; border: none;">Save & Download PDF</button>
            </div>
        </div>

        <!-- Scrollable Preview Area -->
        <div style="flex: 1; overflow-y: auto; display: flex; justify-content: center; padding: 20px 0;">
            <!-- This is the actual container that gets converted to PDF -->
            <div id="pdfPrintArea" style="background: white; padding: 40px; width: 100%; max-width: 800px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); min-height: 1122px;">
                <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px;">
                    <h2 style="margin: 0; color: #000; font-family: Arial, sans-serif;">Official Attendance Report</h2>
                    <p style="margin: 5px 0 0; color: #555; font-size: 0.9rem;">Generated on: <?php echo date('F d, Y h:i A'); ?></p>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-family: Arial, sans-serif; font-size: 0.9rem;">
                    <div>
                        <strong>Subject Filter:</strong> <?php 
                            if(!empty($filter_subject)){
                                $fsub = $conn->query("SELECT subject_name FROM subjects WHERE id = $filter_subject")->fetchColumn();
                                echo htmlspecialchars($fsub);
                            } else { echo "All Subjects"; }
                        ?><br>
                        <strong>Date Filter:</strong> <?php echo !empty($filter_date) ? date('M d, Y', strtotime($filter_date)) : "All Dates"; ?>
                    </div>
                    <div style="text-align: right;">
                        <strong>Total Records:</strong> <?php echo $total_count; ?><br>
                        <strong>Class Average:</strong> <?php echo $avg_attendance; ?>%
                    </div>
                </div>

                <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 0.85rem;" id="pdfTableClone">
                    <!-- Cloned contents will go here -->
                </table>
                
                <div style="margin-top: 50px; text-align: right; font-family: Arial, sans-serif;">
                    <p style="margin-bottom: 40px;">Certified Correct by:</p>
                    <div style="border-bottom: 1px solid #000; width: 200px; display: inline-block;"></div>
                    <p style="margin-top: 5px; font-weight: bold;"><?php echo isset($_SESSION['username']) ? strtoupper($_SESSION['username']) : 'PROFESSOR'; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openPdfPreview() {
    // Clone the table headers and rows
    let sourceTable = document.getElementById("attendanceTable");
    let targetTable = document.getElementById("pdfTableClone");
    
    targetTable.innerHTML = ''; // Clear previous

    // Clone header and style it for PDF
    let thead = sourceTable.querySelector("thead").cloneNode(true);
    let ths = thead.querySelectorAll("th");
    ths.forEach(th => {
        th.style.borderBottom = "2px solid #000";
        th.style.padding = "10px 5px";
        th.style.textAlign = "left";
        th.style.color = "#000";
    });
    targetTable.appendChild(thead);

    // Clone visible rows
    let tbody = document.createElement("tbody");
    let rows = sourceTable.querySelectorAll("tbody tr");
    rows.forEach(row => {
        if (row.style.display !== "none") {
            let tr = row.cloneNode(true);
            let tds = tr.querySelectorAll("td");
            tds.forEach(td => {
                td.style.borderBottom = "1px solid #eee";
                td.style.padding = "8px 5px";
                td.style.color = "#000";
                
                // Clean up badges for PDF printing
                let span = td.querySelector("span");
                if(span) {
                    span.style.background = "transparent";
                    span.style.padding = "0";
                }
            });
            tbody.appendChild(tr);
        }
    });
    targetTable.appendChild(tbody);

    // Show modal
    document.getElementById("pdfModal").style.display = "flex";
}

function closePdfPreview() {
    document.getElementById("pdfModal").style.display = "none";
}

function downloadPdf() {
    const element = document.getElementById('pdfPrintArea');
    const opt = {
        margin:       0.5,
        filename:     'Attendance_Report_<?php echo date("Ymd"); ?>.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    
    // Change button text briefly
    let btn = event.target;
    let oldText = btn.innerHTML;
    btn.innerHTML = "Generating...";
    
    html2pdf().set(opt).from(element).save().then(() => {
        btn.innerHTML = oldText;
        closePdfPreview();
    });
}
</script>

<?php include '../includes/footer.php'; ?>