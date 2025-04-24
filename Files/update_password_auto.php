<?php
// Auto-update password for pradeep123@gmail.com to allow login with 'pradeep12345'
$con = mysqli_connect('localhost','root','','myhmsdb');
if (!$con) {
    die('Could not connect: ' . mysqli_connect_error());
}
// Ensure the password column is correct length
mysqli_query($con, "ALTER TABLE patreg MODIFY password VARCHAR(255) NOT NULL;");
// Set the correct hash (use single quotes to avoid PHP variable parsing)
$sql = 'UPDATE patreg SET password = \'$2y$12$DZd.Y0nxvYW8S0jFO0WWaeHjwxxC6abfRJrij6g5WfutfrFt1MY1u\' WHERE email = \'pradeep123@gmail.com\'';
if (mysqli_query($con, $sql)) {
    echo 'Password updated successfully.';
} else {
    echo 'Error updating password: ' . mysqli_error($con);
}
mysqli_close($con);
?>
