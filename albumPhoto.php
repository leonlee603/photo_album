<?php
require_once "connMysql.php";
// Sanitize the $_GET['id'] value, sanitize id (sid) can be album id or album photo id
if ((isset($_GET['id'])) && ($_GET['id'] !== "")) {
    $sid = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
}
// Sanitize the $_GET['album_id'] value, album id (aid) can be album id or album photo id
if ((isset($_GET['aid'])) && ($_GET['aid'] !== "")) {
    $aid = filter_var($_GET['aid'], FILTER_SANITIZE_NUMBER_INT);
}
// When user click on any photo, sid will be changed form id of album to id of the photo,
// then count the view of that particular picture and direct user to the photo page
if ((isset($_GET['action'])) && ($_GET['action'] === "hits")) {
    $view_query = "UPDATE albumphoto SET ap_hits=ap_hits+1 WHERE ap_id = $sid";
    $db_link->query($view_query);
    header("Location: albumPhoto.php?id=$sid&aid=$aid");
}
// Prepare to display all photos in this album
$all_photo_query = "SELECT * FROM albumphoto WHERE album_id = $aid ORDER BY ap_date ASC";
$all_photo = $db_link->query($all_photo_query);

// Prepare to display the specific photo in an album
$photo_query = "SELECT album.album_title, albumphoto.* FROM album, albumphoto WHERE (album.album_id = albumphoto.album_id) AND ap_id = $sid";
$photo = $db_link->query($photo_query);
$row_photo = $photo->fetch_array(MYSQLI_ASSOC);

// Prepare to display comment
$comment_query = "SELECT * FROM comment where ap_id = $sid ORDER BY comment_time DESC";
$comment = $db_link->query($comment_query);
$num_comment = $comment->num_rows;

// Save the comment to database
$err_list = [];
if ((isset($_POST['action'])) && ($_POST['action'] === "add")) {
    // Name
    if (empty($_POST['comment_name'])) {
        $comment_name_err = '<span class="text-danger">Name is a required field</span>';
        $err_list[] = $comment_content_err;
    } else {
        $comment_name = trim($_POST['comment_name']);
        $comment_name = filter_var($comment_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    // Email
    if (empty($_POST["comment_mail"])) {
        $comment_mail = "";
    } else {
        $comment_mail = trim($_POST["comment_mail"]);
        // check if e-mail address is well-formed
        if (!filter_var($comment_mail, FILTER_VALIDATE_EMAIL)) {
            $comment_mail_err = '<span class="text-danger">Invalid email format</span>';
            $err_list[] = $comment_mail_err;
        }
    }
    // Subject
    if (empty($_POST["comment_subject"])) {
        $comment_subject_err = '<span class="text-danger">Subject is a required field</span>';
        $err_list[] = $comment_subject_err;
    } else {
        $comment_subject = trim($_POST["comment_subject"]);
        $comment_subject = filter_var($comment_subject, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    // Comment's content
    if (empty($_POST["comment_content"])) {
        $comment_content_err = '<span class="text-danger">Comment is a required field</span>';
        $err_list[] = $comment_content_err;
    } else {
        $comment_content = trim($_POST["comment_content"]);
        $comment_content = filter_var($comment_content, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    // Gender
    $comment_gender = $_POST["comment_gender"];
    // ap_id & album_id
    $photo_id = $_POST['id'];
    $album_id = $_POST['aid'];

    // If all required field are filled and no error occurred, connect to database and insert the data into table "board"
    if (empty($err_list)) {
        $save_comment_query = "INSERT INTO comment (comment_name, comment_gender, comment_subject, comment_time, comment_mail, comment_content, ap_id, album_id) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)";
        $save_comment_stmt = $db_link->prepare($save_comment_query);
        $save_comment_stmt->bind_param("sssssii", $comment_name, $comment_gender, $comment_subject, $comment_mail, $comment_content, $photo_id, $album_id);
        $save_comment_stmt->execute();
        $save_comment_stmt->close();
        // Update the number of comment to database
        $update_comment_num_query = "UPDATE albumphoto SET ap_comment=ap_comment+1 WHERE ap_id=$photo_id";
        $db_link->query($update_comment_num_query);

        header("Location: albumPhoto.php?id=$photo_id&aid=$album_id");
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
    <link rel="stylesheet" href="css/style.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poiret+One&family=Nanum+Gothic&display=swap" rel="stylesheet"> <!-- logo -->
    <title>Photobook</title>
</head>
<body class="flex-wrapper">
    <!-- ********** navbar ********** -->
    <nav class="navbar navbar-expand-md bg-dark navbar-dark sticky-top">
        <!-- Brand -->
        <a class="navbar-brand" href="index.php">PhotoBook</a>
        <!-- Toggler/collapsible Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Navbar links -->
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">ALBUM</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="allPhoto.php">ALL PHOTOS</a>
            </li>
            </ul>
            <ul class="navbar-nav">
            <li class="nav-item float-right">
                <a class="nav-link" href="login.php">LOGIN</a>
            </li>
            </ul>
        </div>
    </nav> <!-- ********** End of navbar ********** -->
    
    <!-- Display a specific photo -->
    <div class="container-fluid py-5 photo-box-container">
        <div class="row">
            <a class="mx-2 back-button text-white back-to-album" href="albumDisplay.php?id=<?php echo $aid;?>"><i class="fas fa-arrow-left"></i> back to album</a>
        </div>
        <div class="row photo-box px-3">
            <img class="mx-auto d-block img-fluid" src="photos/<?php echo $row_photo['ap_picurl'] ?>" alt="<?php echo $row_photo['ap_subject'] ?>" data-toggle="modal" data-target="#myModal">
            <!-- The Modal -->
            <div class="modal fade" id="myModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <!-- Modal body -->
                        <div id="photo_modal">
                        <div class="modal-body p-0">
                            <img class="img-fluid mx-auto d-block" src="photos/<?php echo $row_photo['ap_picurl'] ?>" alt="<?php echo $row_photo['ap_subject'] ?>">
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End of Display a specific photo -->

    <!-- Display all photos in this album -->
    <div class="row">
        <?php while ($row_all_photo = $all_photo->fetch_array(MYSQLI_ASSOC)) { ?>
            <div class="col-2 col-sm-2 col-md-1 col-lg-1 mx-0 px-0">
                <div class="card rounded-0 m-0">
                    <div class="album-image">
                        <a href="?action=hits&id=<?php echo $row_all_photo["ap_id"];?>&aid=<?php echo $row_all_photo['album_id'];?>"><img class="card-image-top img-fluid" src="photos/<?php echo $row_all_photo['ap_picurl'] ?>" alt="<?php echo $row_all_photo['ap_subject'] ?>" width="50px" height="50px"></a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div> <!-- End of Display all photos in this album -->

    <!-- Photo information and comments -->
    <div class="container-fluid py-3">
        <div class="row">
            <!-- Photo information -->
            <div class="col-12 col-sm-12 col-md-5 col-lg-5 col-xl-5">
                <div class="jumbotron px-3 py-3">
                    <h5>Album Title&nbsp;:&nbsp;<?php echo $row_photo['album_title']; ?></h5>
                    <hr>
                    <p><i class="far fa-eye"></i>&nbsp;:&nbsp;<?php echo $row_photo['ap_hits']; ?>&nbsp;<?php if ($row_photo['ap_hits'] <= 1) { echo "view"; } else { echo "views"; } ?></p>
                    <p><i class="far fa-calendar-alt"></i>&nbsp;:&nbsp;<?php echo $row_photo['ap_date']; ?></p>
                    <p><i class="far fa-file-alt"></i>&nbsp;:&nbsp;<?php if ($row_photo['ap_subject'] ==="") { echo "Null"; } else { echo $row_photo['ap_subject']; } ?></p>
                    <p>&copy; All Rights Reserved</p>
                </div>
            </div> <!-- End of Photo information -->
            <!-- Comment section -->
            <div class="col-12 col-sm-12 col-md-7 col-lg-7 col-xl-7">
                <div class="jumbotron py-2">
                    <div class="row py-2">
                        <h5>Comments :&nbsp;&nbsp;
                            <?php if ($row_photo['ap_comment'] >=1) {
                                echo $row_photo['ap_comment'] . ' <i class="far fa-comment"></i>';
                            } ?>
                        </h5>
                    </div>
                    <!-- ********** Comment form ********** -->
                    <div id="accordion">
                        <div class="card mb-2">
                            <div class="card-header">
                                <a class="card-link text-secondary" data-toggle="collapse" href="#collapseOne">
                                <i class="fas fa-pencil-alt"></i> Leave a comment!
                                </a>
                            </div>
                            <div id="collapseOne" class="collapse mx-1 <?php if (!empty($err_list)) echo "show"; ?>" data-parent="#accordion">
                                <form method="post" action="?id=<?=$sid?>&aid=<?=$aid?>">
                                <div class="row">
                                    <div class="form-group col-12 col-md-6">
                                        <label for="comment_name">Name:&nbsp;<span class="text-danger">*</span></label><?php if ($comment_name_err!=="") { echo " " . $comment_name_err; } ?>
                                        <input type="text" class="form-control" name="comment_name" id="comment_name" placeholder="Enter your name" value="<?php if ($comment_name !== "") { echo $comment_name; }?>">
                                    </div>
                                    <div class="form-group col-12 col-md-6">
                                        <label for="comment_mail">E-mail:</label><?php if ($comment_mail_err!=="") { echo " " . $comment_mail_err; } ?>
                                        <input type="text" class="form-control" name="comment_mail" id="comment_mail" placeholder="Enter your E-mail" value="<?php if ($comment_mail !== "") { echo $comment_mail; }?>">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="comment_subject">Subject:&nbsp;<span class="text-danger">*</span></label><?php if ($comment_subject_err!=="") { echo " " . $comment_subject_err; } ?>
                                        <input type="text" class="form-control" name="comment_subject" id="comment_subject" placeholder="Enter the subject" value="<?php if ($comment_subject !== "") { echo $comment_subject; }?>">
                                    </div>
                                    <div class="col-12">
                                    <div class="form-group">
                                        <label for="comment_content">Comment:&nbsp;<span class="text-danger">*</span></label><?php if ($comment_content_err!=="") { echo " " . $comment_content_err; } ?>
                                        <textarea type="text" class="form-control" name="comment_content" id="comment_content" placeholder="Enter your message"><?php if ($comment_content !== "") { echo $comment_content; }?></textarea>
                                    </div>
                                    <label for="radio">Gender:</label>
                                    <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="comment_gender" id="radio" value="M" checked>M
                                    </label>
                                    </div>
                                    <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="comment_gender" id="radio" value="F" <?php if ($comment_gender === "F") { echo "checked"; } ?>>F
                                    </label>
                                    </div>
                                    <p class="text-danger">* is required</p>
                                    </div>
                                    <div class="col-12 my-2">
                                        <input name="action" type="hidden" id="action" value="add">
                                        <input type="hidden" name="id" value="<?php echo $sid;?>">
                                        <input type="hidden" name="aid" value="<?php echo $aid?>">
                                        <input type="submit" name="button" id="button" value="Submit">
                                    </div>
                                    
                                </div>
                                </form>
                            </div>
                        </div>
                    </div> <!-- ********** End of Comment form ********** -->

                    <!-- ********** Display the comments ********** -->
                    <?php if ($num_comment === 0) { ?>
                        <p>Do not have any comment yet.</p>
                    <?php } ?>
                    <?php while ($row_comment = $comment->fetch_array(MYSQLI_ASSOC)) { ?>
                        <div class="media border p-2 mb-2"> <!-- Container of comment -->
                            <div class="px-1 mr-4"> <!-- Left side of comment -->
                                <?php if ($row_comment['comment_gender']=='M') { ?>
                                <img src="images/m_profile.png" class="align-self-start  mt-1 rounded-circle" style="width:60px">
                                <?php } elseif ($row_comment['comment_gender']=='F') { ?>
                                <img src="images/f_profile.png" class="align-self-start  mt-1 rounded-circle" style="width:60px">
                                <?php } ?>
                            </div> <!-- End of Left side of comment -->
                            <div class="media-body"> <!-- Right side of comment -->
                                <p class="mt-1 mb-1"><?php echo $row_comment['comment_name']; ?>
                                <!-- Show the writer's email if there is any -->
                                <?php if ($row_comment['comment_mail'] !== "") { ?>
                                    <span><a href="mailto:<?= $row_comment['comment_mail']; ?>"><i class="fas fa-paper-plane text-secondary"></i></a></span>
                                <?php } ?>
                                </p>
                                <h5 class="mb-1"><?php echo $row_comment['comment_subject']; ?></h5>
                                <p class="mb-1"><?php echo $row_comment['comment_content']; ?></p>
                                <p class="text-secondary  mb-0 float-right boardtime"><?php echo $row_comment['comment_time']; ?></p>
                            </div> <!-- End of Right side of comment -->
                        </div> <!-- End of Container of comment -->
                    <?php } ?> <!-- ********** End of Display the comments ********** -->
                </div> <!-- End ofComment section -->
            </div> 
        </div>
    </div> <!-- End of Photo information and comments -->

    <!-- ********** Footer ********** -->
    <footer class="bg-dark mt-5 text-secondary">
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