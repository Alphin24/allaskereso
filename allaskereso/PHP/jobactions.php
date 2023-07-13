<?php
require_once 'connect.php';
global $conn;

if (array_key_exists("torol", $_POST)) {
    $query = "DELETE FROM ALLASHIRDETES WHERE A_ID ='" . $_POST["torol"] . "'";
    $stmt = oci_parse($conn, $query);
    unset($_POST['torol']);
    oci_execute($stmt);
    oci_commit($conn);
    oci_free_statement($stmt);
    header("Location: profile.php");
}
if (array_key_exists("modosit", $_POST)) {
    $a_id = $_POST["modosit"];
    $szalagcim = $_POST["szalagcim"];
    $leiras = $_POST["leiras"];
    $feltetel = $_POST["feltetel"];
    $fizetes = $_POST["fizetes"];
    $m_helye = $_POST["m_helye"];

    $stmt = oci_parse($conn, 'BEGIN update_job(:a_id, :szalagcim, :leiras, :feltetel, :fizetes, :m_helye); END;');
    oci_bind_by_name($stmt, ':a_id', $a_id);
    oci_bind_by_name($stmt, ':szalagcim', $szalagcim);
    oci_bind_by_name($stmt, ':leiras', $leiras);
    oci_bind_by_name($stmt, ':feltetel', $feltetel);
    oci_bind_by_name($stmt, ':fizetes', $fizetes);
    oci_bind_by_name($stmt, ':m_helye', $m_helye);
    oci_execute($stmt);
    oci_commit($conn);
    oci_free_statement($stmt);
    header("Location: profile.php");


}
?>