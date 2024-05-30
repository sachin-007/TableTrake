<?php
session_start();
include('config/config.php');

// Login
if (isset($_POST['addCustomer'])) {
    // Prevent Posting Blank Values
    if (empty($_POST["customer_phoneno"]) || empty($_POST["customer_name"]) || empty($_POST['customer_email'])) {
        $err = "Blank Values Not Accepted";
    } else {
        $customer_name = $_POST['customer_name'];
        $customer_phoneno = $_POST['customer_phoneno'];
        $customer_email = $_POST['customer_email'];
        $customer_password = sha1(md5($_POST['customer_password'])); // Hash This 
        $customer_id = $_POST['customer_id'];
        $tableno = $_GET['tableno'];

        // Insert Captured information to a database table
        $postQuery = "INSERT INTO rpos_customers (tableno,customer_id, customer_name, customer_phoneno, customer_email, customer_password) VALUES(?,?,?,?,?,?)";
        $postStmt = $mysqli->prepare($postQuery);
        
        // Bind parameters
        $rc = $postStmt->bind_param('ssssss', $tableno, $customer_id, $customer_name, $customer_phoneno, $customer_email, $customer_password);
        $postStmt->execute();
        
        // Check if the query executed successfully
        if ($postStmt->affected_rows > 0) {
            $_SESSION['tableno'] = $tableno;
            header("Location: dashboard.php"); // Redirect to dashboard
            exit(); // Ensure that no further code is executed after redirection
        } else {
            $err = "Please Try Again Or Try Later";
        }
    }
}

require_once('partials/_head.php');
require_once('config/code-generator.php');
?>




<body class="bg-dark">
    <div class="main-content">
        <div class="header bg-gradient-primar py-7">
            <div class="container">
                <div class="header-body text-center mb-7">
                    <div class="row justify-content-center">
                        <div class="col-lg-5 col-md-6">
                            <h1 class="text-white">Restaurant Point Of Sale</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mt--8 pb-5">
    <div class="row justify-content-center ">
    <div class="col-md-3 d-none p-0 d-md-block">
    <img src="./Create-account.jpg" alt="Image" class="img-fluid" style="width: 100%; height: 550px; max-height: 100%;">
</div>
        <div class="col-lg-5 p-0 col-md-7">
            
            <div >
                
                <div class="card-body px-lg-6 py-lg-5 bg-secondary shadow border-0">
                    <div class="row">
                      
                        <div class="col-md-9">
                            <form method="post" role="form">
                                <div class="form-group mb-3">
                                    <div class="input-group input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input class="form-control" required name="customer_name" placeholder="Full Name" type="text">
                                        <input class="form-control" value="<?php echo $cus_id;?>" required name="customer_id"  type="hidden">
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="input-group input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-table"></i></span>
                                        </div>
                                        <input class="form-control" required name="customer_phoneno" readonly value="<?php echo $_GET['tableno']; ?>" type="text">
                                    </div>
                                </div>
<div class="form-group mb-3">
                                    <div class="input-group input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input class="form-control" required name="customer_phoneno" placeholder="Phone Number" type="text">
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
                                <!-- <div class="form-group">
                                    <div class="input-group input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                        </div>
                                        <input class="form-control" required name="customer_password" placeholder="Password" type="password">
                                    </div>
                                </div> -->

                                <div class="text-center">
                                </div>
                                <div class="form-group">
                                    <div class="text-left">
                                        <button type="submit" name="addCustomer" class="btn btn-primary my-4">Create Account</button>
                                        <a href="index.php" class=" btn btn-success pull-right">Log In</a>
                                    </div>
                                </div>
                                <div class="col-6">
                                <a href="../admin/forgot_pwd.php" target="_blank" class="text-black"><small>Forgot password?</small></a>
                            </div>
                            </form>

                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
    <!-- Footer -->
    <?php
    require_once('partials/_footer.php');
    ?>
    <!-- Argon Scripts -->
    <?php
    require_once('partials/_scripts.php');
    ?>
</body>

</html>