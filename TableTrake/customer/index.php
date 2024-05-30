<?php
session_start();
include('config/config.php');
//login 
if (isset($_POST['login'])) {
    $customer_email = $_POST['customer_email'];
    $tableno = $_POST['tableno'];
    $_SESSION['tableno']=$tableno;
    // echo $_POST['tableno'];
    // $customer_password = sha1(md5($_POST['customer_password'])); //double encrypt to increase security
    $stmt = $mysqli->prepare("SELECT customer_name,customer_email, tableno, customer_id  FROM  rpos_customers WHERE (customer_email =? AND tableno =?)"); //sql to log in user
    $stmt->bind_param('ss', $customer_email, $tableno); //bind fetched parameters
    $stmt->execute(); //execute bind 
    $stmt->bind_result($customer_name ,$customer_email, $tableno, $customer_id); //bind result
    $rs = $stmt->fetch();
    $_SESSION['customer_id'] = $customer_id;
    $_SESSION['customer_name']=$customer_name;

    if ($rs) {
        //if its sucessfull
        header("location:dashboard.php");
    } else {
        $err = "Incorrect Authentication Credentials ";
    }
}
require_once('partials/_head.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body style="
    background-image: url('./login.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    min-height: 100vh;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
">

<div class="main-content">
    <div class="header bg-gradient-primar pb-7">
        <div class="container">
            <div class="header-body text-center mb-7">
                <div class="row justify-content-center">
                    <div class="col-lg-9 col-md-6">
                        <h1 class="text-white">Restaurant Point Of Sale</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page content -->
    <div class="container mt--8 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-7 ">
                <div class="card  shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        <form method="post" role="form">
                            <div class="form-group">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-table"></i></span>
                                    </div>
                                    <input class="form-control" required name="tableno" value="<?php echo $_SESSION['tableno'] ?>"  type="text">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input class="form-control" required name="customer_email" placeholder="Email" type="email">
                                </div>
                            </div>
                            <div class="custom-control custom-control-alternative custom-checkbox">
                                <input class="custom-control-input" id=" customCheckLogin" type="checkbox">
                                <label class="custom-control-label" for=" customCheckLogin">
                                    <span class="text-muted">Remember me</span>
</label>
                            </div>
                            <div class="form-group">
                                <div class="text-center">
                                    <button type="submit" name="login" class="btn btn-primary my-4">Log In</button>
                                    <a href="create_account.php" class="btn btn-success my-4">Create Account</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <!-- <a href="../admin/forgot_pwd.php" target="_blank" class="text-light"><small>Forgot password?</small></a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer and Scripts -->
<?php
// require_once('partials/_footer.php');
?>
<!-- Argon Scripts -->
<?php
require_once('partials/_scripts.php');
?>
</body>
</html>