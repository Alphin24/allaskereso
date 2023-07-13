<?php
require_once 'connect.php';
global $conn;
global $code_job;

$stid = oci_parse($conn, "SELECT ALLASHIRDETES.*, CEG.NEV FROM CEG,ALLASHIRDETES WHERE ALLASHIRDETES.C_EMAIL = CEG.C_EMAIL");
if (!$stid) {
    $e = oci_error($conn);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$r = oci_execute($stid);
if (!$r){
    $e = oci_error($stid);


    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
require_once 'jobactions.php';
?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Állás módosítás</title>
    <link rel="stylesheet" href="../CSS/global.css">
</head>
<?php include_once 'header.php'?>


<div>

        <table>
            <tr>
                <form action="jobactions.php" method="post">
                <?php
                if(isset($_POST["jobmod"])) {

                    $stmt = oci_parse($conn, "SELECT A_ID, SZALAGCIM, LEIRAS, FELTETEL, FIZETES, M_HELYE FROM ALLASHIRDETES WHERE A_ID ='" . $_POST["jobmod"] . "'");
                    oci_execute($stmt);
                    $record = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS);

                ?>
                    <th>*Szalagcim</th><td><textarea type="longText" name="szalagcim"><?php echo $record['SZALAGCIM']; ?></textarea></td></tr><tr>
                    <th>*Leírás</th><td><textarea type="longText" name="leiras"><?php echo $record['LEIRAS']; ?></textarea></td></tr><tr>
                    <th>*Feltetel</th><td><input type="text" value="<?php echo $record['FELTETEL']; ?>" name="feltetel"></td></tr><tr>
                    <th>*Fizetes</th><td><input type="text" value="<?php echo $record['FIZETES']; ?>" name="fizetes"></td></tr><tr>
                    <th>*Munkavégzés helye</th><td><input type="text" value="<?php echo $record['M_HELYE']; ?>" name="m_helye"></td></tr><tr>
                    <th>

                        <td><button type="submit" name="modosit" value="<?php echo trim($record['A_ID']); ?>" >Módosít</button>
                        <button type="submit" name="torol" value="<?php echo trim($record['A_ID']); ?>">Törlés</button></td>
                    </form>
                    </th>

                <?php
                    }
                ?>
            </tr></table>


</div>
