<?php
require_once '../config/database.php';

try {
    $conn->beginTransaction();

    // 1. Fetch all existing students
    $stmt = $conn->query("SELECT id FROM students ORDER BY id ASC");
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $total_existing = count($existing);
    $half = floor($total_existing / 2);

    // 2. Move half to A, half to D
    for ($i = 0; $i < $total_existing; $i++) {
        $sec = ($i < $half) ? 'A' : 'D';
        $conn->exec("UPDATE students SET section = '$sec' WHERE id = " . $existing[$i]);
    }

    // 3. Count how many are in A, B, C, D
    $counts = [
        'A' => $conn->query("SELECT COUNT(*) FROM students WHERE section = 'A'")->fetchColumn(),
        'B' => $conn->query("SELECT COUNT(*) FROM students WHERE section = 'B'")->fetchColumn(),
        'C' => $conn->query("SELECT COUNT(*) FROM students WHERE section = 'C'")->fetchColumn(),
        'D' => $conn->query("SELECT COUNT(*) FROM students WHERE section = 'D'")->fetchColumn()
    ];

    // Names pools
    $male_first = ['Juan', 'Jose', 'Pedro', 'Manuel', 'Antonio', 'Luis', 'Francisco', 'Carlos', 'Miguel', 'Roberto', 'Eduardo', 'Mario', 'Ricardo', 'Paulo', 'Ramon'];
    $female_first = ['Maria', 'Ana', 'Teresa', 'Carmen', 'Rosario', 'Luz', 'Elena', 'Rosa', 'Josefina', 'Teresita', 'Corazon', 'Lourdes', 'Lilia', 'Maricel', 'Jocelyn'];
    $last_names = ['Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres', 'Villanueva', 'Castillo', 'Flores', 'Ramos', 'Fernandez', 'Rivera', 'Gomez', 'Marcos', 'Aquino', 'Del Rosario', 'Navarro', 'Soriano'];

    // Get max student_no to increment
    $max_no_str = $conn->query("SELECT MAX(student_no) FROM students")->fetchColumn();
    $counter = 1000;
    if ($max_no_str) {
        $parts = explode('-', $max_no_str);
        if (isset($parts[1])) $counter = (int)$parts[1];
    }

    $insert_stmt = $conn->prepare("INSERT INTO students (student_no, first_name, last_name, section, gender, year_level, enrollment_status) VALUES (?, ?, ?, ?, ?, '3', 'Regular')");

    // 4. Fill each section to exactly 10
    foreach (['A', 'B', 'C', 'D'] as $sec) {
        $needed = 10 - $counts[$sec];
        for ($i = 0; $i < $needed; $i++) {
            $counter++;
            $student_no = "2023-" . str_pad($counter, 4, '0', STR_PAD_LEFT);
            
            $is_male = rand(0, 1) == 1;
            $gender = $is_male ? 'Male' : 'Female';
            $first_name = $is_male ? $male_first[array_rand($male_first)] : $female_first[array_rand($female_first)];
            $last_name = $last_names[array_rand($last_names)];

            $insert_stmt->execute([$student_no, $first_name, $last_name, $sec, $gender]);
        }
    }

    $conn->commit();
    echo "Database upgraded! Existing students moved to A and D. Filled A, B, C, D to 10 students each with Filipino names.";
} catch (PDOException $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
