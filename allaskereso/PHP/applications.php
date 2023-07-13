<?php   require_once 'connect.php';
    require_once "actions.php";
    global $conn;
    global $email;
    global $user;

    if (isset($_POST["hire"])){
        $sql = "DELETE JELENTKEZES WHERE A_ID = :id";
        $stmt = oci_parse($conn, $sql);


        oci_bind_by_name($stmt, ':id', $_POST["hire"]);
        oci_execute($stmt);
        oci_commit($conn);
        oci_free_statement($stmt);


        $sql = "DELETE ALLASHIRDETES WHERE A_ID = :id";
        $stmt = oci_parse($conn, $sql);


        oci_bind_by_name($stmt, ':id', $_POST["hire"]);
        oci_execute($stmt);
        oci_commit($conn);
        oci_free_statement($stmt);


        unset($_POST["hire"]);
        header("Location: applications.php");
        exit();
    }

    if (isset($_POST["abject"])){
        $values = explode(',', $_POST['abject']);




        $sql = "DELETE JELENTKEZES WHERE A_ID = :id AND EMAIL = :email ";
        $stmt = oci_parse($conn, $sql);


        oci_bind_by_name($stmt, ':id', $values[0]);
        oci_bind_by_name($stmt, ':email', $values[1]);
        oci_execute($stmt);
        oci_commit($conn);
        oci_free_statement($stmt);



        unset($_POST["abject"]);
        header("Location: applications.php");
        exit();
    }


?>
    <!DOCTYPE html>
    <html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="../CSS/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .applicationTable{
            font-size: 10px;
        }
    </style>
</head>
<?php include_once 'header.php'?>
<div class="profileDiv">
    <!--Állás jelentkezések-->
    <section id="profileBox" class="applicationTable" style="margin: 50px auto; height: max-content;">
        <h1>Jelentkezések</h1>
        <table>
            <?php
            if(isset($_SESSION["email"])){
                    ?>
                    <tr>
                        <th><strong style="float: left">Állás: </strong></th>
                        <th><strong style="float: left">Jelentkező: </strong></th>
                        <th><strong style="float: left">Jelentkezés dátuma: </strong></th>
                    </tr>
                    <?php
                    $stid = oci_parse($conn, "SELECT FELHASZNALOK.NEV AS FNEV,FELHASZNALOK.EMAIL, SZALAGCIM, MIKOR, ALLASHIRDETES.A_ID
                    FROM ALLASHIRDETES, JELENTKEZES, FELHASZNALOK, CEG 
                    WHERE JELENTKEZES.EMAIL=FELHASZNALOK.EMAIL AND JELENTKEZES.A_ID=ALLASHIRDETES.A_ID AND ALLASHIRDETES.C_EMAIL=CEG.C_EMAIL AND CEG.C_EMAIL='".$_SESSION['email']."'");
                    oci_execute($stid);
                    while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){?>
                        <tr>
                            <td><p><?php echo $row['SZALAGCIM'] ?></p></td>
                            <td><p><?php echo $row["FNEV"] ?></p></td>
                            <td><p><?php echo $row["MIKOR"] ?></p></td>
                            <td>
                                <form method="post" action="applications.php">
                                    <button class="norm_button" name="hire" value="<?php echo $row["A_ID"] ?>">Felvesz</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="applications.php">
                                    <button class="del_tbn" name="abject" value="<?php echo $row["A_ID"] ?>,<?php echo $row["EMAIL"] ?>">Elutasít</button>
                                </form>
                            </td>
                        </tr>
                    <?php
                    }
            }?>
        </table>
    </section>

</div>



<?php include_once 'footer.php'?>