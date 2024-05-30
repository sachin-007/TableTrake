<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

if (isset($_GET['order_number'])) {
    $order_number = $_GET['order_number'];
    $ret = "SELECT * FROM rpos_orders WHERE order_number = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('s', $order_number);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
?>
        <!DOCTYPE html>
        <html>

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
            <meta name="author" content="MartDevelopers Inc">
            <title>Restaurant Point Of Sale </title>
            <!-- Favicon -->
            <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icons/apple-touch-icon.png">
            <link rel="icon" type="image/png" sizes="32x32" href="assets/img/icons/favicon-32x32.png">
            <link rel="icon" type="image/png" sizes="16x16" href="assets/img/icons/favicon-16x16.png">
            <link rel="manifest" href="assets/img/icons/site.webmanifest">
            <link rel="mask-icon" href="assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
            <meta name="msapplication-TileColor" content="#da532c">
            <meta name="theme-color" content="#ffffff">
            <link href="assets/css/bootstrap.css" rel="stylesheet" id="bootstrap-css">
            <script src="assets/js/bootstrap.js"></script>
            <script src="assets/js/jquery.js"></script>
            <style>
                body {
                    margin-top: 20px;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <div class="row">
                    <div id="Receipt" class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <address>
                                    <strong>Sandip University Project</strong>
                                    <br>
                                    127-0-0-1
                                    <br>
                                    Nashik, Maharashtra
                                    <br>
                                    +91 7517366568
                                </address>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                                <?php
                                if ($res->num_rows > 0) {
                                    // Fetching the first row to get the order details
                                    $order = $res->fetch_object();
                                    $order_date = date('d/M/Y g:i', strtotime($order->created_at));
                                }
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="text-center">
                                <h2>Receipt</h2>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th class="text-center">Unit Price</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $subtotal = 0;
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($item = $res->fetch_object()) {
                                        $total = ($item->prod_price * $item->prod_qty);
                                        $subtotal += $total;
                                    ?>
                                        <tr>
                                            <td class="col-md-9"><em> <?php echo $item->prod_name; ?> </em></td>
                                            <td class="col-md-1" style="text-align: center"> <?php echo $item->prod_qty; ?></td>
                                            <td class="col-md-1 text-center">₹<?php echo $item->prod_price; ?></td>
                                            <td class="col-md-1 text-center">₹<?php echo $total; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php
                                    $tax = $subtotal * 0.18; // Calculating tax (18%)
                                    $totalAmount = $subtotal + $tax; // Total including tax
                                    ?>
                                    <tr>
                                        <td>   </td>
                                        <td>   </td>
                                        <td class="text-right">
                                            <p><strong>Subtotal: </strong></p>
                                            <p><strong>Tax (18%): </strong></p>
                                        </td>
                                        <td class="text-center">
                                            <p><strong>₹<?php echo $subtotal; ?></strong></p>
                                            <p><strong>₹<?php echo $tax; ?></strong></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>   </td>
                                        <td>   </td>
                                        <td class="text-right">
                                            <h4><strong>Total: </strong></h4>
                                        </td>
                                        <td class="text-center text-danger">
                                            <h4><strong>₹<?php echo $totalAmount; ?></strong></h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
                        <button id="print" onclick="printContent('Receipt');" class="btn btn-success btn-lg text-justify btn-block">Print <span class="fas fa-print"></span></button>
                    </div>
                </div>
            </div>
        </body>

        </html>
        <script>
            function printContent(el) {
                var restorepage = $('body').html();
                var printcontent = $('#' + el).clone();
                $('body').empty().html(printcontent);
                window.print();
                $('body').html(restorepage);
            }
        </script>
<?php
    } else {
        echo "Order not found.";
    }
} else {
    echo "Order number not provided.";
}
?>
