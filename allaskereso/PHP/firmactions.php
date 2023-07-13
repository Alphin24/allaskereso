<?php


    require_once 'connect.php';
    global $conn;
    session_start();

    if(isset($_SESSION["email"])){
        $query = "SELECT * FROM CEG WHERE C_EMAIL = '" . $_SESSION["email"] . "'";
        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);
        global $firm;
        $firm = oci_fetch_assoc($stmt);
    }

    if (array_key_exists("delete", $_POST)) {

        $query = "DELETE FROM CEG WHERE C_EMAIL = '" . $_SESSION['email'] . "' ";
        $stid = oci_parse($conn, $query);



        oci_execute($stid);
        oci_commit($conn);

        oci_free_statement($stid);
        session_unset();
        session_destroy();


    }

    if(array_key_exists("change", $_POST)){

        $error = false;

        if (trim($_POST["name"]) == "")  {
            $GLOBALS["err"] = "A név mező kitöltése kötelező!";
            $error=true;
        }else if(trim($_POST["number"]) == ""){
            $GLOBALS["err"] = "A telefonszám mező kitöltése kötelező!";
            $error=true;
        }else if(strlen(trim($_POST["pass1"])) != 0 ){
                if(strlen(trim($_POST["pass1"])) < 6 ){
                    $GLOBALS["err"] = "A jelszónak legalább 6 karakternek kell lennie!";
                    $error=true;
                }
        }
        if(trim($_POST["pass1"]) != trim($_POST["pass2"]) ){
            $GLOBALS["err"] = "Az új jelszavak nem egyeznek";
            $error=true;
        }
        if(md5($_POST["opass"]) != trim($firm["JELSZO"])){
            $GLOBALS["err"] = "Helytelen megerősítő jelszó";
            $error=true;
        }


        if(!$error){
            $sql = "BEGIN UPDATE_CEG(:name, :email, :pass, :number); END;";



            $name = trim($_POST["name"]);
            $stmt = oci_parse($conn, $sql);
            global $pass;

            $email = trim($firm["C_EMAIL"]) ;
            if(strlen(trim($_POST["pass1"])) != 0 ){
                $pass = md5(trim($_POST["pass1"]));
            }else{
                $pass = trim($firm["JELSZO"]);
            }
            $number = trim( $_POST["number"]);


            oci_bind_by_name($stmt, ':name', $name);
            oci_bind_by_name($stmt, ':email', $email);
            oci_bind_by_name($stmt, ':pass', $pass);
            oci_bind_by_name($stmt, ':number', $number);


            $result = oci_execute($stmt);
            header("Location: index.php");
        }


    }