<?php
session_start();

require 'db.php';

if (isset($_SESSION['user'])) {
    header('Location: admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submitted_username = $_POST['username'] ?? '';
    $submitted_password = $_POST['password'] ?? '';

    $query = "SELECT * FROM users 
              WHERE username='$submitted_username' 
              AND password='$submitted_password'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

        $_SESSION['user'] = [
            'username' => $user['username'],
            'name' => $user['fullname'],
            'id' => $user['student_id']
        ];

        header('Location: admin/index.php');
        exit;

    } else {
        $error = 'Invalid username or password';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="src/style/global.css">
    <link rel="stylesheet" href="src/style/login-styles.css">
</head>
<body class="login-page">
    <div class="login-wrapper">

    <div class="login-brand">
        <div class="login-brand-icon"><i class="bi bi-tencent-qq"></i></div>
        <div class="login-brand-name">CIT11333Z</div>
    </div>

    <div class="login-card">
        <div class="login-card-header">
            <div class="login-card-title">Sign in to your account</div>
            <div class="login-card-sub">Enter your credentials to continue</div>
        </div>
        <div class="login-card-body">

            <?php if ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username"
                           placeholder="Enter your username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="Enter your password"
                           required>
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <div class="credentials-hint">
                <div class="credentials-hint-title"><i class="bi bi-shield-lock-fill"></i> Test Credentials</div>
                <div class="credential-row">
                    <span class="cred-label">Username</span>
                    <span class="cred-user"><?= htmlspecialchars('admin') ?></span>
                </div>
                <div class="credential-row">
                    <span class="cred-label">Password</span>
                    <span class="cred-pass"><?= htmlspecialchars('admin123') ?></span>
                </div>
            </div>

        </div>
    </div>
    <div class="login-footer">
        PHP Student Dashboard - Midterm Project &copy; <?= date('Y') ?> CIT11333Z
    </div>
</div>
</body>
</html>