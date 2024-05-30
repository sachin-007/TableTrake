<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();


// $_SESSION['order_number'] = generateOrderNumber();
// // Get order_number from session
// $order_number = $_SESSION['order_number'];



if (isset($_POST['pay'])) {
    // Prevent Posting Blank Values
    if (empty($_POST["pay_amt"]) || empty($_POST['pay_method'])) {
        $err = "Blank Values Not Accepted";
    } else {
        $pay_Code = $_POST['pay_code'];
        if (strlen($pay_Code) != 10) {
            $err = "Payment Code Verification Failed, Please Enter a 10-digit Alpha-Code";
        } else {
            // Generate a unique order number
            $order_number = $_SESSION['order_number'];
            
            // Set order number as constant for all products
            $tableno = $_SESSION['tableno'];
            $pay_code = $_POST['pay_code'];
            $pay_amt = $_POST['pay_amt'];
            $pay_method = $_POST['pay_method'];
            $pay_id = $_POST['pay_id'];
            
            // You need to have the customer_id initialized here, or retrieve it from somewhere else in your code
            $customer_id = $_SESSION['customer_id'];
            
            $order_status = 'paid';
            $order_id = uniqid();

            // Insert captured information into rpos_payments table
            // $postQuery = "INSERT INTO rpos_payments (order_number, order_id, tableno, pay_id, pay_code, customer_id, pay_amt, pay_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            // $upQry = "UPDATE rpos_orders SET order_status = ? WHERE order_number = ?";

            $postQuery = "INSERT INTO rpos_payments (order_number, order_id, tableno, pay_id, pay_code, customer_id, pay_amt, pay_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $upQry = "UPDATE rpos_orders SET order_status = ? WHERE order_number = ?";


            $postStmt = $mysqli->prepare($postQuery);
            $upStmt = $mysqli->prepare($upQry);

            // Bind parameters
            $postStmt->bind_param('ssssssss', $order_number, $order_id, $tableno, $pay_id, $pay_code, $customer_id, $pay_amt, $pay_method);
            $upStmt->bind_param('ss', $order_status, $order_number);

            $postStmt->execute();
            $upStmt->execute();

            // Remove the order_number session
            
            if ($upStmt && $postStmt) {
                $success = "Paid"; // Removed `&&` here
                header("refresh:1; url=payments_reports.php"); // Removed `&&` here
                unset($_SESSION['order_number']);
                $_SESSION['cnt'] = 0;
            } else {
                $err = "Please Try Again Or Try Later";
            }
        }
    }
}

require_once('partials/_head.php');
?>
<body>
    <!-- Sidenav -->
    <?php
    require_once('partials/_sidebar.php');
    ?>
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        
        <!-- Header -->
        <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <h3>Please Fill All Fields</h3>
                        </div>
                        <div class="card-body">
                            <form id="payment-form" method="post" action="">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Payment ID</label>
                                        <input type="text" name="pay_id" readonly value="<?php echo $payid; ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Payment Code</label>
                                        <input type="text" maxlength="10" name="pay_code" value="<?php echo $mpesaCode; ?>" class="form-control" readonly>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Amount (₹)</label>
                                        <input type="text" name="pay_amt" readonly value="<?php echo $_SESSION['finalAmount']; ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Payment Method</label>
                                        <select class="form-control" name="pay_method">
                                            <option selected>Cash</option>
                                            <option>Online</option>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <input type="submit" name="pay" value="Pay Order" class="btn btn-success">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php
            require_once('partials/_footer.php');
            ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php
    require_once('partials/_scripts.php');
    ?>
</body>

</html>
