<?php
require 'auth.php';

if (!isset($_SESSION['subjects'])) {
    $_SESSION['subjects'] = [
        ['code' => 'CIT11333Z', 'name' => 'Web Development', 'teacher' => 'Prof. Reyes', 'units' => 3, 'schedule' => 'MWF 10:00-11:00'],
        ['code' => 'MATH101', 'name' => 'Calculus I', 'teacher' => 'Dr. Santos', 'units' => 4, 'schedule' => 'TTH 8:00-9:30'],
        ['code' => 'ENG102', 'name' => 'Technical Writing', 'teacher' => 'Ms. Cruz', 'units' => 3, 'schedule' => 'MWF 1:00-2:00'],
        ['code' => 'CS201', 'name' => 'Data Structures', 'teacher' => 'Engr. Lee', 'units' => 3, 'schedule' => 'TTH 10:00-11:30'],
        ['code' => 'PHYS101', 'name' => 'Physics I', 'teacher' => 'Dr. Garcia', 'units' => 4, 'schedule' => 'MWF 2:00-3:30']
    ];
}

$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $name = $_POST['name'] ?? '';
    $teacher = $_POST['teacher'] ?? '';
    $units = (int)($_POST['units'] ?? 0);
    $schedule = $_POST['schedule'] ?? '';
    
    if ($code && $name && $teacher && $units > 0 && $schedule) {
        $_SESSION['subjects'][] = [
            'code' => $code,
            'name' => $name,
            'teacher' => $teacher,
            'units' => $units,
            'schedule' => $schedule
        ];
        
        $_SESSION['flash'] = 'Subject added successfully!';
        header('Location: subjects.php');
        exit;
    }
}

if (isset($_SESSION['flash'])) {
    $success_message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$subjects       = $_SESSION['subjects'];
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
                        <label for="teacher">Teacher</label>
                        <input type="text" id="teacher" name="teacher" placeholder="e.g. Ms. Cruz" required>
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
                    <div class="form-group">
                        <label for="schedule">Schedule</label>
                        <input type="text" id="schedule" name="schedule" placeholder="e.g. MWF 7:30–8:30" required>
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
                    <th>Teacher</th>
                    <th>Units</th>
                    <th>Schedule</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_subjects === 0): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding:24px; color:var(--text-muted);">
                        No subjects yet. Use the form above to add one.
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach ($subjects as $i => $subject): ?>
                <tr>
                    <td class="id-cell"><?= $i + 1 ?></td>
                    <td class="code-cell"><?= htmlspecialchars($subject['code']) ?></td>
                    <td><?= htmlspecialchars($subject['name']) ?></td>
                    <td><?= htmlspecialchars($subject['teacher']) ?></td>
                    <td class="id-cell"><?= $subject['units'] ?> units</td>
                    <td class="schedule-tag"><?= htmlspecialchars($subject['schedule']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include 'footer.php'; ?>


