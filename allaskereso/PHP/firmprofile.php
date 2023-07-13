<?php
    require_once 'connect.php';
    global $conn;

    global $firm;
    global $isFirm;

    if(isset($_GET['firm'])){
        $query = "SELECT * FROM CEG WHERE C_EMAIL = '" . $_GET["firm"] . "'";
        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);


        $firm = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
    }


?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Cég</title>
    <link rel="stylesheet" href="../CSS/global.css">
</head>
<?php require_once "header.php";
   if(isset($_SESSION["email"])){
       $query2 = "SELECT * FROM FELHASZNALOK WHERE EMAIL = '" . $_SESSION['email'] . "'";
       $stmt2 = oci_parse($conn, $query2);
       oci_execute($stmt2);
       $firm2 = oci_fetch_assoc($stmt2);

       if($firm2){
           $isFirm=false;
       }
       else{
           $isFirm=true;
       }
   }
//    ************ Átlag kereses

    global $avg;
    $stmt = oci_parse($conn, 'BEGIN FIRM_REVIEW(:in_email, :avg); END;');


    $avg = 0;
    oci_bind_by_name($stmt, ':in_email', $firm["C_EMAIL"]);
    oci_bind_by_name($stmt, ':avg', $avg, 32);
    oci_execute($stmt);
    oci_free_statement($stmt);


//    -------------------------------------------

?>
<div class="profileDiv">
    <section id="profileBox" style="height: 500px; width: max-content;">
        <h1><?php echo $firm['NEV'] ?></h1>
        <table>
            <tr>
                <th>Email: </th>
                <td><?php echo $firm['NEV'] ?></td></tr> <tr>
                <th>Telefonszám: </th>
                <td><?php echo $firm['TELEFONSZAM'] ?></td></tr> <tr>
                <th>Cím: </th>
                <td><?php echo $firm['TELEPULES'].', '.$firm['UTCA'].' '.$firm['HAZSZAM'] ?></td></tr> <tr>
                <?php if($avg > 0){ ?>
                <th>Értékelések: </th>
                <td>
                    <div class="star-container">
                        <?php
                        for ($i = 0; $i < $avg; $i++) { ?>
                            <img style="width: 20px" src="../RES/star.png" alt="/"> <?php
                        }
                        for ($i = $avg; $i < 10; $i++) { ?>
                            <img style="width: 20px" src="../RES/star2.png" alt="/"> <?php
                        }
                        ?>
                    </div>
                </td> <?php
                }
                ?>
            </tr>
        </table>
        <?php
            if(!$isFirm && isset($_SESSION["email"])){ ?>
                <a href="makeRating.php?firm=<?php echo $firm["C_EMAIL"] ?>" style="color: white">Értékeld</a>
            <?php } ?>
    </section>
</div>


<?php require_once "footer.php" ?>

