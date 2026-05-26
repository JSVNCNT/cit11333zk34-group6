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