<?php
require 'auth.php';   // blocks access if not logged in

if (!isset($_SESSION['grades'])) {
    $_SESSION['grades'] = [
        ['subject' => 'Web Development', 'prelim' => 88, 'midterm' => 92, 'final' => 95, 'grade' => 92],
        ['subject' => 'Calculus I', 'prelim' => 85, 'midterm' => 87, 'final' => 90, 'grade' => 87],
        ['subject' => 'Technical Writing', 'prelim' => 90, 'midterm' => 91, 'final' => 89, 'grade' => 90],
        ['subject' => 'Data Structures', 'prelim' => 78, 'midterm' => 82, 'final' => 85, 'grade' => 82],
        ['subject' => 'Physics I', 'prelim' => 92, 'midterm' => 94, 'final' => 93, 'grade' => 93]
    ];
}

$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $prelim = (int)($_POST['prelim'] ?? 0);
    $midterm = (int)($_POST['midterm'] ?? 0);
    $final = (int)($_POST['final'] ?? 0);
    
    if ($subject && $prelim >= 0 && $midterm >= 0 && $final >= 0 && $prelim <= 100 && $midterm <= 100 && $final <= 100) {
        $computed_grade = round(($prelim + $midterm + $final) / 3);
        
        $_SESSION['grades'][] = [
            'subject' => $subject,
            'prelim' => $prelim,
            'midterm' => $midterm,
            'final' => $final,
            'grade' => $computed_grade
        ];
        
        $_SESSION['flash'] = 'Grade record added successfully!';
        header('Location: grades.php');
        exit;
    }
}

if (isset($_SESSION['flash'])) {
    $success_message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$grades     = $_SESSION['grades'];
$count      = count($grades);
$all_grades = array_column($grades, 'grade');
$avg_grade  = $count > 0 ? round(array_sum($all_grades) / $count, 1) : 0;
$highest    = $count > 0 ? max($all_grades) : 0;
$lowest     = $count > 0 ? min($all_grades) : 0; 


// ----------------------------------------------------------
// PAGE TITLES
// ----------------------------------------------------------
$active_page = 'grades';
$page_title  = 'My Grades';
$page_icon   = '<i class="bi bi-trophy-fill"></i>';

include 'header.php';
?>

<?php if ($success_message): ?>
<div class="alert-success">✅ <?= htmlspecialchars($success_message) ?></div>
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

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-title">Add Grade Record</div>
    </div>
    <div class="form-body">
        <p class="form-hint">Final Grade is auto-computed: (Prelim + Midterm + Final Exam) ÷ 3</p>
        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group" style="grid-column: span 2;">
                    <label for="subject">Subject Name</label>
                    <input type="text" id="subject" name="subject" placeholder="e.g. Statistics and Probability" required>
                </div>
                <div class="form-group">
                    <label for="prelim">Prelim Score</label>
                    <input type="number" id="prelim" name="prelim" min="0" max="100" placeholder="0 – 100" required>
                </div>
                <div class="form-group">
                    <label for="midterm">Midterm Score</label>
                    <input type="number" id="midterm" name="midterm" min="0" max="100" placeholder="0 – 100" required>
                </div>
                <div class="form-group">
                    <label for="final">Final Exam Score</label>
                    <input type="number" id="final" name="final" min="0" max="100" placeholder="0 – 100" required>
                </div>
            </div>
            <button type="submit" class="btn-submit"><i class="bi bi-plus-square"></i> Add Grade Record</button>
        </form>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Grade Report – 1st Semester</div>
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
            <?php foreach ($grades as $i => $g): ?>
            <tr>
                <td class="id-cell"><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($g['subject']) ?></td>
                <td class="id-cell"><?= $g['prelim'] ?></td>
                <td class="id-cell"><?= $g['midterm'] ?></td>
                <td class="id-cell"><?= $g['final'] ?></td>
                <td>
                    <?php 
                    $fg = $g['grade'];
                    $gc = $fg >= 90 ? 'grade-high' : ($fg >= 85 ? 'grade-mid' : 'grade-low');
                    ?>
                    <span class="<?= $gc ?>"><?= $fg ?></span>
                </td>
                <td>
                    <?php 
                    $badge_class = $fg >= 75 ? 'badge-active' : 'badge-probation';
                    $remarks = $fg >= 75 ? 'Passed' : 'Failed';
                    ?>
                    <span class="badge <?= $badge_class ?>"><?= $remarks ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
