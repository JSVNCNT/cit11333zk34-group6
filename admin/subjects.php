<?php

require 'auth.php';
require '../db.php';

$success_message = '';

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

$subjects_query = "
SELECT *
FROM subjects
ORDER BY id ASC
LIMIT $offset, $limit
";

$subjects_result = mysqli_query($conn, $subjects_query);

$subjects = [];

while($row = mysqli_fetch_assoc($subjects_result)) {
    $subjects[] = $row;
}

$total_units = 0;

foreach($subjects as $subject) {
    $total_units += $subject['units'];
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

<div class="stats-row">

    <div class="stat-card">
        <div class="stat-label">Total Subjects</div>
        <div class="stat-value blue"><?= $total_subjects ?></div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Units This Page</div>
        <div class="stat-value green"><?= $total_units ?></div>
    </div>

</div>

<div class="form-card">

    <div class="form-card-header">
        <div class="form-card-title">
            Add New Subject
        </div>
    </div>

    <div class="form-body">

        <form method="POST">

            <div class="form-grid">

                <div class="form-group">

                    <label>Subject Code</label>

                    <input
                        type="text"
                        name="code"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Subject Name</label>

                    <input
                        type="text"
                        name="name"
                        required
                    >

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

            <button type="submit" class="btn-submit">
                <i class="bi bi-plus-square"></i>
                Add Subject
            </button>

        </form>

    </div>

</div>

<div class="table-card">

    <div class="table-card-header">

        <div class="table-card-title">
            Enrolled Subjects
        </div>

        <div class="record-count">
            <?= $total_subjects ?> records
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

                <td class="id-cell">
                    <?= $subject['id'] ?>
                </td>

                <td class="code-cell">
                    <?= htmlspecialchars($subject['subject_code']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($subject['subject_name']) ?>
                </td>

                <td class="id-cell">
                    <?= $subject['units'] ?>
                </td>

                <td>

                    <button
                        class="btn-submit"
                        style="padding:5px 10px;"
                        onclick="openEditModal(
                            '<?= $subject['id'] ?>',
                            '<?= htmlspecialchars($subject['subject_code']) ?>',
                            '<?= htmlspecialchars($subject['subject_name']) ?>',
                            '<?= $subject['units'] ?>'
                        )"
                    >
                        Edit
                    </button>

                    <a
                        href="subjects.php?delete=<?= $subject['id'] ?>"
                        onclick="return confirm('Delete this subject?')"
                        class="btn-submit"
                        style="
                            padding:5px 10px;
                            background:var(--accent4);
                            text-decoration:none;
                        "
                    >
                        Delete
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

<div style="margin-top:20px; display:flex; gap:10px;">

<?php for($i = 1; $i <= $total_pages; $i++): ?>

    <a
        href="subjects.php?page=<?= $i ?>"
        class="btn-submit"
        style="
            text-decoration:none;
            <?= $page == $i ? '' : 'opacity:.6;' ?>
        "
    >
        <?= $i ?>
    </a>

<?php endfor; ?>

</div>

<div
    id="editModal"
    style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(0,0,0,.6);
        z-index:999;
        align-items:center;
        justify-content:center;
    "
>

    <div
        style="
            background:var(--surface);
            padding:24px;
            border-radius:10px;
            width:400px;
        "
    >

        <h2 style="margin-bottom:20px;">
            Edit Subject
        </h2>

        <form method="POST">

            <input type="hidden" name="edit_id" id="edit_id">

            <div class="form-group">

                <label>Subject Code</label>

                <input
                    type="text"
                    name="code"
                    id="edit_code"
                    required
                >

            </div>

            <div class="form-group">

                <label>Subject Name</label>

                <input
                    type="text"
                    name="name"
                    id="edit_name"
                    required
                >

            </div>

            <div class="form-group">

                <label>Units</label>

                <select name="units" id="edit_units" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>

            </div>

            <div style="margin-top:20px; display:flex; gap:10px;">

                <button type="submit" class="btn-submit">
                    Update
                </button>

                <button
                    type="button"
                    class="btn-submit"
                    style="background:gray;"
                    onclick="closeModal()"
                >
                    Cancel
                </button>

            </div>

        </form>

    </div>

</div>

<script>

function openEditModal(id, code, name, units) {

    document.getElementById('edit_id').value = id;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_units').value = units;

    document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

</script>

<?php include 'footer.php'; ?>