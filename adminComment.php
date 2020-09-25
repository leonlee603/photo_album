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
// Sanitize the $_GET['id'] value, sanitize id equal to album photo id
if ((isset($_GET['id'])) && ($_GET['id'] !== "")) {
    $sid = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
}
// Prepare to display comment
$comment_query = "SELECT * FROM comment where ap_id = $sid ORDER BY comment_time DESC";
$comment = $db_link->query($comment_query);
$num_comment = $comment->num_rows;
$album_query = "SELECT album_id FROM albumphoto WHERE ap_id = $sid";
$album = $db_link->query($album_query);
$row_album = $album->fetch_array(MYSQLI_ASSOC);

// Delete comment
if ((isset($_GET['action'])) && ($_GET['action'] === "delete")) {
    $comment_id = $_GET['cid'];
    $delete_comment_query = "DELETE FROM comment WHERE comment_id = $comment_id";
    $db_link->query($delete_comment_query);
    // Update the number of comment to database
    $update_comment_num_query = "UPDATE albumphoto SET ap_comment=ap_comment-1 WHERE ap_id=$sid";
    $db_link->query($update_comment_num_query);
    // Go back to admin page after deletion
    header("Location: adminComment.php?id=$sid");
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

    <!-- ********** Update comment ********** -->
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-9 mx-auto">
                <div class="card create-album">
                    <div class="card-header text-center pb-2 bg-white">
                        <div class="row">
                            <a class="mx-2 back-button text-secondary" href="adminEdit.php?id=<?php echo $row_album['album_id'];?>"><i class="fas fa-arrow-left"></i> back to album</a>
                        </div>
                        <p class="mb-1 display-4 text-warning"><i class="fas fa-edit"></i></p>
                        <h3 class="create mb-2">Delete Comment</h3>
                        <p class="mb-0"><?php echo $num_comment;?>&nbsp;<i class="far fa-comment"></i></p>
                    </div>
                    <div class="card-body">
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
                                    <!-- Edit and Delete icon for admin only -->
                                    <span class="float-right">
                                        <a id= "delete_comment" href="?action=delete&id=<?php echo $row_comment['ap_id'];?>&cid=<?php echo $row_comment['comment_id'];?>"><i class="fas fa-trash-alt text-secondary"></i></a>
                                    </span> <!-- End of Edit and Delete icon -->
                                    </p>
                                    <h5 class="mb-1"><?php echo $row_comment['comment_subject']; ?></h5>
                                    <p class="mb-1"><?php echo $row_comment['comment_content']; ?></p>
                                    <p class="text-secondary  mb-0 float-right boardtime"><?php echo $row_comment['comment_time']; ?></p>
                                </div> <!-- End of Right side of comment -->
                            </div> <!-- End of Container of comment -->
                        <?php } ?> <!-- ********** End of Display the comments ********** -->
                    </div>
                    <div class="card-footer bg-white"></div>
                </div>
            </div>
        </div>
    </div> <!-- ********** End of Update comment ********** -->
    
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
    <script src="js/confirmDelete.js"></script>
</body>
</html>
<?php
$db_link->close();
?>