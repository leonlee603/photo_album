<?php
require_once "connMysql.php";
session_start();
// Check if the admin has logged in yet, if not, direct the user to login page
if ((!isset($_SESSION['loginMember'])) || ($_SESSION['loginMember'] === "")) {
    header("Location: login.php");
}
// Direct the user to index.php when they logout and unset the $_SESSION['loginMember']
if ((isset($_GET['logout'])) && ($_GET['logout'] === "true")) {
    unset($_SESSION['loginMember']);
    header("Location: index.php");
}
// Create album
$err_list = [];
if ((isset($_POST['action'])) && ($_POST['action'] === "create")) {
    // Album_title's value
    if (empty($_POST["album_title"])) {
        $err_album_title = '<span class="text-danger">&nbsp;title is required';
        $err_list[] = $err_album_title;
    } else {
        $album_title = trim($_POST["album_title"]);
        $album_title = filter_var($album_title, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    // Location's value
    if (empty($_POST["album_location"])) {
        $album_location = "";
    } else {
        $album_location = trim($_POST["album_location"]);
        $album_location = filter_var($album_location, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    // Date time's value
    if (empty($_POST["album_date"])) {
        $album_date = date("Y-m-d H:i:s");
    } else {
        $album_date = trim($_POST["album_date"]);
        $album_date = filter_var($album_date, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    // album description value
    if (empty($_POST["album_desc"])) {
        $album_desc = "";
    } else {
        $album_desc = trim($_POST["album_desc"]);
        $album_desc = filter_var($album_desc, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    if (empty($err_list)) {
        $create_album_query = "INSERT INTO album (album_title, album_date, album_location, album_desc) VALUES (?, ?, ?, ?)";
        $create_album_stmt = $db_link->prepare($create_album_query);
        $create_album_stmt->bind_param("ssss", $album_title, $album_date, $album_location, $album_desc);
        $create_album_stmt->execute();
        $album_id = $create_album_stmt->insert_id;
        $create_album_stmt->close();

        for ($i=0; $i<count($_FILES["ap_picurl"]["name"]); $i++) {
            if ($_FILES['ap_picurl']['tmp_name'][$i] !== "") {
                if (empty($_POST["ap_subject"][$i])) {
                    $ap_subject = "";
                } else {
                    $ap_subject = filter_var($_POST["ap_subject"][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                }
                $ap_picurl = filter_var($_FILES["ap_picurl"]["name"][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $upload_photo_query = "INSERT INTO albumphoto (album_id, ap_subject, ap_date, ap_picurl) VALUES (?, ?, NOW(), ?)";
                $upload_photo_stmt = $db_link->prepare($upload_photo_query);
                $upload_photo_stmt->bind_param("iss", $album_id, $ap_subject, $ap_picurl);
                $upload_photo_stmt->execute();
                if(!move_uploaded_file($_FILES['ap_picurl']['tmp_name'][$i] , "photos/" . $_FILES['ap_picurl']['name'][$i])) die("upload failed");
                $upload_photo_stmt->close();
            }
        }
        // header("Location: admin.php");
        header("Location: adminEdit.php?id=$album_id&create=success");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style_admin.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poiret+One&family=Nanum+Gothic&display=swap" rel="stylesheet"> <!-- logo -->
    <title>Photobook | admin</title>
</head>
<body class="bg-dark flex-wrapper">
    <!-- ********** navbar ********** -->
    <nav class="navbar navbar-expand-md bg-secondary navbar-light sticky-top">
        <!-- Brand -->
        <a class="navbar-brand" href="admin.php">PhotoBook</a>
        <!-- Toggler/collapsible Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Navbar links -->
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="admin.php">ALBUM</a>
            </li>
            </ul>
            <ul class="navbar-nav">
            <li class="nav-item float-right">
                <a class="nav-link" href="?logout=true">LOGOUT</a>
            </li>
            </ul>
        </div>
    </nav> <!-- ********** End of navbar ********** -->
    
    <!-- ********** Logo ********** -->
    <div class="container">
        <div class="row">
            <div class="col-12 text-center my-3 text-white">
                <h3><span class="logo-f">Photo</span> <span class="logo-l">Book</span></h3>
                <hr>
            </div>
        </div>
    </div> <!-- ********** End of Logo ********** -->

    <!-- ********** Create album ********** -->
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-9 mx-auto">
                <div class="card create-album">
                    <div class="card-header text-center pb-2 bg-white">
                        <p class="mb-1 display-4 text-info"><i class="fas fa-folder-plus"></i></p>
                        <h3 class="create mb-0">Create Album</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-12 col-md-5">
                                    <div class="form-group">
                                        <label for="album_title">Album title *</label><?php if ($err_album_title !== "") echo $err_album_title; ?>
                                        <input type="text" class="form-control" id="album_title" name="album_title">
                                    </div>
                                    <div class="form-group">
                                        <label for="album_location">Location</label>
                                        <input type="text" class="form-control" id="album_location" name="album_location" value="<?php if($album_location!=="") echo $album_location;?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="album_date">Date & time</label>
                                        <input type="text" class="form-control" id="album_date" name="album_date" value="<?php echo date("Y-m-d H:i:s");?>">
                                    </div>
                                </div>
                                <div class="col-12 col-md-7">
                                    <div class="form-group">
                                        <label for="album_desc">Album description</label>
                                        <textarea type="text" class="form-control" id="album_desc" name="album_desc" rows="4"><?php if ($album_desc!=="") echo $album_desc;?></textarea>
                                    </div>
                                    <p class="mb-0">* is required</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-1">
                                <div class="col-12 col-md-5">
                                    <input class="btn btn-sm" type="file" name="ap_picurl[]" id="ap_picurl[]" accept="image/*"/>
                                </div>
                                <div class="col-12 col-md-7">
                                    <input type="text" class="form-control form-control-sm align-middle" name="ap_subject[]" id="ap_subject[]" placeholder="Pic 1 : Description"/>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-12 col-md-5">
                                    <input class="btn btn-sm " type="file" name="ap_picurl[]" id="ap_picurl[]" accept="image/*"/>
                                </div>
                                <div class="col-12 col-md-7">
                                    <input type="text" class="form-control form-control-sm align-middle" name="ap_subject[]" id="ap_subject[]" placeholder="Pic 2 : Description"/>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-12 col-md-5">
                                    <input class="btn btn-sm" type="file" name="ap_picurl[]" id="ap_picurl[]" accept="image/*"/>
                                </div>
                                <div class="col-12 col-md-7">
                                    <input type="text" class="form-control form-control-sm align-middle" name="ap_subject[]" id="ap_subject[]" placeholder="Pic 3 : Description"/>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-12 col-md-5">
                                    <input class="btn btn-sm " type="file" name="ap_picurl[]" id="ap_picurl[]" accept="image/*"/>
                                </div>
                                <div class="col-12 col-md-7">
                                    <input type="text" class="form-control form-control-sm align-middle" name="ap_subject[]" id="ap_subject[]" placeholder="Pic 4 : Description"/>
                                </div>
                            </div><div class="row mb-1">
                                <div class="col-12 col-md-5">
                                    <input class="btn btn-sm" type="file" name="ap_picurl[]" id="ap_picurl[]" accept="image/*"/>
                                </div>
                                <div class="col-12 col-md-7">
                                    <input type="text" class="form-control form-control-sm align-middle" name="ap_subject[]" id="ap_subject[]" placeholder="Pic 5 : Description"/>
                                </div>
                            </div>
                            <hr>
                            <input type="hidden" name="action" value="create">
                            <input class="btn btn-primary" type="submit" name="button" value="Create">
                        </form>
                    </div>
                    <div class="card-footer bg-white"></div>
                </div>
            </div>
        </div>
    </div> <!-- ********** End of Create album ********** -->
    
    <!-- ********** Footer ********** -->
    <footer class="bg-secondary mt-5 text-black">
        <div class="container py-4">
            <div class="row">
                <div class="col-12 text-center">
                    <?php echo "Copyright&nbsp;&copy;&nbsp;" . date("Y") . " All rights reserved | Leon Lee"; ?>
                </div>
            </div>
        </div>
    </footer> <!-- ********** End of Footer ********** -->
    
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>
</html>
<?php
$db_link->close();
?>