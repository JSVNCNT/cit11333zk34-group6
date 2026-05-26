<?php

require 'auth.php';
require '../db.php';

if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];

    mysqli_query($conn, "DELETE FROM grades WHERE id='$delete_id'");

    header("Location: grades.php?success=deleted");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject_id = $_POST['subject_id'];
    $prelim = $_POST['prelim'];
    $midterm = $_POST['midterm'];
    $final_exam = $_POST['final_exam'];

    $final_grade = round(($prelim + $midterm + $final_exam) / 3);

    if (isset($_POST['edit_id'])) {

        $edit_id = $_POST['edit_id'];

        $update = "
        UPDATE grades
        SET
            subject_id='$subject_id',
            prelim='$prelim',
            midterm='$midterm',
            final_exam='$final_exam',
            final_grade='$final_grade'
        WHERE id='$edit_id'
        ";

        mysqli_query($conn, $update);

        header("Location: grades.php?success=updated");
        exit;

    } else {

        $insert = "
        INSERT INTO grades
        (
            subject_id,
            prelim,
            midterm,
            final_exam,
            final_grade
        )

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

        header("Location: grades.php?success=added");
        exit;
    }
}

$limit = 5;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$offset = ($page - 1) * $limit;

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM grades");

$total_row = mysqli_fetch_assoc($total_query);

$total_records = $total_row['total'];

$total_pages = ceil($total_records / $limit);

$query = "
SELECT grades.*, subjects.subject_name
FROM grades
JOIN subjects
ON grades.subject_id = subjects.id
ORDER BY grades.id ASC
LIMIT $offset, $limit
";

$result = mysqli_query($conn, $query);

$grades = [];

while($row = mysqli_fetch_assoc($result)) {
    $grades[] = $row;
}

$active_page = 'grades';
$page_title = 'Grades';
$page_icon = '<i class="bi bi-trophy-fill"></i>';

include 'header.php';

?>

<div class="table-card">

<div class="table-card-header">

<div class="table-card-title">
Grade Records
</div>

</div>

<table class="data-table">

<thead>

<tr>
<th>ID</th>
<th>Subject</th>
<th>Prelim</th>
<th>Midterm</th>
<th>Final Exam</th>
<th>Final Grade</th>
<th>Actions</th>
</tr>

</thead>

<tbody>

<?php foreach($grades as $g): ?>

<tr>

<td><?= $g['id'] ?></td>

<td><?= htmlspecialchars($g['subject_name']) ?></td>

<td><?= $g['prelim'] ?></td>

<td><?= $g['midterm'] ?></td>

<td><?= $g['final_exam'] ?></td>

<td><?= $g['final_grade'] ?></td>

<td>

<button
class="btn btn-warning btn-sm"
data-bs-toggle="modal"
data-bs-target="#editModal"
onclick="openEditModal(
'<?= $g['id'] ?>',
'<?= $g['subject_id'] ?>',
'<?= $g['prelim'] ?>',
'<?= $g['midterm'] ?>',
'<?= $g['final_exam'] ?>'
)">
Edit
</button>

<a
href="grades.php?delete=<?= $g['id'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this grade?')"
>
Delete
</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<div class="mt-4 d-flex gap-2">

<?php for($i = 1; $i <= $total_pages; $i++): ?>

<a
href="grades.php?page=<?= $i ?>"
class="btn btn-primary <?= $page == $i ? '' : 'opacity-50' ?>"
>
<?= $i ?>
</a>

<?php endfor; ?>

</div>

<?php include 'footer.php'; ?>