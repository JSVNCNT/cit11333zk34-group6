<?php

require 'auth.php';
require '../db.php';

$active_page = 'subjects';
$page_title = 'Subjects';
$page_icon = '<i class="bi bi-journal-text"></i>';

include 'header.php';

$query = "SELECT * FROM subjects";
$result = mysqli_query($conn, $query);

?>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Subject List</div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Units</th>
            </tr>
        </thead>

        <tbody>

        <?php while($subject = mysqli_fetch_assoc($result)): ?>

            <tr>
                <td><?= $subject['id'] ?></td>
                <td class="code-cell"><?= htmlspecialchars($subject['subject_code']) ?></td>
                <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                <td><?= $subject['units'] ?></td>
            </tr>

        <?php endwhile; ?>

        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>