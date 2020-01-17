<?php
    session_start();
    if (isset($_SESSION["login"])){
        Header("Location:./");
    }

    $userid = "";
    $actid = "";

    if(isset($_SESSION["UserID"])) $userid = $_SESSION["UserID"];
    if(isset($_SESSION["ActivationID"])) $actid = $_SESSION["ActivationID"];

    require_once("db".DIRECTORY_SEPARATOR."db.php");
    //FIX TO USE PREPARED STATEMENTS
    $pass1="";
    $pass2="";
    $error = false;
    $registerOK = false;
    if (isset($_SESSION["valid-key"])){
        if ($_SESSION["key"] == $_GET["id"]){
            $pass1="";
            $pass2="";
            $error = false;
            $registerOK = false;

            if(isset($_POST["register"])){
                if(isset($_POST["pass1"])) $pass1=$_POST["pass1"];
                if(isset($_POST["pass2"])) $pass2=$_POST["pass2"];

                if($pass1 == "" || $pass2 == "") {
                  $error=true;
                }else if ($pass1 == $pass2){
                    $registerOK = true;
                }

                if(!$error && $registerOK){
                    $hashed_pass = password_hash($pass1, PASSWORD_DEFAULT);
                    $mydb->query("CALL ActivateAccount('$userid', '$hashed_pass', '$actid');");
                    session_destroy();
                    session_start();
                    $_SESSION["register-success"] = true;
                    Header("Location:login");
                }
            }       
        }else{
            $_SESSION["invalid-register"] = true;
            Header("Location:login");
        }
    }else{
        $result = $mydb->query("CALL GetActivationID('".$_GET["id"]."');");
        $row=mysqli_fetch_array($result);
        if ($row){
            $_SESSION["valid-key"] = true;
            $_SESSION["key"] = $_GET["id"];
            $_SESSION["UserID"] = $row["UserID"];
            $_SESSION["ActivationID"] = $row["ActivationID"];
        }else{
            $_SESSION["invalid-register"] = true;
            Header("Location:login");
        }
    }
?>
<html>
<head>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="shortcut icon" href="/img/favicon.jpg" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CaptureIt | Register</title>
</head>
<body>
    <div class="wrapper">
        <form class="form-signin" name="register" action="<?php echo $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']; ?>">       
          <h2 class="form-signin-heading"><a href="."><img src="img/logo.png" style="width:300px; height:100px;"></a></h2>
          <input type="password" class="form-control" name="pass1" placeholder="Password"/>
            <?php
                if (empty($pass1) && $error){
                    echo "<span class='text-danger'>Please enter your password</span><br>";
                }
            ?>
            <input type="password" class="form-control" name="pass2" placeholder="Re-Enter Password"/>
            <?php
                if (empty($pass2) && $error){
                    echo "<span class='text-danger'>Please enter your password</span><br>";
                }
            ?>
          <button class="btn btn-lg btn-primary btn-block" type="submit" name="register" formmethod="post">Register</button> 
            <?php
                if(!$registerOK && isset($_POST["register"])){
                    echo "<span class='text-danger'>Please make sure both passwords match</span>";
                }
            ?>  
        </form>
        <footer>
            <br><br>
            <div class="text-center text-muted">
                <p>
                    &copy;2018 BIT 4454 CaptureIt Team
                </p>
                <p>
                    Made with love for CarMax, Inc. in Blacksburg, VA
                </p>    
            </div>
        </footer>
    </div>
</body>
</html>