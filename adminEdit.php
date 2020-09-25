<?php
require_once "connMysql.php";
session_start();
// Check if the admin has logged in yet, if not, direct the user to login page
if ((!isset($_SESSION['loginMember'])) || ($_SESSION['loginMember'] === "")) {
    header("Location: index.php");
}
// Direct the user to index.php when they logout and unset the $_SESSION['loginMember']
if ((isset($_GET['logout'])) && ($_GET['logout'] === "true")) {
    unset($_SESSION['loginMember']);
    header("Location: index.php");
}
// Display the success message after album is created
if ((isset($_GET['create'])) && ($_GET['create'] === "success")) {
    $create_msg = '<span class="text-success">Album successfully created!</span>';
}
// Display the success message after album is updated
if ((isset($_GET['update'])) && ($_GET['update'] === "success")) {
    $update_msg = '<span class="text-success">Album successfully updated!</span>';
}
// Update album
$err_list = [];
if ((isset($_POST['action'])) && ($_POST['action'] === "update")) {
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
    // Update photo's information
    for ($i=0; $i<count($_POST['ap_id']); $i++) {
        $update_photo_query = "UPDATE albumphoto SET ap_subject=? WHERE ap_id=?";
        $update_photo_stmt = $db_link->prepare($update_photo_query);
        $update_photo_stmt->bind_param("si", $_POST['update_subject'][$i], $_POST['ap_id'][$i]);
        $update_photo_stmt->execute();
        $update_photo_stmt->close();
    }
    $album_id = $_POST['album_id'];
    if (empty($err_list)) {
        // Update album's information
        $update_album_query = "UPDATE album SET album_title=?, album_date=?, album_location=?, album_desc=? WHERE album_id=?";
        $update_album_stmt = $db_link->prepare($update_album_query);
        $update_album_stmt->bind_param("ssssi", $album_title, $album_date, $album_location, $album_desc, $album_id);
        $update_album_stmt->execute();
        $update_album_stmt->close();
        // Delete photo from folder <photos>
        for ($i=0; $i<count($_POST['del_check']); $i++) {
            $delete_id = $_POST['del_check'][$i]; // Set the check_id to $delete_id, which is the index position of the array item
            $delete_query = "DELETE FROM albumphoto WHERE ap_id = {$_POST['ap_id'][$delete_id]}"; // $_POST['ap_id'] is equal to $row_photo['ap_id']
            $db_link->query($delete_query);
            unlink("photos/" . $_POST['del_file'][$delete_id]);
        }
        // Upload new photo
        for ($i=0; $i<count($_FILES["ap_picurl"]["name"]); $i++) {
            if ($_FILES['ap_picurl']['tmp_name'][$i] !== "") {
                if (empty($_POST["ap_subject"][$i])) {
                    $ap_subject[$i] = "";
                } else {
                    $ap_subject[$i] = filter_var($_POST["ap_subject"][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
        header("Location: ?id=" . $_POST["album_id"] . "&update=success");
    }
}
// Sanitize the $_GET['id'] value, sanitize id (sid) equal to album id
$sid = 0;
if ((isset($_GET['id'])) && ($_GET['id'] !== "")) {
    $sid = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
}
// Prepare information for showing album and photos' information
$album_query = "SELECT * FROM album WHERE album_id=$sid";
$album = $db_link->query($album_query);
$photo_query = "SELECT * FROM albumphoto WHERE album_id=$sid";
$photo = $db_link->query($photo_query);
$total_records = $photo->num_rows;
$row_album = $album->fetch_array(MYSQLI_ASSOC);
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

    <!-- ********** Update album ********** -->
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-9 mx-auto">
                <div class="card create-album">
                    <div class="card-header text-center pb-2 bg-white">
                        <div class="row">
                            <a class="mx-2 back-button text-secondary" href="admin.php"><i class="fas fa-arrow-left"></i> back to all albums</a>
                        </div>
                        <p class="mb-1 display-4 text-warning"><i class="fas fa-edit"></i></p>
                        <h3 class="create mb-2">Edit Album</h3>
                        <?php if ($create_msg !== "") echo $create_msg; ?>
                        <?php if ($update_msg !== "") echo $update_msg; ?>
                        <p class="mb-0"><?php echo $total_records;?>&nbsp;<i class="far fa-images"></i></p>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>?id=<?php echo $row_album['album_id']; ?>" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-12 col-md-5">
                                    <div class="form-group">
                                        <label for="album_title">Album title *</label><?php if ($err_album_title !== "") echo $err_album_title; ?>
                                        <input type="text" class="form-control" id="album_title" name="album_title" value="<?php echo $row_album["album_title"]; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="album_location">Location</label>
                                        <input type="text" class="form-control" id="album_location" name="album_location" value="<?php if (isset($album_location)) {echo $album_location;} else { echo $row_album['album_location']; } ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="album_date">Date & time</label>
                                        <input type="text" class="form-control" id="album_date" name="album_date" value="<?php if (isset($album_date)) {echo $album_date;} else { echo $row_album['album_date']; } ?>">
                                    </div>
                                </div>
                                <div class="col-12 col-md-7">
                                    <div class="form-group">
                                        <label for="album_desc">Album description</label>
                                        <textarea type="text" class="form-control" id="album_desc" name="album_desc" rows="4"><?php if (isset($album_desc)) {echo $album_desc;} else { echo $row_album["album_desc"]; } ?></textarea>
                                    </div>
                                    <p class="mb-0">* is required</p>
                                </div>
                            </div>
                            <div class="row">
                            <?php
                            $check_id = 0;
                            while ($row_photo = $photo->fetch_array(MYSQLI_ASSOC)) {
                            ?>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 my-3">
                                    <div class="card rounded-0 mb-2">
                                        <div class="image">
                                            <img class="card-image-top img-fluid" src="photos/<?php echo $row_photo["ap_picurl"]; ?>" alt="<?php echo $row_photo["ap_subject"]; ?>">
                                        </div>
                                    </div>
                                    <a href="adminComment.php?id=<?php echo $row_photo['ap_id'];?>">Delete Comment(<?php echo $row_photo['ap_comment'];?>)</a>
                                    <input class="form-control form-control-sm mb-0" type="text" name="update_subject[]" value="<?php if (isset($ap_subject)) { echo $ap_subject; } else { echo $row_photo['ap_subject']; } ?>" placeholder="Description">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="del_check[]" id="<? echo $check_id;?>" value="<?php echo $check_id; $check_id++ ?>">
                                        <label class="form-check-label" for="<? echo $check_id-1;?>">Delete</label>
                                    </div>
                                    <input type="hidden" name="ap_id[]" value="<?php echo $row_photo['ap_id']; ?>">
                                    <input type="hidden" name="del_file[]" value="<?php echo $row_photo['ap_picurl']; ?>">
                                </div>
                            <?php } ?>
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
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="album_id" value="<?php echo $row_album['album_id'];?>">
                            <input class="btn btn-primary" type="submit" name="button" value="Update">
                        </form>
                    </div>
                    <div class="card-footer bg-white"></div>
                </div>
            </div>
        </div>
    </div> <!-- ********** End of Update album ********** -->
    
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