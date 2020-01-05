<?php
    session_start();
    if (isset($_SESSION["login"])){
        Header("Location:..");
    }

    $username="";
    $password="";
    $lname = "";
    $fname = "";
    $role = "";
    $userid = "";
    $error = false;
    $loginOK = false;

    if(isset($_POST["login"])){
        if(isset($_POST["username"])) $username=$_POST["username"];
        if(isset($_POST["password"])) $password=$_POST["password"];

        if(empty($username) || empty($password)) {
          $error=true;
        }

        if(!$error){
          //check username and password with the database record
            require_once("../db/db.php");
            $result = $mydb->query("CALL Login('$username');");
            $row=mysqli_fetch_array($result);
            if ($row){
                if (password_verify($password, $row["Password"])){
                    $loginOK=true;
                    $lname = $row["last"];
                    $fname = $row["first"];
                    $role = $row["role"];
                    $userid = $row["UserID"];
                } else {
                    $loginOK = false;
                }
            }

            if($loginOK) {
                $_SESSION["username"] = $username;
                $_SESSION["first"] = $fname;
                $_SESSION["last"] = $lname;
                $_SESSION["role"] = $role;
                $_SESSION["login"] = true;
                $_SESSION["userid"] = $userid;
                if ($role == 1){
                    Header("Location: ../");
                }else if ($role == 2){
                    Header("Location: ../dashboard");
                }
            }
        }
    }
?>
<html>
<head>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" type="text/css" href="../css/login.css">
    <link rel="shortcut icon" href="../img/favicon.jpg" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CaptureIt | Login</title>
</head>
<body>
    <div class="wrapper">
        <form class="form-signin" name="login" action="<?php echo $_SERVER['PHP_SELF']; ?>">       
          <h2 class="form-signin-heading"><a href="."><img src="../img/logo.png" style="width:300px; height:100px;"></a></h2>
          <?php
            if (isset($_SESSION["invalid-register"])){
                echo "<div class='alert alert-danger' role='alert'><strong>Invalid registration key.</strong> Please try again.</div>";
                unset($_SESSION["invalid-register"]);
            }else if (isset($_SESSION["register-success"])){
                echo "<div class='alert alert-success' role='alert'><strong>Registration successful!</strong> Please login :)</div>";
                unset($_SESSION["register-success"]);
            }
          ?>
          <input type="text" class="form-control" name="username" placeholder="Email Address" value="<?php
            if(!empty($username)){
                echo $username;
               }
            ?>"/>
            <?php
                if (empty($username) && $error){
                    echo "<span class='text-danger'>Please enter your email address</span>";
                }
            ?>
          <input type="password" class="form-control" name="password" placeholder="Password"/>
            <?php
                if (empty($password) && $error){
                    echo "<span class='text-danger'>Please enter your password</span><br>";
                }
            ?>
          <label class="checkbox">
            <input type="checkbox" value="remember-me" id="rememberMe" name="rememberMe"> Remember me
          </label>
          <button class="btn btn-lg btn-primary btn-block" type="submit" name="login" formmethod="post">Login</button> 
            <?php
                if(!$loginOK && isset($_POST["login"])){
                    echo "<span class='text-danger'>Invalid login credentials</span>";
                }
            ?>  
        </form>
        <a href="#" class="text-center new-account">Forgot Password?</a>
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