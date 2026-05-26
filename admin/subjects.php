<?php

require 'auth.php';
require '../db.php';

if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];

    mysqli_query($conn, "DELETE FROM subjects WHERE id='$delete_id'");

    header("Location: subjects.php?success=deleted");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code = $_POST['code'];
    $name = $_POST['name'];
    $units = $_POST['units'];

    if (isset($_POST['edit_id'])) {

        $edit_id = $_POST['edit_id'];

        $update = "
        UPDATE subjects
        SET
            subject_code='$code',
            subject_name='$name',
            units='$units'
        WHERE id='$edit_id'
        ";

        mysqli_query($conn, $update);

        header("Location: subjects.php?success=updated");
        exit;

    } else {

        $insert = "
        INSERT INTO subjects
        (subject_code, subject_name, units)

        VALUES

        ('$code', '$name', '$units')
        ";

        mysqli_query($conn, $insert);

        header("Location: subjects.php?success=added");
        exit;
    }
}

$limit = 5;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$offset = ($page - 1) * $limit;

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM subjects");

$total_row = mysqli_fetch_assoc($total_query);

$total_subjects = $total_row['total'];

$total_pages = ceil($total_subjects / $limit);

$query = "
SELECT *
FROM subjects
ORDER BY id ASC
LIMIT $offset, $limit
";

$result = mysqli_query($conn, $query);

$subjects = [];

while($row = mysqli_fetch_assoc($result)) {
    $subjects[] = $row;
}

$active_page = 'subjects';
$page_title = 'Subjects';
$page_icon = '<i class="bi bi-journal-text"></i>';

include 'header.php';

?>

<?php if(isset($_GET['success'])): ?>

<div class="alert-success">

<?php

if($_GET['success'] === 'added') {
    echo "✅ Subject added successfully!";
}

if($_GET['success'] === 'updated') {
    echo "✅ Subject updated successfully!";
}

if($_GET['success'] === 'deleted') {
    echo "✅ Subject deleted successfully!";
}

?>

</div>

<?php endif; ?>

<div class="form-card">

<div class="form-card-header">

<div class="form-card-title">
Add Subject
</div>

</div>

<div class="form-body">

<form method="POST">

<div class="form-grid">

<div class="form-group">

<label>Subject Code</label>

<input type="text" name="code" required>

</div>

<div class="form-group">

<label>Subject Name</label>

<input type="text" name="name" required>

</div>

<div class="form-group">

<label>Units</label>

<select name="units" required>

<option value="">Select</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>

</select>

</div>

</div>

<button type="submit" class="btn btn-primary">
<i class="bi bi-plus-square"></i>
Add Subject
</button>

</form>

</div>

</div>

<div class="table-card">

<div class="table-card-header">

<div class="table-card-title">
Subject Records
</div>

</div>

<table class="data-table">

<thead>

<tr>
<th>ID</th>
<th>Code</th>
<th>Subject Name</th>
<th>Units</th>
<th>Actions</th>
</tr>

</thead>

<tbody>

<?php foreach($subjects as $subject): ?>

<tr>

<td><?= $subject['id'] ?></td>

<td class="code-cell">
<?= htmlspecialchars($subject['subject_code']) ?>
</td>

<td>
<?= htmlspecialchars($subject['subject_name']) ?>
</td>

<td>
<?= $subject['units'] ?>
</td>

<td>

<button
class="btn btn-warning btn-sm"
data-bs-toggle="modal"
data-bs-target="#editModal"
onclick="openEditModal(
'<?= $subject['id'] ?>',
'<?= htmlspecialchars($subject['subject_code']) ?>',
'<?= htmlspecialchars($subject['subject_name']) ?>',
'<?= $subject['units'] ?>'
)">
Edit
</button>

<a
href="subjects.php?delete=<?= $subject['id'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this subject?')"
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
href="subjects.php?page=<?= $i ?>"
class="btn btn-primary <?= $page == $i ? '' : 'opacity-50' ?>"
>
<?= $i ?>
</a>

<?php endfor; ?>

</div>

<div class="modal fade" id="editModal" tabindex="-1">

<div class="modal-dialog">

<div class="modal-content bg-dark text-light">

<div class="modal-header border-secondary">

<h5 class="modal-title">
Edit Subject
</h5>

<button
type="button"
class="btn-close btn-close-white"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<form method="POST">

<input type="hidden" name="edit_id" id="edit_id">

<div class="mb-3">

<label class="form-label">
Subject Code
</label>

<input
type="text"
class="form-control"
name="code"
id="edit_code"
required>

</div>

<div class="mb-3">

<label class="form-label">
Subject Name
</label>

<input
type="text"
class="form-control"
name="name"
id="edit_name"
required>

</div>

<div class="mb-3">

<label class="form-label">
Units
</label>

<select
class="form-select"
name="units"
id="edit_units"
required>

<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>

</select>

</div>

<button
type="submit"
class="btn btn-primary">
Update Subject
</button>

</form>

</div>

</div>

</div>

</div>

<script>

function openEditModal(id, code, name, units) {

document.getElementById('edit_id').value = id;
document.getElementById('edit_code').value = code;
document.getElementById('edit_name').value = name;
document.getElementById('edit_units').value = units;

}

</script>

<?php include 'footer.php'; ?>