<?php


    require_once 'connect.php';
    global $conn;
    session_start();


    if(isset($_SESSION["email"])){
        $query = "SELECT * FROM FELHASZNALOK WHERE EMAIL = '" . $_SESSION["email"] . "'";
        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);
        global $user;
        $user = oci_fetch_assoc($stmt);
    }

    if (array_key_exists("logout", $_POST)) {
        session_unset();
        session_destroy();
        header("Location: index.php");
    }

    if (array_key_exists("delete", $_POST)) {
        $query = "DELETE FROM FELHASZNALOK WHERE EMAIL = '" . $_SESSION["email"] . "' ";
        $stid = oci_parse($conn, $query);


        oci_execute($stid);
        oci_commit($conn);

        oci_free_statement($stid);
        oci_close($conn);

        session_unset();
        session_destroy();
        header("Location: index.php");
    }


    if (array_key_exists("change", $_POST)) {

        $new = md5($_POST["opass"]);
        $old = trim($user["JELSZO"]);


        if($old == $new){

            $error = false;


            if (trim($_POST["name"]) == "")  {
                $GLOBALS["err"] = "Adj meg egy nevet!";
                $error=true;
            }

            if(!$error){


                $email = $_SESSION["email"];
                $name = $_POST["name"];
                $number = $_POST["number"];
                $resume = $_POST["resume"];

                $stmt = oci_parse($conn, 'BEGIN update_felhasznalok(:email, :nev, :telefonszam, :oneletrajz); END;');
                oci_bind_by_name($stmt, ':email', $email);
                oci_bind_by_name($stmt, ':nev', $name);
                oci_bind_by_name($stmt, ':telefonszam', $number);
                oci_bind_by_name($stmt, ':oneletrajz', $resume);
                oci_execute($stmt);
                oci_commit($conn);
                oci_free_statement($stmt);
                header("Location: index.php");

            }


        }else{
            $GLOBALS["err"] = "Helytelen jelszó!";
        }
    }

    //if(array_key_exists("mod", $_POST)){
    //    session_start();
    //    $_SESSION["mod"] = $_POST["mod"];
    //    global $code_job;
    //    $code_job = $_POST["mod"];
    //    unset($_POST["mod"]);
    //    header("Location: jobchange.php");
    //}
?>