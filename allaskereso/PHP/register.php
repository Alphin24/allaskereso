<?php

    $GLOBALS["err"] = "";
    require_once 'connect.php';
    global $conn;

    if (isset($_POST["submit"])) {

        $validEmail = false;
        $error = false;


        if (trim($_POST["email"]) == "") {
            $GLOBALS["err"] = "Adj meg egy email címet!";
            $error=true;
        } else if (trim($_POST["name"]) == "")  {
            $GLOBALS["err"] = "Adj meg egy nevet!";
            $error=true;
        }else if(strlen(trim($_POST["pass1"])) < 6) {
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

            $userExists = false;

            $email = $_POST['email'];
            $sql = "SELECT * FROM FELHASZNALOK WHERE EMAIL = :email";
            $stmt = oci_parse($conn, $sql);

            oci_bind_by_name($stmt, ':email', $email);

            oci_execute($stmt);

            $sql2 = "SELECT * FROM CEG WHERE C_EMAIL = :email";
            $stmt2 = oci_parse($conn, $sql2);

            oci_bind_by_name($stmt2, ':email', $email);
            oci_execute($stmt2);

            if (oci_fetch($stmt) || oci_fetch($stmt2)) {
                $userExists = true;
                $GLOBALS["err"] = "Az emailcím foglalt!";
            }

            if (!$userExists && !$error) {

                $query = "INSERT INTO FELHASZNALOK (email, jelszo, nev, telefonszam, szerepkor, oneletrajz) VALUES (:1, :2, :3, :4, :5, :6)";
                $stid = oci_parse($conn, $query);

                $email = $_POST["email"];
                $pass =  md5($_POST["pass1"]);
                $name = $_POST["name"];
                $number = $_POST["number"];
                $resume = $_POST["resume"];


                $data = array($email, $pass, $name, $number, "Álláskereső", $resume);
                oci_bind_by_name($stid, ':1', $data[0]);
                oci_bind_by_name($stid, ':2', $data[1]);
                oci_bind_by_name($stid, ':3', $data[2]);
                oci_bind_by_name($stid, ':4', $data[3]);
                oci_bind_by_name($stid, ':5', $data[4]);
                oci_bind_by_name($stid, ':6', $data[5]);

                oci_execute($stid);
                oci_commit($conn);


                oci_free_statement($stid);
                header("Location: index.php");
            }
        }
    }


    require_once "actions.php";
    global $user;

    if(isset($_SESSION['email'])){
        $query = "SELECT * FROM FELHASZNALOK WHERE EMAIL = '" . $_SESSION["email"] . "'";
        $stmt = oci_parse($conn, $query);

        oci_execute($stmt);

        $user = oci_fetch_assoc($stmt);
    }
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="../CSS/global.css">
</head>
<?php require_once "header.php"?>
<div class="profileDiv">
<?php if(!isset($_SESSION["email"])){  ?>
    <div id="profileBox"  style="height: max-content">
        <h1>Regisztráció</h1>
        <form action="register.php" method="post">
            <table><tr>
            <th>*Email</th><td><input placeholder="*Kötelező megadni*" value="<?php if(isset($_POST["email"])){echo $_POST["email"] ; } ?>" type="email" name="email"></td></tr><tr>
            <th>*Név</th><td><input placeholder="*Kötelező megadni*" type="text" value="<?php if(isset($_POST["name"])){echo $_POST["name"] ; } ?>" name="name"></td></tr><tr>
            <th>Telefonszám</th><td><input placeholder="*Kötelező megadni*" type="text" value="<?php if(isset($_POST["number"])){echo $_POST["number"] ; } ?>" placeholder="0620-302-79-59" name="number"></td></tr><tr>
                    <th>Önéletrajz</th><td><textarea  type="text" value="<?php if(isset($_POST["resume"])){echo $_POST["resume"] ; } ?>" name="resume"></textarea></td></tr><tr>
            <th>*Jelszó</th><td><input placeholder="*Kötelező megadni*" type="password"  name="pass1"></td></tr><tr>
            <th>*Jelszó ismét</th><td><input placeholder="*Kötelező megadni*" type="password"  name="pass2"></td>
            </tr></table>
            <button type="submit" name="submit" class="reg" style="margin: auto;">Regisztráció</button>
        </form>
        <a href="firmregister.php" style="color: white">cégreg</a>
        <a href="index.php" style="color: white">login</a>
    </div>
    <?php
}else{ ?>
    <div id="profileBox" style="height: 500px">
        <form action="register.php" method="post">
            <table><tr>
            <th>*Név</th><td><input type="text" value="<?php echo $user["NEV"] ?>" name="name"></td></tr><tr>
            <th>Telefonszám</th><td><input type="text" value="<?php echo $user["TELEFONSZAM"] ?>" placeholder="0620-302-79-59" name="number"></td></tr><tr>
            <th>Önéletrajz</th><td><input type="text" value="<?php echo $user["ONELETRAJZ"];  ?>" name="resume"></td></tr><tr>
            <th>**Régi Jelszó</th><td><input type="password" value="Random" name="opass"></td>
            </tr></table>
            <button type="submit" name="change" >Módosít</button>
            <button type="submit" name="delete" >Töröl</button>
        </form>
    </div>
    <?php
} ?>


    <?php
    if($GLOBALS["err"] != ""){ ?>
            <?php function_alert($GLOBALS["err"]);
    }?>

</div>
</body>
</html>
