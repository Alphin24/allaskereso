<?php   require_once 'connect.php';
global $conn;
require_once "actions.php";
global $user;

$GLOBALS["err"] = "";
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

if(isset($_SESSION["email"])) {
    if (isset($_POST['btn'])) {
        $error = false;
        $query_oneletrajz = "SELECT oneletrajz FROM FELHASZNALOK WHERE email='" . $_SESSION["email"] . "'";
        $run_oneletrajz = oci_parse($conn, $query_oneletrajz);
        oci_execute($run_oneletrajz);
        while($record = oci_fetch_array($run_oneletrajz, OCI_RETURN_NULLS) != false){
            if (!oci_field_is_null($run_oneletrajz, 1)) {
                $code = key($_POST['btn']);
                $email = $_SESSION["email"];
                $jelentkezik = "INSERT INTO JELENTKEZES (EMAIL, A_ID, MIKOR) VALUES (:1, :2, SYSDATE)";
                $stmt = oci_parse($conn, $jelentkezik);


                oci_bind_by_name($stmt, ':1', $email);
                oci_bind_by_name($stmt, ':2', $code);

                oci_execute($stmt);
                oci_commit($conn);
                oci_free_statement($stmt);
            } else {
                $GLOBALS["err"] = "Nincs önéletrajzod";
            }

            if($GLOBALS["err"] != ""){ ?>
                <?php echo($GLOBALS["err"]);
            }
        }
    }

}
    if(isset($_POST["jobdelete"])){
        $query = "DELETE FROM ALLASHIRDETES WHERE A_ID =" . $_POST["jobdelete"] . "";
        $delete = oci_parse($conn, $query);

        oci_execute($delete);
        oci_commit($conn);
        unset($_POST["jobdelete"]);

        oci_free_statement($delete);

        header("Location: jobs.php");
        exit();
    }

    if (isset($_POST["delapply"])){

        $query = "DELETE FROM JELENTKEZES WHERE A_ID =" . $_POST["delapply"] . " AND EMAIL = '" . $_SESSION["email"] . "' ";
        $deleteapp = oci_parse($conn, $query);

        oci_execute($deleteapp);
        oci_commit($conn);
        unset($_POST["delapply"]);

        oci_free_statement($deleteapp);

        header("Location: jobs.php");
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
        <form id="jobSearch" method="get">
            <label>Hirdetések közötti keresés: </label>
            <input type="text" id="search" name="search" class="form-control" placeholder="<?php if(isset($_GET['search'])){echo $_GET['search']; } ?>">
            <button type="submit" class="btn btn-primary">Szűrés</button><br>
            <label style="font-size: 11px;">(Szűrés lehetséges a rövid illetve hosszú leírás, munkavégzés helye, követelmények vagy hirdető cég szerint)</label>
        </form>
    </div>
    <table>
        <tr>
            <th> A munka röviden </th>
            <th> Bővebb leírás </th>
            <th> Felvételhez szükséges tudás és elvárások </th>
            <th> Havi Bruttó fizetés </th>
            <th> Munkavégzés helye </th>
            <th> Hirdető Cég </th>
            <?php
            if(isset($_SESSION["email"]) && !empty($user) && empty($_SESSION['admin'])){
                ?>
                <th> Jelentkezés </th>
            <?php
            }if(!empty($_SESSION['admin'])){
            ?>
                <th> Műveletek </th> <?php
            }
            ?>
        </tr>
        <?php

        if(isset($_GET['search']))
        {
            $filtervalues = trim($_GET['search']);
            $query = "SELECT ALLASHIRDETES.*, CEG.NEV FROM ALLASHIRDETES INNER JOIN CEG ON ALLASHIRDETES.C_EMAIL = CEG.C_EMAIL WHERE LOWER(ALLASHIRDETES.SZALAGCIM) LIKE '%" . strtolower($filtervalues) . "%' OR LOWER(ALLASHIRDETES.FELTETEL) LIKE '%" . strtolower($filtervalues) . "%' OR LOWER(CEG.NEV) LIKE '%" . $filtervalues . "%' OR LOWER(ALLASHIRDETES.M_HELYE) LIKE '%" . strtolower($filtervalues) . "%'";
            $query_run = oci_parse($conn, $query);
            oci_execute($query_run);

                while($record = oci_fetch_array($query_run, OCI_ASSOC+OCI_RETURN_NULLS))
                {
                    $sql = "SELECT * FROM JELENTKEZES WHERE EMAIL = :email AND A_ID = :aid";
                    $stmt = oci_parse($conn, $sql);

                     $apply = false;

                    oci_bind_by_name($stmt, ':email', $_SESSION["email"]);
                    oci_bind_by_name($stmt, ':aid', $record["A_ID"]);
                    oci_execute($stmt);

                    if (oci_fetch($stmt)){ $apply = true; }
                    ?>
                    <tr>
                        <td> <?php echo $record['SZALAGCIM'] ?> </td>
                        <td> <?php echo $record['LEIRAS'] ?> </td>
                        <td> <?php echo $record['FELTETEL'] ?> </td>
                        <td> <?php echo $record['FIZETES'] . ' Ft' ?> </td>
                        <td> <?php echo $record['M_HELYE'] ?> </td>
                        <td ><a href="firmprofile.php?firm=<?php echo $record['C_EMAIL'] ?>"> <?php echo $record['NEV'] ?></a> </td>

                            <?php
                            if(isset($_SESSION["email"]) && !empty($user) && empty($_SESSION["admin"])){
                                if(!$apply){ ?>
                                <form action="jobs.php" method="post">
                                    <td><button class="norm_button" type="submit" name="btn[<?php echo $record['A_ID'] ?>]">Jelentkezes</button></td>
                                </form>
                                <?php
                                }else{ ?>
                                    <form action="jobs.php" method="post">
                                        <td><button class="del_tbn" type="submit" name="delapply" value="<?php echo $record['A_ID'] ?>">Visszavon</button></td>
                                    </form> <?php
                                }
                            if(!empty($_SESSION["admin"])){ ?>
                                <form action="jobs.php" method="post">
                                    <td> <button class="del_tbn" type="submit" name="jobdelete" value="<?php echo trim($record['A_ID']); ?>">Hirdetés törlése</button> </td>
                                </form>
                            <?php
                                }
                            } }?>
                    </tr>
        <?php } else {
        while($record = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
        $sql = "SELECT * FROM JELENTKEZES WHERE EMAIL = :email AND A_ID = :aid";
        $stmt = oci_parse($conn, $sql);

        $apply = false;

        oci_bind_by_name($stmt, ':email', $_SESSION["email"]);
        oci_bind_by_name($stmt, ':aid', $record["A_ID"]);
        oci_execute($stmt);

        if (oci_fetch($stmt)){ $apply = true; }
        ?>
        <tr>
            <td> <?php echo $record['SZALAGCIM'] ?> </td>
            <td> <?php echo $record['LEIRAS'] ?> </td>
            <td> <?php echo $record['FELTETEL'] ?> </td>
            <td> <?php echo $record['FIZETES'] . ' Ft' ?> </td>
            <td> <?php echo $record['M_HELYE'] ?> </td>
            <td > <a href="firmprofile.php?firm=<?php echo $record['C_EMAIL'] ?>"> <?php echo $record['NEV'] ?></a> </td>

            <?php
            if(isset($_SESSION["email"]) && !empty($user) && empty($_SESSION['admin'])){
                if(!$apply){ ?>
                    <form action="jobs.php" method="post">
                        <td><button class="norm_button" type="submit" name="btn[<?php echo $record['A_ID'] ?>]">Jelentkezes</button></td>
                    </form>
                    <?php
                }else{ ?>
                    <form action="jobs.php" method="post">
                        <td><button class="del_tbn" type="submit" name="delapply" value="<?php echo $record['A_ID'] ?>">Visszavon</button></td>
                    </form> <?php
                }
                }
                if(!empty($_SESSION["admin"])){ ?>
                    <form action="jobs.php" method="post">
                        <td> <button class="del_tbn" type="submit" name="jobdelete" value="<?php echo trim($record['A_ID']); ?>">Hirdetés törlése</button> </td>
                    </form>
                <?php
                    }
                }
            }
            ?>

        </tr>

    </table>

</div>
<?php include_once 'footer.php'?>


