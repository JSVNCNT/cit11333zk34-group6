<?php
require 'auth.php';
require '../db.php';

$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $name = $_POST['name'] ?? '';
    $units = (int)($_POST['units'] ?? 0);
    
    if ($code && $name && $units > 0) {
        $insert = "
            INSERT INTO subjects (subject_code, subject_name, units)
            VALUES ('$code', '$name', '$units')
        ";

        mysqli_query($conn, $insert);

        $_SESSION['flash'] = 'Subject added successfully!';
        header('Location: subjects.php');
        exit;
    }
}

if (isset($_SESSION['flash'])) {
    $success_message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$subjects_query = "SELECT * FROM subjects ORDER BY id ASC";
$subjects_result = mysqli_query($conn, $subjects_query);
$subjects = [];

while ($row = mysqli_fetch_assoc($subjects_result)) {
    $subjects[] = $row;
}

$total_subjects = count($subjects);
$total_units    = array_sum(array_column($subjects, 'units')); 


// ----------------------------------------------------------
// PAGE TITLES
// ----------------------------------------------------------
$active_page = 'subjects';
$page_title  = 'Subjects';
$page_icon   = '<i class="bi bi-journal-text"></i>';

include 'header.php';
?>
<main class="content">
    <?php if ($success_message): ?>
        <div class="alert-success">✅ <?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Subjects</div>
            <div class="stat-value blue"><?= $total_subjects ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Units</div>
            <div class="stat-value green"><?= $total_units ?></div>
        </div>
    </div>

    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-title">Add New Subject</div>
        </div>
        <div class="form-body">
            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="code">Subject Code</label>
                        <input type="text" id="code" name="code" placeholder="e.g. MATH102" required maxlength="10">
                    </div>
                    <div class="form-group">
                        <label for="name">Subject Name</label>
                        <input type="text" id="name" name="name" placeholder="e.g. Statistics and Probability" required>
                    </div>
                    <div class="form-group">
                        <label for="units">Units</label>
                        <select id="units" name="units" required>
                            <option value="">— Select —</option>
                            <option value="1">1 unit</option>
                            <option value="2">2 units</option>
                            <option value="3">3 units</option>
                            <option value="4">4 units</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-submit"><i class="bi bi-plus-square"></i> Add Subject</button>
            </form>
        </div>
    </div>

    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Enrolled Subjects</div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Subject Name</th>
                    <th>Units</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_subjects === 0): ?>
                <tr>
                    <td colspan="4" style="text-align:center; padding:24px; color:var(--text-muted);">
                        No subjects yet. Use the form above to add one.
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach ($subjects as $i => $subject): ?>
                <tr>
                    <td class="id-cell"><?= $i + 1 ?></td>
                    <td class="code-cell"><?= htmlspecialchars($subject['subject_code']) ?></td>
                    <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                    <td class="id-cell"><?= $subject['units'] ?> units</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include 'footer.php'; ?>


