<?php
    session_start();
    $manager = false;
    if (!isset($_SESSION["login"])){
        Header("Location:../login");
    }else{
      if ($_SESSION["role"] == 1){
        $manager = true;
      }
    }
?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../img/favicon.jpg">

        <title>CaptureIt | Dashboard</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">

        <!-- Custom styles for this template -->
        <link href="../css/offcanvas.css" rel="stylesheet">
        <link href="../css/sb-admin.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../css/settings.css">
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.print.css" media="print">
    </head>

    <body class="bg-light">

        <nav class="navbar navbar-expand-md navbar-collapse fixed-top navbar-dark" style="background: #00529F;">
            <a class="navbar-brand" href=".">CaptureIt</a>
            <button class="navbar-toggler p-0 border-0" type="button" data-toggle="offcanvas">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-collapse offcanvas-collapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="" id="dash">Dashboard <span class="sr-only">(current)</span></a>
                    </li>
                    <?php 
                      if ($manager){
                        echo "
                        <li class='nav-item'>
                          <a class='nav-link' data-toggle='collapse' data-target='.navbar-collapse' href='' id='applicants'>Applicants</a>
                        </li>
                        <li class='nav-item'>
                          <a class='nav-link' data-toggle='collapse' data-target='.navbar-collapse' href='' id='interview'>Scheduled Interviews</a>
                        </li>
                        <li class='nav-item'>
                          <a class='nav-link' data-toggle='collapse' data-target='.navbar-collapse' href='' id='analysis'>Analysis</a>
                        </li>
                        ";
                      }else{
                        echo "
                        <li class='nav-item'>
                          <a class='nav-link' data-toggle='collapse' data-target='.navbar-collapse' href='' id='profile'>Profile</a>
                        </li>
                        <li class='nav-item'>
                          <a class='nav-link' data-toggle='collapse' data-target='.navbar-collapse' href='' id='app'>Application</a>
                        </li>
                        ";
                      }
                    ?>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item navbar-right">
                        <form action="../logout.php">
                            <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="nav-scroller bg-white box-shadow">
            <nav class="nav nav-underline">
                <a class="nav-link active" href="" id="dash-sub">Dashboard</a>
                <a class="nav-link" href="" id="edit-profile">Edit Profile</a>
                <?php
                  if (!$manager){
                    echo "<a class='nav-link' href='' id='manage-app'>Manage Application</a>"; 
                  }else{
                     echo "
                     <a class='nav-link nav-sub' href='' id='bubble-nav' style='display: none;'>Bubble Chart</a>
                     <a class='nav-link nav-sub' href='' id='liveschool-nav' style='display: none;'>School Count</a>
                     <a class='nav-link nav-sub' href='' id='gpabyschool-nav' style='display: none;'>GPA by Major</a>
                     <a class='nav-link nav-sub' href='' id='wgpabyschool-nav' style='display: none;'>Weighted GPA by Major</a>
                     <a class='nav-link nav-sub' href='' id='individual-nav' style='display: none;'>Individual Overview Chart</a>
                     "; 
                  }
                ?>
            </nav>
        </div>
        <?php
          require_once("../db/db.php");
          $result = $mydb->query("SELECT a.FirstName, a.LastName, a.Role, roles.RoleTitle, a.EmailAddress, a.PhoneNumber, ad.Street, ad.City, ad.State, ad.Zip FROM account a LEFT JOIN address ad ON a.UserID=ad.UserID LEFT JOIN roles ON roles.RoleID = a.Role WHERE a.UserID='".$_SESSION["userid"]."';");
          $row = mysqli_fetch_array($result);
          $last = $row["LastName"];
          $first = $row["FirstName"];
          $email = $row["EmailAddress"];
          $role = $row["RoleTitle"];
          $address = $row["Street"];
          $phone = $row["PhoneNumber"];
          $city = $row["City"];
          $state = $row["State"];
          $zip = $row["Zip"];
        ?>
            <main role="main" class="container">
                <div id="dash-content" class="active-content" style="">
                    <div class="text-center">
                        <?php
                          if (isset($_SESSION["contact-update"])){
                            echo "<div class='alert alert-success alert-dismissible' role='alert' id='verify-banner'>
                              <strong>Success!</strong> Contact information updated!
                              <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                              </button>
                            </div>";
                            unset($_SESSION["contact-update"]);
                          }else if (isset($_SESSION["password-update"])){
                            if ($_SESSION["password-update"]){
                              echo "<div class='alert alert-success alert-dismissible' role='alert' id='verify-banner'>
                              <strong>Success!</strong> Password updated!
                              <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                              </button>
                              </div>";
                            }else{
                              echo "<div class='alert alert-danger alert-dismissible' role='alert' id='verify-banner'>
                              <strong>Error!</strong> Please make sure both password fields are not blank and contain the same value!
                              <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                              </button>
                              </div>";
                            }      
                            unset($_SESSION["password-update"]);
                          }else if (isset($_SESSION["profile-update"])){
                             echo "<div class='alert alert-success alert-dismissible' role='alert' id='verify-banner'>
                              <strong>Success!</strong> Profile information updated!
                              <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                              </button>
                            </div>";
                            unset($_SESSION["profile-update"]);
                          }
                        ?>
                        <br>
                        <h1>Welcome, <?php echo $first;?>!</h1>
                        <br>
                        <p>
                          <?php
                            require_once("../db/db.php");
                            if (!$manager){
                              $result = $mydb->query("SELECT sc.StatusTitle, DATE_FORMAT(s.LastDate, '%M %d, %Y') AS `LastDate`, TIME_FORMAT(s.LastTime, '%r') AS `LastTime` FROM status_code sc, applicant_status s WHERE s.UserID='".$_SESSION["userid"]."' AND s.Status=sc.StatusCode;");
                              $row = mysqli_fetch_array($result);
                              $status = $row["StatusTitle"];
                              $date = $row["LastDate"];
                              $time = $row["LastTime"];
                            }else{
                              $result = $mydb->query("SELECT COUNT(a.UserID) AS count FROM account a WHERE a.Role=2");
                              $row = mysqli_fetch_array($result);
                              $appcount = $row["count"];

                              $result = $mydb->query("SELECT COUNT(i.InterviewID) AS count FROM interviews i WHERE i.Date >= CURDATE()");
                              $row = mysqli_fetch_array($result);
                              $intcount = $row["count"];
                            }

                          ?>
                          <?php
                            if (!$manager){
                              echo "
                                <h4>Application Status</h4>
                                <span class='text-info'>$status<br></span>
                                <span class='text-info'>Updated $date".' at '."$time</span>
                              ";
                            }else{
                              echo "
                                <h4>Quick Stats</h4>
                                <span class='text-info'>$appcount".' applicants in pool'."<br></span>
                                <span class='text-info'>$intcount".' scheduled interviews'."<br></span>
                                ";
                            }
                          ?>
                        </p>
                    </div>
                </div>
                <div id="prof-content" style="display:none;">
                    <div class="ui-67">
                        <div class="ui-head">
                            <div class="ui-details">
                                <h3 id="name-header"><?php echo $first." ".$last;?></h3>
                                <h4><?php echo $role?>, CarMax</h4>
                            </div>
                            <div class="ui-image">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png" alt="Profile Picture" class="img-responsive" width="100" height="100">
                            </div>
                        </div>
                        <div class="ui-content">
                            <div class="row">
                                <div class="col-sm-8 col-md-8 col-lg-8 col-lg-offset-2 acc-col bg-light">
                                    <section>
                                        <h3>Contact Information</h3>
                                        <form class="ng-pristine ng-valid" action="update.php">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label class="control-label">Email:</label>
                                                    <input type="email" class="form-control" id="email" name="email" value=<?php echo "'$email'";?>>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="control-label">Phone Number:</label>
                                                    <input type="text" class="form-control" id="phone" name="phone" value=<?php echo "'$phone'";?>>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label class="control-label">Address:</label>
                                                    <input type="text" class="form-control" id="address" name="address" value=<?php echo "'$address'";?>>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="control-label">City:</label>
                                                    <input type="text" class="form-control" id="city" name="city" value=<?php echo "'$city'";?>>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label class="control-label">State/Province:</label>
                                                    <input type="text" class="form-control" id="state" name="state" value=<?php echo "'$state'";?>>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label class="control-label">Zip Code:</label>
                                                    <input type="number" class="form-control" id="zip" name="zip" value=<?php echo "'$zip'";?>>
                                                </div>
                                            </div>
                                            <div class="col-sm-212">
                                                <div class="btn-div">
                                                    <button type="submit" formmethod="post" name="updateInfo" class="btn btn-primary pull-left">Update</button>
                                                </div>
                                            </div>
                                            <hr>
                                        </form>
                                    </section>

                                    <section>
                                        <h3>Change Password</h3>
                                        <form role="form" action="update.php">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label class="control-label">Password:</label>
                                                    <div>
                                                        <input type="password" class="form-control" id="pass1" name="pass1">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <label class="control-label">Confirm:</label>
                                                    <div>
                                                        <input type="password" class="form-control" id="pass2" name="pass2">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="btn-div">
                                                        <button type="submit" formmethod="post" name="updatePass" class="btn btn-primary pull-right">Update</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="app-content" style="display:none;">
                    <div class="ui-67">
                        <div class="ui-head">
                            <div class="ui-details">
                                <h3 id="name-header"><?php echo $first." ".$last;?></h3>
                                <h4><?php echo $role?>, CarMax</h4>
                            </div>
                            <div class="ui-image">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png" alt="Profile Picture" class="img-responsive" width="100" height="100">
                            </div>
                        </div>
                        <div class="ui-content">
                            <div class="row">
                                <div class="col-sm-8 col-md-8 col-lg-8 col-lg-offset-2 acc-col bg-light">
                                    <form class="ng-pristine ng-valid" action="update.php">
                                        <section>
                                            <h3>School Information</h3>
                                            <?php
                                              $result = $mydb->query("SELECT * FROM applicant_profile ap WHERE ap.ProfileID=(SELECT a.ProfileID FROM account a WHERE a.UserID='".$_SESSION["userid"]."');");
                                              $data = mysqli_fetch_array($result);
                                              if ($data){
                                                $school = $data["SchoolID"];
                                                $degree = $data["DegreeID"];
                                                $major = $data["Major"];
                                                $minor = $data["Minor"];
                                                $gdate = $data["AntGrad"];
                                                $currs = $data["Extracurriculars"];
                                                $ogpa = $data["GPAOverall"];
                                                $igpa = $data["GPAInMajor"];
                                                $emptype = $data["EmpType"];
                                                $citizen = $data["Citizenship"];
                                              }
                                            ?>
                                            <div class="row">
                                              <div class="col-sm-6">
                                                  <label class="control-label">School Name:</label>
                                                    <select class="custom-select" name="school">
                                                      <?php
                                                        $result = $mydb->query("SELECT * FROM schools;");
                                                        while($row = mysqli_fetch_array($result)){
                                                          if ($data && $row["SchoolID"] == $school){
                                                            echo "<option selected value='".$row["SchoolID"]."'>".$row["SchoolName"]."</option>";
                                                          }else{
                                                            echo "<option value='".$row["SchoolID"]."'>".$row["SchoolName"]."</option>";
                                                          }
                                                        }
                                                      ?>
                                                    </select>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label class="control-label">Expected Gradutation:</label>
                                                        <input type="date" class="form-control" id="gdate" name="gdate" value="<?php if ($data) echo $gdate ?>">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label class="control-label">Degree Type:</label>
                                                        <select class="custom-select" name="degtype">
                                                            <?php
                                                              $result = $mydb->query("SELECT * FROM degree_type;");
                                                              while($row = mysqli_fetch_array($result)){
                                                                if ($data && $row["DegreeID"] == $degree){
                                                                  echo "<option selected value='".$row["DegreeID"]."'>".$row["DegreeName"]."</option>";
                                                                }else{
                                                                  echo "<option value='".$row["DegreeID"]."'>".$row["DegreeName"]."</option>";
                                                                }
                                                              }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <label class="control-label">Extracurricular Activities:</label>
                                                        <textarea class="form-control" type="text" name="extra" id="extra">
                                                            <?php if ($data) echo $currs ?>
                                                        </textarea>
                                                    </div>
                                                </div>
                                        </section>
                                        <br>
                                        <hr>
                                        <section>
                                            <h3>Degree Information</h3>
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <label class="control-label">Overall GPA:</label>
                                                    <input type="number" step="0.01" class="form-control" id="o-gpa" name="o-gpa" value="<?php if ($data) echo $ogpa ?>">
                                                </div>
                                                <div class="col-sm-2">
                                                    <label class="control-label">In-Major GPA:</label>
                                                    <input type="number" step="0.01" class="form-control" id="i-gpa" name="i-gpa" value="<?php if ($data) echo $igpa ?>">
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label class="control-label">Primary Major:</label>
                                                    <select class="custom-select" name="major">
                                                        <?php
                                                          $result = $mydb->query("SELECT * FROM majors;");
                                                          while($row = mysqli_fetch_array($result)){
                                                            if ($data && $row["MajorID"] == $major){
                                                              echo "<option selected value='".$row["MajorID"]."'>".$row["MajorName"]."</option>";
                                                            }else{
                                                              echo "<option value='".$row["MajorID"]."'>".$row["MajorName"]."</option>";
                                                            }
                                                          }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="control-label">Minor:</label>
                                                    <select class="custom-select" name="minor">
                                                        <?php
                                                          $result = $mydb->query("SELECT * FROM majors;");
                                                          while($row = mysqli_fetch_array($result)){
                                                            if ($data && $row["MajorID"] == $minor){
                                                              echo "<option selected value='".$row["MajorID"]."'>".$row["MajorName"]."</option>";
                                                            }else{
                                                              echo "<option value='".$row["MajorID"]."'>".$row["MajorName"]."</option>";
                                                            }
                                                          }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </section>
                                        <section>
                                            <hr>
                                            <h3>Employment Information</h3>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label class="control-label">Desired Employment Type:</label>
                                                    <select class="custom-select" name="emp-type">
                                                        <?php
                                                          $result = $mydb->query("SELECT * FROM emp_type;");
                                                          while($row = mysqli_fetch_array($result)){
                                                            if ($data && $row["TypeID"] == $emptype){
                                                              echo "<option selected value='".$row["TypeID"]."'>".$row["TypeName"]."</option>";
                                                            }else{
                                                              echo "<option value='".$row["TypeID"]."'>".$row["TypeName"]."</option>";
                                                            }

                                                          }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="control-label">Citizenship Status:</label>
                                                    <select class="custom-select" name="citizenship">
                                                        <?php
                                                          $result = $mydb->query("SELECT * FROM citizenship_type;");
                                                          while($row = mysqli_fetch_array($result)){
                                                            if ($data && $row["TypeID"] == $citizen){
                                                              echo "<option selected value='".$row["TypeID"]."'>".$row["TypeName"]."</option>";
                                                            }else{
                                                              echo "<option value='".$row["TypeID"]."'>".$row["TypeName"]."</option>";
                                                            }
                                                          }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </section>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="btn-div text-center">
                                <button type="submit" formmethod="post" name="appdata" class="btn btn-primary pull-right">Save Changes</button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
                <div id="applicants-content" style="display:none;">
                  <div class="text-center">
                    <br>
                    <h1>Current Applicants</h1>
                    <hr>
                  </div>
                  <table id="table" class="table row-border">
                    <thead>
                      <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Major</th>
                        <th>Degree</th>
                        <th>Date Applied</th>
                        <th>Resume Capture</th>
                        <th>Status</th>
                      </tr>
                    </thead>  
                    <tbody>
                      <?php
                        if ($manager){
                          $result = $mydb->query("CALL GetApplicants();");
                          while($row = mysqli_fetch_array($result)){
                            echo "<tr id='".$row["UserID"]."'>
                            <td>".$row["FirstName"]."</td>
                            <td>".$row["LastName"]."</td>
                            <td>".$row["MajorName"]."</td>
                            <td>".$row["DegreeName"]."</td>
                            <td>".$row["UploadDate"]."</td>
                            <td><a class='capture' onclick='return false' href='../uploads/".$row["FileID"].".png'>View Image</a></td>
                            <td>".$row["StatusTitle"]."</td>
                            </tr>";
                          }
                        }
                      ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Major</th>
                        <th>Degree</th>
                        <th>Date Applied</th>
                        <th>Resume Capture</th>
                        <th>Status</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <div id="analysis-content" style="display:none;">
                  <?php
                    if($manager){
                      echo "
                      <div id='bubble' style='display: none;'>
                        <iframe src='https://public.tableau.com/views/BubbleChartLive/Dashboard1?:showVizHome=no&:embed=true' style='height:580px; width:100%;' frameborder='0'></iframe>
                      </div>
                      <div id='live-school' style='display: none;'>
                        <iframe src='https://public.tableau.com/views/LiveSchoolCount/Dashboard1?:showVizHome=no&:embed=true' style='height:580px; width:100%;' frameborder='0'></iframe>
                      </div>
                      <div id='gpa-by-major-by-school' style='display: none;'>
                        <iframe src='https://public.tableau.com/views/Gpabymajorbyschool/Dashboard1?:showVizHome=no&:embed=true' style='height:580px; width:2000px;' frameborder='0'></iframe>
                      </div>
                      <div id='weighted-gpa-by-school' style='display: none;'>
                        <iframe src='https://public.tableau.com/views/WeightedGPAbyschool/Dashboard1?:showVizHome=no&:embed=true' style='height:580px; width:100%;' frameborder='0'></iframe>
                      </div>
                      <div id='individual-overview' style='display: none;'>
                        <iframe src='https://public.tableau.com/views/Individualoverview/Dashboard2?:showVizHome=no&:embed=true' style='height:580px; width:100%;' frameborder='0'></iframe>
                      </div>
                      ";
                    }
                  ?>
                </div>
                <div id="interview-content" style="display:none;">
                  <br>
                  <h1 class="text-center">Interview Schedule</h1>
                  <hr>
                  <div id="calendar"></div>
                </div>
            </main>
            <div class="modal fade" id="capture-modal">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <img id="cap" src="" class="img-responsive center-block">
                </div>
              </div>
            </div>
            <div class="modal fade" tabindex="-1" id="view-modal" role="dialog">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h3 class="modal-title" id="view-title"></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form id="view-form">
                      <div class='' role='alert' id='view-success'>
                      </div>
                      <div class="form-group row">
                          <label class="col-3 col-form-label">Email Address</label>
                          <div class="col-6">
                            <input class="form-control" type="text" value="" name="view-email" id="view-email" disabled>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-3 col-form-label">Phone Number</label>
                          <div class="col-6">
                            <input class="form-control" type="text" value="" name="view-phone" id="view-phone" disabled>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-3 col-form-label">Recruiter Comments</label>
                          <div class="col-6">
                            <textarea class="form-control" type="text" value="" name="view-comments" id="view-comments" disabled>
                            </textarea>
                          </div>
                      </div>
                      <hr>
                      <h5>School Information</h5>
                      <br>
                      <div class="form-group row">
                          <label class="col-3 col-form-label">School</label>
                          <div class="col-9">
                            <input class="form-control" type="text" value="" name="view-school" id="view-school" disabled>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-3 col-form-label">GPA (Overall)</label>
                          <div class="col-3">
                            <input class="form-control" type="text" value="" name="view-gpa-o" id="view-gpa-o" disabled>
                          </div>
                          <label class="col-3 col-form-label">GPA (In-Major)</label>
                          <div class="col-3">
                            <input class="form-control" type="text" value="" name="view-gpa-i" id="view-gpa-i" disabled>
                          </div>
                      </div>
                      <hr>
                      <h5>Schedule Interview</h5>
                      <br>
                      <div class="form-group row">
                          <label class="col-3 col-form-label">Status</label>
                          <div class="col-6">
                            <select class="custom-select" name="view-status" id="view-status">
                              <option value="1">In Review</option>
                              <option value="2">Interview Offered</option>
                              <option value="3">Declined</option>
                              <option value="4">Job Offered</option>
                            </select>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-3 col-form-label">Interview Date</label>
                          <div class="col-4">
                            <input type="date" class="form-control" id="int-date" name="int-date"> 
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-3 col-form-label">Interview Start</label>
                          <div class="col-4">
                            <input type="time" class="form-control" id="int-start" name="int-start"> 
                          </div>
                          <label class="col-3 col-form-label"><i>(HH:MM AM/PM)</i></label>
                      </div>
                      <div class="form-group row">
                          <label class="col-3 col-form-label">Interview End</label>
                          <div class="col-4">
                            <input type="time" class="form-control" id="int-end" name="int-end"> 
                          </div>
                          <label class="col-3 col-form-label"><i>(HH:MM AM/PM)</i></label>
                      </div>
                      <div class="form-group row">
                          <label class="col-3 col-form-label">Interview Round</label>
                          <div class="col-2">
                            <select class="custom-select" name="int-round" id="int-round">
                              <option value="1">1</option>
                              <option value="2">2</option>
                              <option value="3">3</option>
                            </select>
                          </div>
                      </div>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="view-close">Close</button>
                    <button type="button" class="btn btn-primary" id="view-submit">Save changes</button>
                  </div>
                </div>
              </div>
            </div>

            <footer class="footer">
                <br>
                <br>
                <div class="text-center text-muted">
                    <p>
                        &copy;2018 BIT 4454 CaptureIt Team
                    </p>
                    <p>
                        Made with love for CarMax, Inc. in Blacksburg, VA
                    </p>
                </div>
            </footer>
            <script src="http://code.jquery.com/jquery-3.3.1.min.js" ntegrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
            <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
            <script src="../js/moment.js"></script>
            <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
            <script src="../js/holder.min.js"></script>
            <script src="../js/offcanvas.js"></script>
            <script src="../js/navigation.js"></script>
            <script src="../js/tabledata.js"></script>
            <script src="../js/calendar.js"></script>
            <script type="text/javascript">
              $("#table").DataTable();
            </script>
            <script type="text/javascript">
            $(".capture").click(function(){
              $("#cap").attr("src", $(this).attr("href"))
              $("#capture-modal").modal("show");
            });
            </script>
            <script type="text/javascript">
              $(".nav-item").click(function(){
                $(".open").collapse("hide");
              });
            </script>
    </body>
    </html>