<!--
    WARNING! The code below is most definitely a steaming turd, but it works so I don't really care how bad it is :) 
-->
<?php
    session_start();
    if (!isset($_SESSION["login"])){
        Header("Location:login");
    }
    require_once("db".DIRECTORY_SEPARATOR."db.php");
    if (isset($_POST["update"])){
        $userid = $_SESSION["new-user-id"];
        $note = $_POST["notes"];
        $first = $_POST["fname"];
        $last = $_POST["lname"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $noteid = md5(uniqid(rand(), true));
        $mydb->query("CALL UpdateAccount('$userid', '$first', '$last', '$email', '$phone', '$noteid', '$note');");          

        unset($_SESSION["fname"]);
        unset($_SESSION["lname"]);
        unset($_SESSION["email"]);
        unset($_SESSION["phone"]);
        unset($_SESSION["new-user-id"]);
        $_SESSION["verify-data"] = true;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CaptureIt | Upload</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="img/favicon.jpg" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <style>
        .loader {
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 100px;
            height: 100px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .bd-example-modal-lg .modal-dialog{
            position: absolute;
            left: 50%;
            top: 50%;
        }
          
        .bd-example-modal-lg .modal-dialog .modal-content{
            background-color: transparent;
            border: none;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div id="loading" class="modal fade bd-example-modal-lg" data-backdrop="static" data-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content h-100 row align-items-center vertical-center-row" style="width: 48px">
                    <span class="loader"></span>
                </div>
            </div>
        </div>
        <form class="form-signin text-center" action="#">
            <h2 class="form-signin-heading"><a href="."><img src="img/logo.png" style="width:300px; height:100px;"></a></h2>
            <h4>Resume Upload</h4>
            <br><br>
            <?php
                if (isset($_SESSION["upload-success"]) && !isset($_POST["update"])){
                    if ($_SESSION["upload-success"] = true){
                        echo "
                            <div class='alert alert-info' role='alert' id='verify-banner'><strong>Success!</strong> Resume uploaded successfully.</div>
                        ";
                        if (isset($_SESSION["modal"])){
                            echo "
                                <script type='text/javascript'>
                                    $(document).ready(function() {
                                        $('#edit').modal('show'); 
                                    });
                                </script>
                            ";
                            unset($_SESSION["modal"]);
                        }
                    }else{
                        echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> An error occurred while uploading the resume.</div>";
                    }
                    unset($_SESSION["upload-success"]);
                }else if (isset($_SESSION["verify-data"])){
                    echo "
                        <div class='alert alert-success' role='alert' id='verify-banner'><strong>Success!</strong> Data verified & activation email sent.</div>
                    ";
                    unset($_SESSION["verify-data"]);
                }
            ?>
            <div class="form-group align-items-center">
                <div class="custom-file input-group" name="image">
                    <label class="custom-file-label text-left" for="customFile" id="file">Choose file</label>
                    <input type="file" class="custom-file-input" id="customFile" name="image" accept=".jpg, .jpeg, .png" required>
                </div>     
                <br>
                <br>
                <button class="btn btn-md btn-primary upload" type="submit">Upload</button>
            </div>
            <div id="progressbar" class="progress progress-striped active" style="display:none;">
                <div class="progress-bar" style="width:0%"></div>
            </div>        
        </form>
        <div class="modal fade" id="edit">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Verify Resume Data</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                    <div class="modal-body">
                        <form name="verify" id="verify" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-group row">
                                <label class="col-3 col-form-label">First Name</label>
                                <div class="col-9">
                                    <input class="form-control" type="text" value="<?php if(isset($_SESSION['fname'])){echo $_SESSION['fname'];}?>" name="fname" id="fname">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">Last Name</label>
                                <div class="col-9">
                                    <input class="form-control" type="text" value="<?php if(isset($_SESSION['lname'])){echo $_SESSION['lname'];}?>" name="lname" id="lname">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">Email Address</label>
                                <div class="col-9">
                                    <input class="form-control" type="email" value="<?php if(isset($_SESSION['email'])){echo $_SESSION['email'];}?>" name="email" id="email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">Phone Number</label>
                                <div class="col-9">
                                    <input class="form-control" type="text" step="1" value="<?php if(isset($_SESSION['phone'])){echo $_SESSION['phone'];}?>" name="phone" id="phone">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">Notes</label>
                                <div class="col-9">
                                    <textarea class="form-control" type="text" value="" name="notes" id="notes"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>-->
                                <button type="submit" class="btn btn-primary" name="update" id="update"  formmethod="post">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <a href="dashboard" class="text-center new-account">Manage Applicants</a>
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
    <script type="text/javascript"></script>
    <script>
        $(document).on('submit','form',function(e){
            var oForm = $(this);
            var formId = oForm.attr("id");
            if (!$(this).attr("id")){
                e.preventDefault();
                $("#progressbar").show();
                uploadImage(oForm);
            }
        });

        function uploadImage($form){
            $form.find('.progress-bar').removeClass('progress-bar-success')
                                        .removeClass('progress-bar-danger');
            var formdata = new FormData($form[0]);
            var request = new XMLHttpRequest();
            request.upload.addEventListener('progress',function(e){
                var percent = Math.round(e.loaded/e.total * 100);
                if (percent == 100){
                    $("#progressbar").hide();
                    $("#loading").modal("show");
                }
                $form.find('.progress-bar').width(percent+'%').html(percent+'%');
            });

            request.addEventListener('load',function(e){
                window.location.reload();
            });
            request.open('post', 'upload.php');
            request.send(formdata);
        }
    </script>
    <script type="text/javascript">
        $('#customFile').change(function() {
          var i = $(this).prev('label').clone();
          var file = $('#customFile')[0].files[0].name;
          $(this).prev('label').text(file);
        });
    </script>
</body>
</html>