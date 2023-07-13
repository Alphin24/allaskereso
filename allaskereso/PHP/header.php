<?php

    $GLOBALS["err"] = "";
    require_once 'connect.php';
    global $conn;
    global $user;
    global $email;
    if(isset($_SESSION["email"])){
        $email = $_SESSION["email"];
    }


    if (isset($_POST["in"])) {


        if (trim($_POST["email"]) == "") {
            $GLOBALS["err"] = "Adj meg egy email címet!";
            $error=true;
        } else if (trim($_POST["pass"]) == "")  {
            $GLOBALS["err"] = "Add meg a jelszavad!";
            $error=true;
        }else {

            $email = trim($_POST['email']);
            $password = trim(md5($_POST['pass']));


            $query = "SELECT * FROM FELHASZNALOK WHERE EMAIL = '" . $email . "' AND JELSZO = '" . $password . "'";
            $stmt = oci_parse($conn, $query);
            oci_execute($stmt);

            $query2 = "SELECT * FROM CEG WHERE C_EMAIL = '" . $email . "' AND JELSZO = '" . $password . "'";
            $stmt2 = oci_parse($conn, $query2);
            oci_execute($stmt2);

            if (oci_fetch($stmt)) {
                $email = oci_result($stmt, "EMAIL");
                $name = oci_result($stmt, "NEV");
                $admin = oci_result($stmt, "SZEREPKOR");


                session_start();
                $_SESSION["email"] = $email;
                $_SESSION["name"] = $name;
                if(trim($admin) == "Admin"){
                    $_SESSION["admin"] = true;
                }


                header("Location: index.php");
            } else if (oci_fetch($stmt2)) {
                $email = oci_result($stmt2, "C_EMAIL");
                $name = oci_result($stmt2, "NEV");

                session_start();
                $_SESSION["email"] = $email;
                $_SESSION["name"] = $name;
                $_SESSION["ceg"] = true;

                header("Location: index.php");
            } else {
                $GLOBALS["err"] = "Nincs ilyen email/jelszó páros!";
            }


        }
    }


    if(isset($_POST["modify"])){
        session_start();
        $_SESSION['email'] = $email;
        if(isset($_SESSION["ceg"])){
            $_SESSION["ceg"] = true;
        }
    }


    require_once "actions.php";

?>

<body>
<div class="topnav">
    <a class="pageLink" href="index.php">Főoldal</a>
    <a class="pageLink" href="jobs.php">Munkák</a>

    <?php if(!empty($_SESSION["admin"])){ ?> <a class="pageLink" href="review.php">Értékelések</a> <a class="pageLink" href="users.php">Felhasználók</a><a class="pageLink" href="companies.php">Cégek</a>  <?php } ?>
    <div class="search-container">
        <?php if(!isset($_SESSION["ceg"]) && !isset($_SESSION["email"])){ ?>
            <form action="register.php">
                <button name="modify">Regisztráció</button>
            </form>
        <?php } ?>

    </div>
    <div class="search-container">
        <?php if(!isset($_SESSION["email"])){ ?>
            <div>
                <form action="index.php" method="post" style="display: flex;width: 470px;gap:10px ;flex-direction: row">
                    <input type="text" id="fname" name="email" placeholder="Felhasználónév" size="10">
                    <input type="password" id="fpss" name="pass"  placeholder="Jelszó" size="10"><br>
                    <button type="submit" name="in">Bejelentkezés</button>
                </form>
            </div>   <?php
        }else{ ?>

                <div class="profileData">
                    <div class="row">
                        <img src="../RES/profile.png" alt="prof" style="width: 14px; height: 14px">
                        <a style="padding: 0 10px;" href="profile.php"><?php echo $_SESSION["name"]; ?></a>
                    </div>
                    <div class="row">
                        <img src="../RES/email.png" alt="email" style="width: 14px; height: 14px">
                        <?php echo $_SESSION["email"]; ?>
                    </div>
                </div>
            <div >
                <form action="index.php" method="post">
                    <button type="submit" name="logout">Kijelentkezés</button>
                </form>
            </div>
            <?php
                $c_user = true;
                $c_email = $_SESSION["email"];
                $query_c_user = "SELECT * FROM CEG WHERE C_EMAIL = '" . $c_email . "'";
                $stmt_c_user = oci_parse($conn, $query_c_user);
                oci_execute($stmt_c_user);
                if(!oci_fetch($stmt_c_user)){
                    $c_user = false;
                }
                if($c_user){
            ?>
            <div >
                <form action="jobcreate.php" method="post">
                <button type="submit" name="posting">Állás létrehozása</button>
                </form>
            </div>
            <?php
                }
            }
        ?>

    </div>

    <?php
    if($GLOBALS["err"] != ""){ ?>
        <?php function_alert($GLOBALS['err']);
    }?>



</div>

