<?php   require_once 'connect.php';
require_once "actions.php";
global $conn;
global $email;
global $user;
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="../CSS/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<?php include_once 'header.php'?>
<?php
    /*echo'<div class="profileDiv">';
        echo'<div id="profileBox">';
            echo'<h1>Profil</h1>';
            echo'<table>';
                if(isset($_SESSION["email"])){
                    if($user){
                        $stid = oci_parse($conn, "SELECT NEV, TELEFONSZAM FROM FELHASZNALOK WHERE email='".$_SESSION['email']."'");
                        oci_execute($stid);
                        $row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
                        echo '<tr><th><strong style="float: left">Név: </strong></th><td><p>'.$row['NEV'].'</p></td>';
                        echo '</tr>';
                        echo '<tr><th><strong style="float: left">Email: </strong></th><td><p>'.$_SESSION["email"].'</p></td>';
                        echo '</tr>';
                        echo '<tr><th><strong style="float: left">Telefonszám: </strong></th><td><p>'.$row['TELEFONSZAM'].'</p></td>';
                        echo '</tr>';
                        echo'</table>';
                        echo'<form>';
                        echo'<button formaction="register.php" name="modify" style="margin: auto">Módosít</button>';
                        echo'</form>';
                    } else {
                        $stid = oci_parse($conn, "SELECT nev, c_email, telefonszam, telepules, utca, hazszam FROM ceg WHERE c_email='".$_SESSION['email']."'");
                        oci_execute($stid);
                        $row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
                         echo '<tr><th><strong style="float: left">Név: </strong></th><td><p>'.$row['NEV'].'</p></td>';
                         echo '</tr>';
                         echo '<tr><th><strong style="float: left">Email: </strong></th><td><p>'.$row["C_EMAIL"].'</p></td>';
                         echo '</tr>';
                         echo '<tr><th><strong style="float: left">Telefonszám: </strong></th><td><p>'.$row['TELEFONSZAM'].'</p></td>';
                         echo '</tr>';
                         echo '<tr><th><strong style="float: left">Cím: </strong></th><td><p>'.$row['TELEPULES'].' '.$row['UTCA'].' '.$row['HAZSZAM'].'.</p></td>';
                         echo '</tr>';
                         echo'</table>';
                        echo'<form>';
                        echo'<button formaction="firmregister.php" name="modify" style="margin: auto">Módosít</button>';
                        echo'</form>';
                    }
                }
        echo'</div>';
    echo'</div>';*/


?>
<div class="profileDiv">
    <div id="profileBox">
        <h1>Profil</h1>
        <table>
            <?php
            if(isset($_SESSION["email"])){
                if($user){
                    $stid = oci_parse($conn, "SELECT NEV, TELEFONSZAM, SZEREPKOR FROM FELHASZNALOK WHERE email='".$_SESSION['email']."'");
                    oci_execute($stid);
                    $row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
                    ?>
                    <tr>
                        <th><strong style="float: left">Név: </strong></th>
                        <td><p><?php echo $row['NEV'] ?></p></td>
                    </tr>
                    <tr>
                        <th><strong style="float: left">Email: </strong></th>
                        <td><p><?php echo $_SESSION["email"] ?></p></td>
                    </tr>
                    <tr>
                        <th><strong style="float: left">Telefonszám: </strong></th>
                        <td><p><?php echo $row['TELEFONSZAM'] ?></p></td>
                    </tr>
                    <tr><th><td>
                            <form>
                                <button formaction="register.php" name="modify" style="margin: auto">Módosít</button>
                            </form>
                        </td></th></tr>
                    <?php
                } else {
                    $stid = oci_parse($conn, "SELECT nev, c_email, telefonszam, telepules, utca, hazszam FROM ceg WHERE c_email='".$_SESSION['email']."'");
                    oci_execute($stid);
                    $row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS); ?>
                    <tr>
                        <th><strong style="float: left">Név: </strong></th>
                        <td><p><?php echo $row['NEV'] ?></p></td>
                    </tr>
                    <tr>
                        <th><strong style="float: left">Email: </strong></th>
                        <td><p><?php echo $row["C_EMAIL"] ?></p></td>
                    </tr>
                    <tr>
                        <th><strong style="float: left">Telefonszám: </strong></th>
                        <td><p><?php echo $row['TELEFONSZAM'] ?></p></td>
                    </tr>
                    <tr>
                        <th><strong style="float: left">Cím: </strong></th>
                        <td><p><?php echo $row['TELEPULES'] . ' ' . $row['UTCA'] . ' ' . $row['HAZSZAM'] ?></p></td>
                    </tr>
                    <tr>
                        <td><a href="applications.php" style="color: white; margin: auto">Jelentkezések</a></td><td>
                            <form>
                                <button formaction="firmregister.php" name="modify" style="margin: auto">Módosít</button>
                            </form>
                        </td>
                    </tr>
        </table>
    </div>
    <div id="profileBox" >
        <table>
            <tr>
                <th> <h2>Hirdetett Állások</h2> </th>
            </tr>
            <?php

            $stid = oci_parse($conn, "SELECT A_ID, SZALAGCIM FROM ALLASHIRDETES WHERE ALLASHIRDETES.C_EMAIL = '" . $_SESSION["email"] . "'");
            oci_execute($stid);

            while($record = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
                ?>
                <tr>
                    <td><?php echo $record['SZALAGCIM'] ?></td>
                    <td><form action="jobchange.php" method="post">
                            <button type="submit" name="jobmod" value ="<?php echo $record['A_ID']; ?>"style="margin: auto">Módosítás</button>
                        </form></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>

                    <?php
                }
            } ?>


</div>
<?php include_once 'footer.php'?>
