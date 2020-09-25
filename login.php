<?php
session_start();
// Check if the admin has logged in yet
if ((!isset($_SESSION['loginMember'])) || ($_SESSION['loginMember'] === "")) {
    // If not yet login, check if user has submit the login request
    if ((isset($_POST['username'])) && (isset($_POST['passwd']))) {
        // If yes, connect the database to check if the username and password is correct
        require_once "connMysql.php";
        $login_query = "SELECT * FROM admin";
        $login = $db_link->query($login_query);
        while ($row_login = $login->fetch_array(MYSQLI_ASSOC)) {
            if (($row_login['username'] === $_POST['username']) && ($row_login['passwd'] === $_POST['passwd'])) {
                // If login's input are correct, create the session record and redirect user to admin page
                $_SESSION['loginMember'] = $row_login['username'];
                $db_link->close();
                header("Location: admin.php");
            } else {
                // display the error message
                $username = $_POST['username'];
                $passwd = $_POST['passwd'];
                $login_err = '</br><span class="text-danger">&nbsp;Sorry, login failed. The password is incorrect.</span>';
            }
        }
    }
} else {
    // If session record is already existed, direct user to admin page
    header("Location: admin.php");
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
<body>
    <!-- ********** navbar ********** -->
    <nav class="navbar navbar-expand-md bg-dark navbar-dark sticky-top">
        <!-- Brand -->
        <a class="navbar-brand" href="#">PhotoBook</a>
        <!-- Toggler/collapsibe Button -->
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
                <a class="nav-link" href="#">LOGIN</a>
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
            </div>
        </div>
    </div> <!-- ********** End of Logo ********** -->

    <!-- ********** Login card ********** -->
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6 mx-auto">
                <div class="card login-card">
                    <div class="card-header text-center pb-2 bg-white">
                        <p class="mb-1"><img src="images/login_icon.png" alt="login icon" width="50px" height="50px"></p>
                        <h3 class="login mb-0">Login</h3>
                        <p></p>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input class="form-control mb-3" type="text" id="username" name="username" placeholder="Username" value="<?php if ($username !== "") { echo $username; }?>">
                            <input class="form-control mb-3" type="password" id="passwd" name="passwd" placeholder="Password" value="<?php if ($passwd !== "") { echo $passwd; }?>">
                            <div class="text-center">
                                <input class="btn btn-secondary" type="submit" id="login" name="login" value="Login">
                                <?php if ($login_err !== "") { echo $login_err; } ?>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer bg-white text-center text-secondary">
                        <?php echo "Copyright&nbsp;&copy;&nbsp;" . date("Y") . " All rights reserved | Leon Lee"; ?>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- ********** End of Login card ********** -->
    
    
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>
</html>