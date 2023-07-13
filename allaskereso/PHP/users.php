<?php
require_once 'connect.php';
global $conn;
$stid = oci_parse($conn, "SELECT * FROM FELHASZNALOK WHERE SZEREPKOR != 'Admin'");
if (!$stid) {
    $e = oci_error($conn);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$r = oci_execute($stid);
if (!$r){
    $e = oci_error($stid);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

if(isset($_POST["userdelete"])){
    $query = "DELETE FROM FELHASZNALOK WHERE EMAIL = :email";
    $delete = oci_parse($conn, $query);
    oci_bind_by_name($delete, ':email', $_POST["userdelete"]);
    oci_execute($delete);
    oci_commit($conn);
    header("Location: users.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Felhasználók</title>
    <link rel="stylesheet" href="../CSS/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{
            background-image: url("../RES/admin.jpg");
            background-size: cover;
            background-repeat: no-repeat;
        }

        .topnav{
            background-color: rgba(114, 92, 92, 0.75);
        }
        p{
            background-color: rgba(114, 92, 92, 0.75);
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }
    </style>
</head>
<body >
<?php include_once 'header.php'?>

<div class="table-container">
    <div class="table-name">
        <p>Felhasználók listája</p>
    </div>
    <table>
        <tr>
            <th> Név </th>
            <th> Telefonszám </th>
            <th> Email </th>
            <th> Önéletrajz </th>
            <th> Művelet </th>
        </tr>
        <?php while($record = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){ ?>
            <tr>
                <td> <?php echo $record['NEV'] ?> </td>
                <td> <?php echo $record['TELEFONSZAM'] ?> </td>
                <td> <?php echo $record['EMAIL'] ?> </td>
                <td> <?php echo $record['ONELETRAJZ'] ?> </td>
                <?php if(!empty($_SESSION["admin"])){ ?>
                    <form action="users.php" method="post">
                        <td> <button class="norm_button" type="submit" name="userdelete" value="<?php echo trim($record['EMAIL']); ?>">Felhasználó törlése</button> </td>
                    </form>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
</div>

<?php include_once 'footer.php'?>
</body>
</html>
