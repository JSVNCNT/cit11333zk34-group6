<?php

require '../db.php';

header('Content-Type: application/json');

$query = "SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_name ASC";
$result = mysqli_query($conn, $query);

$subjects = [];
while($row = mysqli_fetch_assoc($result)) {
    $subjects[] = $row;
}

echo json_encode($subjects);

?>
