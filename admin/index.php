<?php
require 'auth.php';

// ----------------------------------------------------------
// STUDENT DATA
// ----------------------------------------------------------
$student = [
    'name' => $logged_in_user['name'],
    'id' => $logged_in_user['id'],
    'status' => 'Active',
    'course' => 'Bachelor of Science in Information Technology',
    'year' => '3rd Year',
    'section' => 'A',
    'gpa' => '1.75',
    'email' => 'hiro.hamada@university.edu',
    'phone' => '+63 912 345 6789',
    'address' => '123 Tech Street, Digital City, 1234',
    'birth_date' => 'April 4, 2003',
    'age' => '21',
    'gender' => 'Male',
    'guardian_name' => 'Tadashi Hamada',
    'guardian_relation' => 'Brother',
    'guardian_phone' => '+63 912 345 6790',
    'guardian_email' => 'tadashi.hamada@tech.com',
    'guardian_address' => '123 Tech Street, Digital City, 1234'
];

//==================================================

// ----------------------------------------------------------
// PAGE TITLES
// ----------------------------------------------------------
$active_page = 'profile';
$page_title = 'Student Profile';
$page_icon = '<i class="bi bi-person-fill"></i>';

// Include header
include 'header.php'; 
?>
<main class="content">
    <div class="profile-header table-card" style="margin-bottom: 24px;">
        <div class="profile-banner">
            <img src="../src/assets/images/hiro-avatar.png" alt="Avatar">
        </div>
        <div class="profile-info-header">
            <div>
                <div class="profile-name"><?= htmlspecialchars($student['name']) ?></div>
                <div class="profile-id"><?= htmlspecialchars($student['id']) ?></div>
                <span class="badge badge-active"><?= htmlspecialchars($student['status']) ?></span>
            </div>
        </div>
    </div>
     <div class="table-card" style="margin-bottom: 24px;">
        <div class="table-card-header">
            <div class="table-card-title">Personal Information</div>
        </div>
        <?php
        $personal = [
            'Course' => $student['course'],
            'Year & Section' => $student['year'] . ' - ' . $student['section'],
            'GPA' => $student['gpa'],
            'Email' => $student['email'],
            'Phone' => $student['phone'],
            'Address' => $student['address'],
            'Birth Date' => $student['birth_date'],
            'Age' => $student['age'],
            'Gender' => $student['gender']
        ];

        foreach ($personal as $label => $value): ?>
        <div class="info-row">
            <div class="info-row-label"><?= htmlspecialchars($label) ?></div>
            <div class="info-row-value"><?= htmlspecialchars($value) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="table-card" styliiiiiiiiiiiiii7qqe="margin-bottom: 0;">
        <div class="table-card-header">
            <div class="table-card-title">Guardian Information</div>
        </div>
        <?php
        $guardian = [
            'Guardian Name' => $student['guardian_name'],
            'Relationship' => $student['guardian_relation'],
            'Phone' => $student['guardian_phone'],
            'Email' => $student['guardian_email'],
            'Address' => $student['guardian_address']
        ];

        foreach ($guardian as $label => $value): ?>
        <div class="info-row">
            <div class="info-row-label"><?= htmlspecialchars($label) ?></div>
            <div class="info-row-value"><?= htmlspecialchars($value) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</main>
<?php include 'footer.php'; ?>