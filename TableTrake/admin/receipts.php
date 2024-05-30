<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav --><!-- For more projects: Visit freeprojectscodes.com  -->
    <?php
    require_once('partials/_sidebar.php');
    ?>
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php
        require_once('partials/_topnav.php');
        ?>
        <!-- Header -->
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
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
                        <div class="row ml-2">
                            <div class="card-header border-0">
                                Paid Orders
                            </div>
                            <div class="col-md-3">
                            <form method="POST">
                                <div class="input-group ml-8 mt-1 mb-1">
                                    <input type="text" name="searchTerm" class="form-control"
                                        placeholder="Order Number">
                                    <div class="input-group-append">
                                        <button type="submit" name="search" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">order number</th>
                                        <th scope="col">Customer</th>
                                        <th class="text-success" scope="col">Product</th>
                                        <th scope="col">Unit Price</th>
                                        <th class="text-success" scope="col">Qty</th>
                                        <th scope="col">Total Price</th>
                                        <th class="text-success" scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                               
                                <tbody>
                                    <?php
                                    if (isset($_POST['search'])) {
                                        $searchTerm = '%' . $_POST['searchTerm'] . '%';
                                        $ret = "SELECT * FROM rpos_orders WHERE order_number LIKE ?";
                                        $stmt = $mysqli->prepare($ret);
                                        if ($stmt) {
                                            $stmt->bind_param('s', $searchTerm);
                                            $stmt->execute();
                                            $res = $stmt->get_result();
                                            while ($order = $res->fetch_object()) {
                                                $total = ($order->prod_price * $order->prod_qty);
                                    ?>
                                                <tr>
                                                    <th class="text-success" scope="row"><?php echo $order->order_number; ?></th>
                                                    <td><?php echo $order->customer_name; ?></td>
                                                    <td class="text-success"><?php echo $order->prod_name; ?></td>
                                                    <td>₹ <?php echo $order->prod_price; ?></td>
                                                    <td class="text-success"><?php echo $order->prod_qty; ?></td>
                                                    <td>₹ <?php echo $total; ?></td>
                                                    <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                                                    <td>
                                                        <a target="_blank" href="print_receipt.php?order_number=<?php echo $order->order_number; ?>">
                                                            <button class="btn btn-sm btn-primary">
                                                                <i class="fas fa-print"></i>
                                                                Print Receipt
                                                            </button>
                                                        </a>
                                                    </td>
                                                </tr>
                                    <?php
                                            }
                                        } else {
                                            echo "Error in preparing statement: " . $mysqli->error;
                                        }
                                    } else {
                                        $ret = "SELECT * FROM  rpos_orders WHERE order_status = 'Paid' ORDER BY `rpos_orders`.`created_at` DESC  ";
                                        $stmt = $mysqli->prepare($ret);
                                        $stmt->execute();
                                        $res = $stmt->get_result();
                                        while ($order = $res->fetch_object()) {
                                            $total = ($order->prod_price * $order->prod_qty);
                                    ?>
                                            <tr>
                                                <th class="text-success" scope="row"><?php echo $order->order_number; ?></th>
                                                <td><?php echo $order->customer_name; ?></td>
                                                <td class="text-success"><?php echo $order->prod_name; ?></td>
                                                <td>₹ <?php echo $order->prod_price; ?></td>
                                                <td class="text-success"><?php echo $order->prod_qty; ?></td>
                                                <td>₹ <?php echo $total; ?></td>
                                                <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                                                <td>
                                                    <a target="_blank" href="print_receipt.php?order_number=<?php echo $order->order_number; ?>">
                                                        <button class="btn btn-sm btn-primary">
                                                            <i class="fas fa-print"></i>
                                                            Print Receipt
                                                        </button>
                                                    </a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
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
<!-- For more projects: Visit freeprojectscodes.com  -->
</html>