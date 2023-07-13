<?php
require_once 'connect.php';
global $conn;
global $avg;
$stid = oci_parse($conn, "SELECT CEG.*, TELEPULESEK.* FROM CEG,TELEPULESEK WHERE TELEPULESEK.TELEPULES = CEG.TELEPULES");
if (!$stid) {
    $e = oci_error($conn);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$r = oci_execute($stid);
if (!$r){
    $e = oci_error($stid);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

if(isset($_POST["firmdelete"])){
    $query = "DELETE FROM CEG WHERE C_EMAIL = '" . $_POST["firmdelete"] . "' ";
    $delete = oci_parse($conn, $query);

    oci_execute($delete);
    oci_commit($conn);
    unset($_POST["firmdelete"]);

    oci_free_statement($delete);

    header("Location: companies.php");
    exit();
}
$count_query = oci_parse($conn, "SELECT COUNT(C_EMAIL) AS CEGSZAM FROM CEG");
oci_execute($count_query);
$count_result = oci_fetch_array($count_query);
$count = $count_result['CEGSZAM'];


?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Cégek</title>
    <link rel="stylesheet" href="../CSS/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body >
<?php include_once 'header.php' ?>
<?php if(!empty($_SESSION["admin"])){ ?>
    <style>
        body{
            background-image: url("../RES/admin.jpg");
            background-size: cover;
            background-repeat: no-repeat;
        }
        .table-name{
            background-color: rgba(128, 128, 128, 0.8);
        }

    </style>

<div class="table-container">
    <div class="table-name">
        <h2>Cégek listája</h2>
        <p>Jelenlegi cégek száma: <?php echo $count ?> </p>
    </div>
    <table>
        <tr>
            <th> Cégek Nevei </th>
            <th colspan="2"> Kapcsolat felvételi lehetőségek </th>
            <th> Telephely </th>
            <th> Átlag fizetés </th>
            <th> Művelet </th>

        </tr>
        <?php
        $stid2 = oci_parse($conn, "SELECT CEG.NEV AS cegnev, AVG(FIZETES) AS fizetesatlag
FROM CEG, ALLASHIRDETES
WHERE ALLASHIRDETES.C_EMAIL = CEG.C_EMAIL
GROUP BY CEG.NEV");
        $r = oci_execute($stid2);

        while(($record1 = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) !== false && ($record2 = oci_fetch_array($stid2, OCI_ASSOC+OCI_RETURN_NULLS)) !== false){
            $record = array_merge($record1, $record2);
        ?>
        <tr>
            <td> <?php echo $record['NEV'] ?> </td>
            <td> <?php echo $record['C_EMAIL'] ?> </td>
            <td> <?php echo $record['TELEFONSZAM'] ?> </td>
            <td> <?php echo $record['MEGYE'] . ', ' . $record['TELEPULES'] . ', ' . $record['UTCA'] . ' ' . $record['HAZSZAM'] . '.' ?> </td>
            <td> <?php echo $formatted_num = number_format($record['FIZETESATLAG'], 0, '.', ''); ?> Ft</td>
            <?php
            if(!empty($_SESSION["admin"])){ ?>
                <form action="companies.php" method="post">
                    <td> <button class="norm_button" type="submit" name="firmdelete" value="<?php echo trim($record['C_EMAIL']); ?>">Cég törlése</button> </td>
                </form>
            <?php  }
            }
            ?>
        </tr>
    </table>
</div>
    <?php
} ?>
<?php include_once 'footer.php'?>
