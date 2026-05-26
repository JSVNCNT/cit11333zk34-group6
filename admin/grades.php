<?php

require 'auth.php';
require '../db.php';

$active_page = 'grades';
$page_title  = 'My Grades';
$page_icon   = '<i class="bi bi-trophy-fill"></i>';

include 'header.php';

$query = "
SELECT grades.*, subjects.subject_name
FROM grades
JOIN subjects
ON grades.subject_id = subjects.id
";

$result = mysqli_query($conn, $query);

$grades = [];
$total_grade = 0;
$highest = 0;
$lowest = 100;

while ($row = mysqli_fetch_assoc($result)) {

    $grades[] = $row;

    $total_grade += $row['final_grade'];

    if ($row['final_grade'] > $highest) {
        $highest = $row['final_grade'];
    }

    if ($row['final_grade'] < $lowest) {
        $lowest = $row['final_grade'];
    }
}

$count = count($grades);

$avg_grade = $count > 0 ? round($total_grade / $count, 1) : 0;

?>

<?php if (isset($_GET['success'])): ?>
<div class="alert-success">
    ✅ Grade record added successfully!
</div>
<?php endif; ?>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-label">Avg Grade</div>
        <div class="stat-value blue"><?= $avg_grade ?></div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Highest</div>
        <div class="stat-value green"><?= $highest ?></div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Lowest</div>
        <div class="stat-value red"><?= $lowest ?></div>
    </div>
</div>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject_id = $_POST['subject_id'];
    $prelim = $_POST['prelim'];
    $midterm = $_POST['midterm'];
    $final_exam = $_POST['final_exam'];

    $final_grade = round(($prelim + $midterm + $final_exam) / 3);

    $check_query = "SELECT id FROM grades WHERE subject_id = '$subject_id' LIMIT 1";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $update = "
        UPDATE grades
        SET prelim = '$prelim',
            midterm = '$midterm',
            final_exam = '$final_exam',
            final_grade = '$final_grade'
        WHERE subject_id = '$subject_id'
        ";

        mysqli_query($conn, $update);
    } else {
        $insert = "
        INSERT INTO grades
        (subject_id, prelim, midterm, final_exam, final_grade)

        VALUES

        (
            '$subject_id',
            '$prelim',
            '$midterm',
            '$final_exam',
            '$final_grade'
        )
        ";

        mysqli_query($conn, $insert);
    }

    header("Location: grades.php?success=1");
    exit;
}

?>

<div class="form-card">

    <div class="form-card-header">
        <div class="form-card-title">
            Add Grade Record
        </div>
    </div>

    <div class="form-body">

        <p class="form-hint">
            Final Grade is automatically computed.
        </p>

        <form method="POST">

            <div class="form-grid">

                <div class="form-group" style="grid-column: span 2;">

                    <label for="subject_id">
                        Subject
                    </label>

                    <select name="subject_id" id="subject_id" required>

                        <option value="">
                            Select Subject
                        </option>

                        <?php

                        $subjects_query = "SELECT * FROM subjects";
                        $subjects_result = mysqli_query($conn, $subjects_query);

                        while($subject = mysqli_fetch_assoc($subjects_result)):

                        ?>

                        <option value="<?= $subject['id'] ?>">
                            <?= htmlspecialchars($subject['subject_name']) ?>
                        </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label for="prelim">
                        Prelim Score
                    </label>

                    <input
                        type="number"
                        id="prelim"
                        name="prelim"
                        min="0"
                        max="100"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="midterm">
                        Midterm Score
                    </label>

                    <input
                        type="number"
                        id="midterm"
                        name="midterm"
                        min="0"
                        max="100"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="final_exam">
                        Final Exam Score
                    </label>

                    <input
                        type="number"
                        id="final_exam"
                        name="final_exam"
                        min="0"
                        max="100"
                        required
                    >

                </div>

            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-plus-square"></i>
                Add Grade Record
            </button>

        </form>

    </div>

</div>

<div class="table-card">

    <div class="table-card-header">

        <div class="table-card-title">
            Grade Report – 1st Semester
        </div>

        <div class="record-count">
            <?= $count ?> records
        </div>

    </div>

    <table class="data-table">

        <thead>

            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Prelim</th>
                <th>Midterm</th>
                <th>Final Exam</th>
                <th>Final Grade</th>
                <th>Remarks</th>
            </tr>

        </thead>

        <tbody>

        <?php foreach($grades as $g): ?>

            <tr>

                <td class="id-cell">
                    <?= $g['id'] ?>
                </td>

                <td>
                    <?= htmlspecialchars($g['subject_name']) ?>
                </td>

                <td class="id-cell">
                    <?= $g['prelim'] ?>
                </td>

                <td class="id-cell">
                    <?= $g['midterm'] ?>
                </td>

                <td class="id-cell">
                    <?= $g['final_exam'] ?>
                </td>

                <td>

                    <?php

                    $fg = $g['final_grade'];

                    $gc = $fg >= 90
                        ? 'grade-high'
                        : ($fg >= 85
                            ? 'grade-mid'
                            : 'grade-low');

                    ?>

                    <span class="<?= $gc ?>">
                        <?= $fg ?>
                    </span>

                </td>

                <td>

                    <?php if($fg >= 75): ?>

                        <span class="badge badge-active">
                            Passed
                        </span>

                    <?php else: ?>

                        <span class="badge badge-probation">
                            Failed
                        </span>

                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

<?php include 'footer.php'; ?>