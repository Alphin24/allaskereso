<?php
    require_once 'connect.php';
    global $conn;
    $stid = oci_parse($conn, "SELECT ERTEKELESEK.*, CEG.NEV FROM CEG,ERTEKELESEK WHERE ERTEKELESEK.C_EMAIL = CEG.C_EMAIL");

    $r = oci_execute($stid);

    if(isset($_POST["reviwedelete"])){
        $query = "DELETE FROM ERTEKELESEK WHERE EMAIL = :email";
        $delete = oci_parse($conn, $query);
        oci_bind_by_name($delete, ':email', $_POST["reviwedelete"]);
        oci_execute($delete);
        oci_commit($conn);
        header("Location: review.php");
        exit();
    }


?>



<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Munkák</title>
    <link rel="stylesheet" href="../CSS/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body >
<?php include_once 'header.php'?>
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

    </style> <?php
} ?>

<div class="table-container">
    <div class="table-name">
        <p>Értékelések listája</p>
    </div>
    <table>
        <tr>
            <th> Email </th>
            <th> Cég név </th>
            <th> Értékelés </th>
            <th> Tartalom </th>
            <th> Művelet </th>
        </tr>
        <?php
        while($record = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
        ?>
        <tr>
            <td> <?php echo $record['EMAIL'] ?> </td>
            <td> <?php echo $record['NEV'] ?> </td>
            <td> <?php echo $record['ERTEKELES'] ?> </td>
            <td> <?php echo $record['TARTALOM'] ?> </td>
            <form action="review.php" method="post">
                <td> <button id="del" type="submit" name="reviwedelete" value="<?php echo trim($record['EMAIL']); ?>">Értékelés törlése</button> </td>
            </form>
            <?php
            }
            ?>
        </tr>
    </table>
</div>
<?php include_once 'footer.php'?>


