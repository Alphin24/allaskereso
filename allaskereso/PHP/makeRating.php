<?php

    require_once 'connect.php';
    global $conn;



?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Értékelés</title>
    <link rel="stylesheet" href="../CSS/global.css">
</head>
<div id="makeRating">
    <?php
    require_once "header.php";


    global $firm;


    if (isset($_GET['firm'])){

        $sql = "SELECT* FROM CEG WHERE C_EMAIL = '" . $_GET["firm"] . "'";
        $stmt = oci_parse($conn, $sql);

        oci_execute($stmt);


        $firm = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);

    }



    if(isset($_POST["rate"])){

        $error = false;


        if (trim($_POST["num"]) == "") {
            $GLOBALS["err"] = "Adj értékelést!";
            $error=true;
        } else if (trim($_POST["longText"]) == "")  {
            $GLOBALS["err"] = "Részletezd!";
            $error=true;
        }





        if (!$error) {

            $query = "INSERT INTO ERTEKELESEK (EMAIL,C_EMAIL,ERTEKELES,TARTALOM)  VALUES (:1, :2, :3, :4)";
            $stid = oci_parse($conn, $query);

            $email = $_SESSION['email'];
            $cemail = trim($firm["C_EMAIL"]);
            $rate = $_POST["num"];
            $text = $_POST["longText"];


            $data = array($email,$cemail , $rate,$text);
            oci_bind_by_name($stid, ':1', $data[0]);
            oci_bind_by_name($stid, ':2', $data[1]);
            oci_bind_by_name($stid, ':3', $data[2]);
            oci_bind_by_name($stid, ':4', $data[3]);


            oci_execute($stid);
            oci_commit($conn);


            oci_free_statement($stid);
            header("Location: jobs.php");
        }
    }

    ?>
    <form method="post" action="makeRating.php?firm=<?php echo $firm["C_EMAIL"]?>" class="makeRating">
        <p style="color: white;" >Üdvözöllek a(z) <?php echo $firm["NEV"];?> értékelő oldalán</p><br>
        <table><tr><th style="color: white">Hány pontra értékeli?</th>
                <td><label><input style="color: white; border-bottom: 1px solid white" name="num" type="number" min="1" max="10" value="1"></label></td></tr><tr>
                <th rowspan="1" style="color: white;">Megjegyzés:</th><td><label><textarea name="longText" rows="4" cols="50"></textarea></label></td></tr><tr>
                <td></td><td><input type="submit" style="float: right" name="rate" id="RatingButton" value="Értékelés elküldése"></td></tr><tr>
        </table>
    </form>
</div>


<?php
if($GLOBALS["err"] != ""){ ?>
    <?php function_alert($GLOBALS["err"]);
}?>


<?php require_once "footer.php" ?>
