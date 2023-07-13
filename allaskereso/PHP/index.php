<?php
require_once 'connect.php';
global $conn;


?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Főoldal</title>
    <link rel="stylesheet" href="../CSS/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<?php include_once 'header.php'?>
<?php if(empty($_SESSION["admin"])){ ?>
    <div id="main">
        <h1>Üdvözüljük a weboldalunkon!</h1>
        <img id="mainpic" src="../RES/lego.jpg" alt="háttér">
    </div>
    <div id="leiras">
        <h2>A weboldal célja</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam tincidunt, magna eget dignissim bibendum, augue quam ultricies risus, a porta neque nisi in ipsum. Morbi tincidunt finibus euismod. Nullam at nibh odio. Suspendisse vehicula diam et purus porttitor congue quis sit amet mi. Sed nec sodales nibh, nec vulputate diam. Sed ac turpis ante. Sed ac felis eu est rutrum elementum egestas sit amet ligula. Aliquam libero arcu, vestibulum ut vestibulum non, elementum sed velit. Fusce dignissim ligula a enim pharetra mattis. Pellentesque accumsan efficitur est eget rutrum. Nunc in ligula lobortis est maximus molestie. Nam id tempor metus, vitae convallis risus. Integer eu pellentesque leo.</p>
    </div>
    <?php
}else{ ?>
    <div id="main">
        <h2>Helló Admin!</h2>
    </div>
    <style>
        body{
            background-image: url("../RES/admin.jpg");
            background-size: cover;

        }
        h2{
            font-size: 80px;
            margin: 50px;
        }
    </style>
    <h1>Statisztikai Adatok</h1>
    <?php
        $stid2 = oci_parse($conn, "SELECT NEV, COUNT(A_ID) AS ALLASSZAM FROM CEG, ALLASHIRDETES WHERE ALLASHIRDETES.C_EMAIL = CEG.C_EMAIL GROUP BY NEV ORDER BY ALLASSZAM 
DESC FETCH NEXT 1 ROWS ONLY");
        oci_execute($stid2);
        $row2 = oci_fetch_array($stid2, OCI_ASSOC+OCI_RETURN_NULLS);
        ?>
    <div class="container"
        <div class = "box">
            <table>
                <tr>
                    <td>Legtöbb hirdetéssel rendelkező cég: </td>
                    <td><?php echo $row2['NEV'] ?></td>
                    <td><?php echo $row2['ALLASSZAM'].' db' ?></td>
                </tr>
            </table>
        </div>
    </div>


    <h1>Hirdetésszámok megyékre bontva:</h1>
    <?php
    $stid3 = oci_parse($conn, "SELECT TELEPULESEK.megye, COUNT(*) as allashirdetesek_szama
FROM ALLASHIRDETES
JOIN CEG ON ALLASHIRDETES.C_EMAIL = CEG.C_EMAIL
JOIN TELEPULESEK ON CEG.TELEPULES = TELEPULESEK.TELEPULES
GROUP BY TELEPULESEK.megye
");
    oci_execute($stid3);
    ?>
    <div class="container">
        <div class = "box">
            <table>
                <tr>
                    <th> Megye </th>
                    <th> Hirdetésszám </th>
                </tr>
                <?php while($record = oci_fetch_array($stid3, OCI_ASSOC+OCI_RETURN_NULLS)){ ?>
                    <tr>
                        <td> <?php echo $record['MEGYE'] ?> </td>
                        <td> <?php echo $record['ALLASHIRDETESEK_SZAMA'] ?> </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <h1>Az adatbázisban levő településeken mennyi Cég hirdet és nekik mennyi az átlagértékelésük</h1>
    <?php
    $stid4 = oci_parse($conn, "SELECT TELEPULESEK.telepules, COUNT(CEG.c_email) AS cegek_szama, AVG(e.ertekeles) AS atlag_ertekeles
FROM TELEPULESEK
LEFT JOIN CEG ON TELEPULESEK.telepules = CEG.telepules
LEFT JOIN (
    SELECT CEG.telepules, AVG(ERTEKELESEK.ertekeles) AS ertekeles
    FROM ERTEKELESEK
    INNER JOIN CEG ON ERTEKELESEK.c_email = CEG.c_email
    GROUP BY CEG.telepules
) e ON TELEPULESEK.telepules = e.telepules
GROUP BY TELEPULESEK.telepules");
    oci_execute($stid4);
    ?>
    <div class="container">
        <div class = "box">
            <table>
                <tr>
                    <th> Település </th>
                    <th> Cégek száma </th>
                    <th> Átlag értékelés </th>
                </tr>
                <?php while($record = oci_fetch_array($stid4, OCI_ASSOC+OCI_RETURN_NULLS)){ ?>
                    <tr>
                        <td> <?php echo $record['TELEPULES'] ?> </td>
                        <td> <?php echo $record['CEGEK_SZAMA'] ?> </td>
                        <?php if($record['ATLAG_ERTEKELES'] != 0) {?>
                        <td> <?php echo $formatted_num = number_format($record['ATLAG_ERTEKELES'], 2, '.', ''); ?> </td>
                        <?php
                        } else { ?>
                            <td><?php echo "Nincs értékelve" ?></td>
                                <?php
                        }
                        ?>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <h1>Cégek, akiknek van hirdetése, abból mennyi van, és ezekre eddig hányan jelentkeztek</h1>
    <?php
    $stid5 = oci_parse($conn, "SELECT CEG.nev, COUNT(DISTINCT ALLASHIRDETES.a_id) AS hirdetes_szam, COUNT(DISTINCT JELENTKEZES.email) AS jelentkezesek_szama
FROM ALLASHIRDETES
JOIN CEG ON ALLASHIRDETES.c_email = CEG.c_email
LEFT JOIN JELENTKEZES ON ALLASHIRDETES.a_id = JELENTKEZES.a_id
GROUP BY CEG.nev");
    oci_execute($stid5);
    ?>
    <div class="container">
        <div class = "box">
            <table>
                <tr>
                    <th> Cégek </th>
                    <th> Hirdetésszám </th>
                    <th> Jelentkezések száma </th>
                </tr>
                <?php while($record = oci_fetch_array($stid5, OCI_ASSOC+OCI_RETURN_NULLS)){ ?>
                    <tr>
                        <td> <?php echo $record['NEV'] ?> </td>
                        <td> <?php echo $record['HIRDETES_SZAM'] ?> </td>
                        <td> <?php echo $record['JELENTKEZESEK_SZAMA'] ?> </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <h1>Magas fizetéseket pályázó felasználók:</h1>
    <?php
    $stid6 = oci_parse($conn, "SELECT DISTINCT felhasznalok.nev
        FROM felhasznalok
        JOIN jelentkezes ON felhasznalok.email = jelentkezes.email
        WHERE jelentkezes.a_id IN (
         SELECT a_id
        FROM allashirdetes
         WHERE fizetes > 500000
            )");
    oci_execute($stid6);
    ?>
    <div class="container">
        <div class = "box">
            <table>
                <tr>
                    <th> Felhasználók </th>
                </tr>
                <?php while($record = oci_fetch_array($stid6, OCI_ASSOC+OCI_RETURN_NULLS)){ ?>
                    <tr>
                        <td> <?php echo $record['NEV'] ?> </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>


<?php } ?>
</html>

<?php include_once 'footer.php'?>