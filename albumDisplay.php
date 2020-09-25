<?php
require_once "connMysql.php";
// Sanitize the $_GET['id'] value, sanitize id (sid) can be album id or album photo id
if ((isset($_GET['id'])) && ($_GET['id'] !== "")) {
    $sid = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
}
// Sanitize the $_GET['aid'] value, sanitize aid equal to album id
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
// Prepare to display the album page
$album_query = "SELECT * FROM album WHERE album_id = $sid";
$photo_query = "SELECT * FROM albumphoto WHERE album_id = $sid ORDER BY ap_date ASC";
$album = $db_link->query($album_query);
$photo = $db_link->query($photo_query);
$total_photos = $photo->num_rows;
$photo_info = $db_link->query($photo_query);
// Fetch the data from $album
$row_album = $album->fetch_array(MYSQLI_ASSOC);
$row_photo_info = $photo_info->fetch_array(MYSQLI_ASSOC);
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
    <title><?php echo $row_album['album_title'] ?> | Photobook</title>
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
    
    <!-- ********** Logo ********** -->
    <div class="container">
        <div class="row">
            <div class="col-12 text-center my-3">
                <h3><span class="logo-f">Photo</span> <span class="logo-l">Book</span></h3>
                <hr>
                <div class="row">
                <a class="mx-2 back-button text-secondary" href="index.php"><i class="fas fa-arrow-left"></i> back to all albums</a>
                </div>
            </div>
        </div>
    </div> <!-- ********** End of Logo ********** -->

    <!-- ********** Album display ********** -->
    <div class="container-fluid">
        <!-- ********** Container for Album info & Album photos ********** -->
        <div class="col-12 col-md-10 mx-auto px-0">
            <!-- ********** Album info ********** -->
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 mx-auto px-0">
                    <div class="card rounded-0">
                        <div class="album-top px-0">
                            <?php if(!$row_photo_info){?>
                                <img class="card-image-top img-fluid" src="images/empty.jpg" alt="no image">
                            <?php }else{?> 
                                <img class="card-image-top img-fluid" src="photos/<?php echo $row_photo_info["ap_picurl"]; ?>" alt="<?php echo $row_album["album_title"]; ?>">
                            <?php } ?>
                        </div>
                        <div class="card-img-overlay d-flex flex-column justify-content-center px-3 pb-0 text-center album-info">
                                <h5 class="text-white mb-1 text-shadow"><?php echo $row_album['album_title'] ?></h5>
                                <p class="text-white text-shadow">
                                    <?php echo $total_photos ?>&nbsp;<i class="far fa-images"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $row_album['album_hits'] ?>&nbsp;<i class="far fa-eye"></i>
                                </p>
                                <p class="text-white">Description : </br><?php if ($row_album['album_desc'] === "") { echo "NULL"; } else { echo nl2br($row_album["album_desc"]); }?></p>
                        </div>
                    </div>
                </div>
            </div> <!-- ********** End of Album info ********** -->
            <!-- ********** Album photos ********** -->
            <div class="container-fluid px-0 mx-0">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 mx-auto px-0">
                    <div class="row">
                        <?php while ($row_photo = $photo->fetch_array(MYSQLI_ASSOC)) { ?>
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mx-0 px-0">
                                <div class="card rounded-0 m-0">
                                    <div class="album-image">
                                        <img class="card-image-top img-fluid" src="photos/<?php echo $row_photo['ap_picurl'] ?>" alt="<?php echo $row_photo['ap_subject'] ?>">
                                    </div>
                                    <!-- Image overlay with the view number of that picture -->
                                    <a href="?action=hits&id=<?php echo $row_photo["ap_id"];?>&aid=<?php echo $row_photo['album_id'];?>">
                                        <div class="card-img-overlay d-flex flex-column justify-content-end px-3 pb-0 album-image-hits">
                                            <p class="text-white text-shadow mb-2"><?php echo $row_photo['ap_hits'] ?>&nbsp;<i class="far fa-eye"></i>&nbsp;&nbsp;<?php echo $row_photo['ap_comment'] ?>&nbsp;<i class="far fa-comment"></i></p>
                                        </div>
                                    </a> <!-- End of Image overlay with the view number of that picture -->
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div> <!-- ********** End of Album photos ********** -->
        </div> <!-- ********** End of Container for Album info & Album photos ********** -->
    </div> <!-- ********** End of Album display ********** -->

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