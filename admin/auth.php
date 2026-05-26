<?php
// ============================================================
//  auth.php  –  Session Guard
//
//  Include this at the VERY TOP of every admin page.
//  If the user is not logged in, they get sent back to login.
// ============================================================
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

// Make the logged-in user available to every page
$logged_in_user = $_SESSION['user'];