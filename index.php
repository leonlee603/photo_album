<?php
require_once "connMysql.php";
// number of album display on the page
$pageRow_records = 12;
// Default page number
$num_pages = 1;
if (isset($_GET['page'])) {
    // $num_pages = $_GET['page'];
    $num_pages = filter_var($_GET['page'], FILTER_SANITIZE_NUMBER_INT);
}
// Staring number of the album of each page
$startRow_records = ($num_pages - 1) * $pageRow_records;
// Query of information for all album
$album_all_query = "SELECT album.album_id , album.album_date , album.album_location , album.album_title , album.album_desc , album.album_hits , albumphoto.ap_picurl, count( albumphoto.ap_id ) AS albumNum FROM album LEFT JOIN albumphoto ON album.album_id = albumphoto.album_id GROUP BY album.album_id , album.album_date , album.album_location , album.album_title , album.album_desc ORDER BY album_date DESC";
// Result for all album
$album_all = $db_link->query($album_all_query);
// Query of information for album per page (pp)
$album_pp_query = $album_all_query." LIMIT {$startRow_records}, {$pageRow_records}";
// Result for album per page
$album_pp = $db_link->query($album_pp_query);
// Total number of album
$total_records = $album_all->num_rows;
// Total number of page
$total_pages = ceil($total_records / $pageRow_records);

// Sanitize the $_GET['id'] value, sanitize id (sid) equal to album id
$sid = 0;
if ((isset($_GET['id'])) && ($_GET['id'] !== "")) {
    $sid = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
}
// When user click on any album, will count the view of that particular album and direct user to the album photo page
if ((isset($_GET['action'])) && ($_GET['action'] === "hits")) {
    $view_query = "UPDATE album SET album_hits=album_hits+1 WHERE album_id = $sid";
    $db_link->query($view_query);
    header("Location: albumDisplay.php?id=$sid");
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
        <!-- Toggler/collapsibe Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Navbar links -->
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
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
                <p><?php echo $total_records ?>&nbsp;<i class="fas fa-book"></i></br></p>
            </div>
        </div>
    </div> <!-- ********** End of Logo ********** -->

    <!-- ********** Album ********** -->
    <div class="container-fluid">
        <!-- ********** Responsive container for all album ********** -->
        <div class="col-12 col-md-10 mx-auto px-0">
            <div class="row">
                <?php while($row_album_pp=$album_pp->fetch_array(MYSQLI_ASSOC)){ ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 my-3">
                        <div class="card rounded-0">
                            <div class="image">
                                <?php if($row_album_pp["albumNum"]==0){?>
                                    <img class="card-image-top img-fluid" src="images/empty.jpg" alt="no image">
                                <?php }else{?>
                                    <img class="card-image-top img-fluid" src="photos/<?php echo $row_album_pp["ap_picurl"]; ?>" alt="<?php echo $row_album_pp["album_title"]; ?>">
                                <?php } ?>
                            </div>
                            <a href="?action=hits&id=<?php echo $row_album_pp["album_id"];?>">
                            <!-- <a href="albumDisplay.php?id=<?php echo $row_album_pp["album_id"]; ?>"> -->
                                <div class="card-img-overlay d-flex flex-column justify-content-end px-3 pb-0 border_effect">
                                        <h6 class="text-white mb-1 text-shadow"><?php echo $row_album_pp['album_title'] ?></h6>
                                        <p class="text-white text-shadow mb-2">
                                            <?php echo $row_album_pp['albumNum'] ?>&nbsp;<i class="far fa-images"></i>&nbsp;&nbsp;
                                            <?php echo $row_album_pp['album_hits'] ?>&nbsp;<i class="far fa-eye"></i>
                                        </p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div> <!-- ********** End of Responsive container for all album ********** -->
    </div>  <!-- ********** End of Album ********** -->

    <!-- ********** Pagination ********** -->
    <!-- Only show page selection when there is more than 1 page -->
    <?php if ($total_pages > 1) { ?> 
    <div class="row justify-content-center pageList mt-3">
        <ul class="pagination pagination-sm">
            <?php if ($num_pages == 1) { ?>
            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
            <?php } else { ?>
            <li class="page-item"><a class="page-link" href="<?php echo "index.php?page=" . ($num_pages-1); ?>">Previous</a></li>
            <?php } ?>
            <?php for ($i=1; $i <= $total_pages; $i++) {
                if ($i == $num_pages) { ?>
                    <li class="page-item active"><span class="page-link pointer" href=""><?= $i ?></span></li>
                <?php } else { ?>
                    <li class="page-item"><a class="page-link" href=<?php echo "\"index.php?page=" . $i . "\""?>><?php echo $i; ?></a></li>
                <?php } ?>
            <?php } ?>
            <?php if ($num_pages == $total_pages) { ?>
            <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
            <?php } else { ?>
            <li class="page-item"><a class="page-link" href="<?php echo "index.php?page=" . ($num_pages+1); ?>">Next</a></li>
            <?php } ?>
        </ul>
    </div> 
    <?php } ?><!-- ********** End of Pagination ********** -->

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