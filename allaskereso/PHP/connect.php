<?php
$user = "alphin";
$pass = "Alpha2379";
$host = "localhost/XE";

$conn = oci_connect($user, $pass, $host,"AL32UTF8");


if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
function function_alert($message) {
    echo "<script type='text/javascript'>alert('$message');</script>";
}
?>

