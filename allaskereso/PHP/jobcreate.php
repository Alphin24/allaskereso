<?php
require_once 'connect.php';
global $conn;
$GLOBALS["err"] = "";

require_once "actions.php";
global $user;

if(isset($_SESSION['email'])){
    $query = "SELECT * FROM CEG WHERE C_EMAIL = '" . $_SESSION["email"] . "'";
    $stmt = oci_parse($conn, $query);

    oci_execute($stmt);
    $user = oci_fetch_assoc($stmt);

    if (isset($_POST["submit"])) {
        if (trim($_POST["szalagcim"]) == "") {
            $GLOBALS["err"] = "Kell szalagcím";
            $error = true;
        } else if ($_POST["fizetes"] == 0) {
            $GLOBALS["err"] = "Kell Fizetést adni, nincs rabszolgaság :)";
            $error = true;
        } else if (trim($_POST["m_helye"]) == "") {
            $GLOBALS["err"] = "Adj meg egy nevet!";
            $error = true;
        } else {
            $query = "INSERT INTO ALLASHIRDETES (SZALAGCIM, LEIRAS, FELTETEL, FIZETES, M_HELYE, C_EMAIL) VALUES (:1, :2, :3, :4, :5, :6)";
            $stid = oci_parse($conn, $query);

            $szalagcim = trim($_POST["szalagcim"]);
            $leiras = trim($_POST["leiras"]);
            $feltetel = trim($_POST["feltetel"]);
            $fizetes = $_POST["fizetes"];
            $m_helye = trim($_POST["m_helye"]);
            $c_email = trim($_SESSION["email"]);

            $data = array($szalagcim, $leiras, $feltetel, $fizetes, $m_helye, $c_email);

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

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Munkák</title>
    <link rel="stylesheet" href="../CSS/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<?php require_once "header.php"?>

<?php if(isset($_SESSION["email"])){
    /*echo $_SESSION["email"];*/
    ?>
<div class="profileDiv">
    <section id="profileBox">
    <form action="jobcreate.php" method="post">
        <table><tr>
                <th>Szalagcím*</th><td><textarea placeholder="*Kötelező megadni*" type="longText" rows="2" cols="50" value="<?php if(isset($_POST["szalagcim"])){echo $_POST["szalagcim"] ; } ?>"  name="szalagcim"></textarea></td></tr><tr>
                <th>Leírás</th><td><textarea type="longText" rows="6" cols="50" value="<?php if(isset($_POST["leiras"])){echo $_POST["leiras"] ; } ?>" name="leiras"></textarea></td></tr><tr>
        <th>Feltétel</th><td><input type="text" value="<?php if(isset($_POST["feltetel"])){echo $_POST["feltetel"] ; } ?>"  name="feltetel"></td></tr><tr>
                <th>Fizetés Forintban*</th><td><input placeholder="*Kötelező megadni*" type="number" value="<?php if(isset($_POST["fizetes"])){echo $_POST["fizetes"] ; } ?>" name="fizetes"></td></tr><tr>
        <th>Munkavégzés helye*</th><td><input placeholder="*Kötelező megadni*" type="text" value="<?php if(isset($_POST["m_helye"])){echo $_POST["m_helye"] ; } ?>" name="m_helye">
        </tr></table>
        <button type="submit" name="submit" class="reg" style="margin: auto;">Álláshirdetés létrehozása</button>
    </form>
    <a href="jobs.php" style="color: white">Vissza a hirdetésekhez</a>
    <a href="index.php" style="color: white">Vissza a főoldalra</a> <?php
}
?>
    </section>
</div>


<?php
if($GLOBALS["err"] != ""){ ?>
<?php echo($GLOBALS["err"]);
}?>

</body>
</html>
