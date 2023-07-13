<?php

    $GLOBALS["err"] = "";
    require_once 'connect.php';
    global $conn;

    if (isset($_POST["submit"])) {

        $error = false;

        if (trim($_POST["email"]) == "") {
            $GLOBALS["err"] = "Adj meg egy email címet!";
            $error=true;
        } else if (trim($_POST["name"]) == "")  {
            $GLOBALS["err"] = "Adj meg egy nevet!";
            $error=true;
        } else if(trim($_POST["number"]) == ""){
            $GLOBALS["err"] = "A telefonszám mező kitöltése kötelező!";
            $error=true;
        } else if (trim($_POST["city"]) == "") {
            $GLOBALS["err"] = "Település mező kitöltése kötelező!";
            $error=true;
        }  else if(trim($_POST["area"]) == ""){
            $GLOBALS["err"] = "Megye mező kitöltése kötelező!";
            $error=true;
        } else if(trim($_POST["street"]) == ""){
            $GLOBALS["err"] = "Utca mező kitöltése kötelező!";
            $error=true;
        }  else if(trim($_POST["hnumber"]) == ""){
            $GLOBALS["err"] = "Házszám mező kitöltése kötelező!";
            $error=true;
        } else if(strlen(trim($_POST["pass1"])) < 6) {
            $GLOBALS["err"] = "A jelszónak legalább 6 karakternek kell lennie";
            $error=true;
        } else if (strtolower(trim($_POST["pass1"])) == trim($_POST["pass1"])) {
            $GLOBALS["err"] = "A jelszónak tartalmaznia kell legalább egy nagybetűt";
            $error=true;
        } else if (strtoupper(trim($_POST["pass1"])) == trim($_POST["pass1"])) {
            $GLOBALS["err"] = "A jelszónak tartalmaznia kell legalább egy kisbetűt";
            $error=true;
        } else if ($_POST["pass1"] != $_POST["pass2"]) {
            $GLOBALS["err"] = "A jelszavak nem egyeznek meg";
            $error=true;
        } else {

            $userExists =false;

            $email = $_POST['email'];
            $sql = "SELECT * FROM FELHASZNALOK WHERE EMAIL = :email";
            $stmt = oci_parse($conn, $sql);

            oci_bind_by_name($stmt, ':email', $email);

            oci_execute($stmt);

            $sql2 = "SELECT * FROM CEG WHERE EMAIL = :email";
            $stmt2 = oci_parse($conn, $sql2);

            oci_bind_by_name($stmt2, ':email', $email);

            oci_execute($stmt2);

            if (oci_fetch($stmt) || oci_fetch($stmt2)) {
                $userExists = true;
                $GLOBALS["err"] = "Az emailcím foglalt!";
            }


            if(!$error && !$userExists){

                $cityExist = false;
                global $gcity;

                $city = $_POST['city'];
                $sql = "SELECT * FROM TELEPULESEK WHERE TELEPULES = :city";
                $stmt = oci_parse($conn, $sql);

                oci_bind_by_name($stmt, ':city', $city);

                oci_execute($stmt);

                if ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                    $cityExist = true;
                    $gcity = oci_result($stmt, 'MEGYE');
                }

                if(!$cityExist) {
                    $query = "INSERT INTO TELEPULESEK (TELEPULES, MEGYE) VALUES (:1, :2)";

                    $stid = oci_parse($conn, $query);


                    $area = $_POST["area"];
                    $city = $_POST["city"];

                    $data = array($city, $area);

                    oci_bind_by_name($stid, ':1', $data[0]);
                    oci_bind_by_name($stid, ':2', $data[1]);


                    oci_execute($stid);
                    oci_commit($conn);
                    oci_free_statement($stid);
                    $cityExist=true;
                }

                if($cityExist){

                    $query = "INSERT INTO CEG (C_EMAIL, JELSZO, NEV, TELEFONSZAM, TELEPULES, UTCA, HAZSZAM) VALUES (:1, :2, :3, :4, :5, :6, :7)";
                    $stid = oci_parse($conn, $query);

                    $email = $_POST["email"];
                    $name = $_POST["name"];
                    $pass =  md5($_POST["pass1"]);
                    $street = $_POST["street"];
                    $hnumber = $_POST["hnumber"];
                    $number = $_POST["number"];



                    $data = array($email, $pass, $name, $number, $city, $street, $hnumber);
                    oci_bind_by_name($stid, ':1', $data[0]);
                    oci_bind_by_name($stid, ':2', $data[1]);
                    oci_bind_by_name($stid, ':3', $data[2]);
                    oci_bind_by_name($stid, ':4', $data[3]);
                    oci_bind_by_name($stid, ':5', $data[4]);
                    oci_bind_by_name($stid, ':6', $data[5]);
                    oci_bind_by_name($stid, ':7', $data[6]);

                    oci_execute($stid);
                    oci_commit($conn);


                    oci_free_statement($stid);


                    header("Location: index.php");

                }

            }

        }
    }

    require_once "firmactions.php";
    global $firm;
    global $area;

    if(isset($_SESSION['email'])){
        $query = "SELECT * FROM CEG WHERE C_EMAIL = '" . $_SESSION["email"] . "'";
        $stmt = oci_parse($conn, $query);

        oci_execute($stmt);


        $firm = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);

        $query = "SELECT * FROM TELEPULESEK WHERE TELEPULES = '" . $firm["TELEPULES"] . "'";
        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $area = oci_fetch_assoc($stmt);

        oci_free_statement($stmt);
    }
?>





<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="../CSS/global.css">
</head>
<body>
<div class="profileDiv" style="height: 900px">
    <?php if(!isset($_SESSION["ceg"])){ ?>
        <div id="profileBox" style="height: 850px">
            <h1>Regisztráció</h1>
            <form  action="firmregister.php" method="post">
                <table>
                <tr>
                    <th>*Email</th><td><input placeholder="*Kötelező megadni*" value="<?php if(isset($_POST["email"])){echo $_POST["email"] ; } ?>" type="email" name="email"></td></tr>
                    <th>*Név</th><td><input type="text" placeholder="*Kötelező megadni*" value="<?php if(isset($_POST["name"])){echo $_POST["name"] ; } ?>" name="name"></td></tr>
                    <th>*Telefonszam</th><td><input placeholder="*Kötelező megadni* Form: 0620-302-79-59"  type="text" value="<?php if(isset($_POST["number"])){echo $_POST["number"] ; } ?>" name="number"></td></tr>
                    <th>*Telepulés</th><td><input placeholder="*Kötelező megadni*" type="text" value="<?php if(isset($_POST["city"])){echo $_POST["city"] ; } ?>"  name="city"></td></tr>
                    <th>*Megye</th><td><input placeholder="*Kötelező megadni*" type="text" value="<?php if(isset($_POST["area"])){echo $_POST["area"] ; } ?>"  name="area"></td></tr>
                    <th>*Utca</th><td><input placeholder="*Kötelező megadni*" type="text"  value="<?php if(isset($_POST["street"])){echo $_POST["street"] ; } ?>" name="street"></td></tr>
                    <th>*Házszám</th><td><input placeholder="*Kötelező megadni*" type="text" value="<?php if(isset($_POST["hnumber"])){echo $_POST["hnumber"] ; } ?>"  name="hnumber"></td></tr>
                    <th>*Jelszó</th><td><input placeholder="*Kötelező megadni*" type="password"  name="pass1"></td></tr>
                    <th>*Jelszó ismét</th><td><input placeholder="*Kötelező megadni*" type="password"  name="pass2"></td></tr>
                </table>
                <button type="submit" name="submit" class="reg" style="margin: auto">Regisztráció</button>
            </form>
            <a href="index.php" style="color:white">Főoldal</a>
        </div>
    <?php
    }else { ?>
        <div id="profileBox"  style="height: 500px">
        <form action="firmregister.php" method="post">
            <table>
                <tr>
                    <th>*Név</th><td><input type="text" value="<?php echo $firm["NEV"]; ?>" name="name"></td></tr><tr>
                    <th>*Telefonszam</th><td><input type="text" value="<?php echo $firm["TELEFONSZAM"];  ?>" placeholder="0620-302-79-59" name="number"></td></tr><tr>
                    <th>Új jelszó</th><td><input type="password" value="" name="pass1"></td></tr><tr>
                    <th>Új jelszó ismét</th><td><input type="password" value="" name="pass2"></td></tr><tr>
                    <th>**Régi jelszó</th><td><input type="password" value="" name="opass"></td></tr><tr>
                </tr>
            </table>
            <button type="submit" name="change" >Módosít</button>
            <button type="submit" name="delete" >Cég törlése</button>
            <a href="profile.php" style="color:white">Profil</a>
        </form>
        </div>
    <?php } ?>




    <?php
    if($GLOBALS["err"] != ""){ ?>
        <?php function_alert($GLOBALS["err"]);
    }?>
</div>
<footer>
    Készítették: Bánfi József, Mackovic Mark, Ferenczi Tamás Norbert
</footer>
</body>
</html>