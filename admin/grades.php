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

$search = $_GET['search'] ?? '';

$search_sql = '';

if ($search !== '') {
    $search_sql = " WHERE subjects.subject_name LIKE '%$search%' ";
}

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM grades JOIN subjects ON grades.subject_id = subjects.id $search_sql");

$total_row = mysqli_fetch_assoc($total_query);

$total_records = $total_row['total'];

$total_pages = ceil($total_records / $limit);

$query = "
SELECT grades.*, subjects.subject_name
FROM grades
JOIN subjects
ON grades.subject_id = subjects.id
$search_sql
ORDER BY grades.id ASC
LIMIT $offset, $limit
";

$result = mysqli_query($conn, $query);

$grades = [];

$total_grade = 0;
$highest = 0;
$lowest = 100;

while($row = mysqli_fetch_assoc($result)) {

    $grades[] = $row;

    $total_grade += $row['final_grade'];

    if($row['final_grade'] > $highest) {
        $highest = $row['final_grade'];
    }

    if($row['final_grade'] < $lowest) {
        $lowest = $row['final_grade'];
    }
}

$count = count($grades);

$avg_grade = $count > 0
? round($total_grade / $count, 1)
: 0;

// Get all subjects for edit modal dropdown
$subjects_query = mysqli_query($conn, "SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_name ASC");
$subjects = [];
while($row = mysqli_fetch_assoc($subjects_query)) {
    $subjects[] = $row;
}

$active_page = 'grades';
$page_title = 'My Grades';
$page_icon = '<i class="bi bi-trophy-fill"></i>';

include 'header.php';

?>

<?php if(isset($_GET['success'])): ?>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
    <div class="toast show align-items-center text-bg-success border-0">
        <div class="d-flex">
            <div class="toast-body">
                <?php
                if($_GET['success'] === 'added') {
                    echo "✅ Grade added successfully!";
                } elseif($_GET['success'] === 'updated') {
                    echo "✅ Grade updated successfully!";
                } elseif($_GET['success'] === 'deleted') {
                    echo "✅ Grade deleted successfully!";
                }
                ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
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

<form method="GET" class="mb-3 d-flex gap-2 flex-wrap">
    <input type="text" name="search" class="form-control" placeholder="Search subjects..." value="<?= htmlspecialchars($search) ?>" style="max-width:300px;">
    <button class="btn btn-primary">
        <i class="bi bi-search"></i>
    </button>
    <?php if($search): ?>
    <a href="grades.php" class="btn btn-secondary">
        Reset
    </a>
    <?php endif; ?>
</form>

<div class="table-card">

<div class="table-card-header">

<div class="table-card-title">
Grade Report – 1st Semester
</div>

<div class="record-count">
<?= $total_records ?> records
</div>

</div>

<div class="table-responsive">

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
<th>Actions</th>
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

<td><?= $g['prelim'] ?></td>

<td><?= $g['midterm'] ?></td>

<td><?= $g['final_exam'] ?></td>

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

<td>

<div class="d-flex gap-2 flex-wrap">

<button
class="btn btn-warning btn-sm"
data-bs-toggle="modal"
data-bs-target="#editGradeModal"
onclick="openEditGradeModal(
'<?= $g['id'] ?>',
'<?= $g['subject_id'] ?>',
'<?= $g['prelim'] ?>',
'<?= $g['midterm'] ?>',
'<?= $g['final_exam'] ?>'
)">

<i class="bi bi-pencil-square"></i>
Edit

</button>

<a
href="grades.php?delete=<?= $g['id'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this grade?')">

<i class="bi bi-trash"></i>
Delete

</a>

</div>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

<nav class="mt-4">
    <ul class="pagination">
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>

<!-- Edit Grade Modal -->
<div class="modal fade" id="editGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">
                    Edit Grade
                </h5>
                <button
                type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input
                    type="hidden"
                    name="edit_id"
                    id="edit_id">

                    <div class="mb-3">
                        <label class="form-label">
                            Subject
                        </label>
                        <select
                        class="form-select"
                        name="subject_id"
                        id="edit_subject_id"
                        required>
                            <option value="">— Select Subject —</option>
                            <?php foreach($subjects as $subject): ?>
                            <option value="<?= $subject['id'] ?>">
                                <?= htmlspecialchars($subject['subject_code']) ?> - <?= htmlspecialchars($subject['subject_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Prelim Grade
                        </label>
                        <input
                        type="number"
                        class="form-control"
                        name="prelim"
                        id="edit_prelim"
                        min="0"
                        max="100"
                        required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Midterm Grade
                        </label>
                        <input
                        type="number"
                        class="form-control"
                        name="midterm"
                        id="edit_midterm"
                        min="0"
                        max="100"
                        required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Final Exam Grade
                        </label>
                        <input
                        type="number"
                        class="form-control"
                        name="final_exam"
                        id="edit_final_exam"
                        min="0"
                        max="100"
                        required>
                    </div>

                    <button
                    type="submit"
                    class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i>
                        Update Grade
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openEditGradeModal(id, subjectId, prelim, midterm, finalExam) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_subject_id').value = subjectId;
    document.getElementById('edit_prelim').value = prelim;
    document.getElementById('edit_midterm').value = midterm;
    document.getElementById('edit_final_exam').value = finalExam;
}
</script>

<?php include 'footer.php'; ?>