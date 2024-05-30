<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

require_once('partials/_head.php');

$_SESSION['cnt'] = isset($_SESSION['cnt']) ? $_SESSION['cnt'] : 0;

if (!isset($_SESSION['quantity'])) {
    $_SESSION['quantity'] = array();
}

// Handle form submission for making orders
if (isset($_POST['make'])) {

    $_SESSION['cnt'] = isset($_SESSION['cnt']) ? $_SESSION['cnt'] : 0;

    // Increment order counter
    if (isset($_SESSION['cnt'])) {
        $_SESSION['cnt']++;
    } else {
        $_SESSION['cnt'] = 0;
    }

    // Validate form data
    if (empty($_POST["order_code"]) || empty($_POST["prod_price"]) || empty($_POST['prod_name'])) {
        $err = "Blank Values Not Accepted";
    } else {
        // Get session variables
        $customer_id = $_SESSION['customer_id'];
        $tableno = $_SESSION['tableno'];
        $customer_name = $_SESSION['customer_name'];

        // Get form data
        $order_id = bin2hex(random_bytes('5'));
        $order_code = $_POST['order_code'];
        $prod_price = $_POST['prod_price'];
        $prod_id = $_POST['prod_id'];
        $prod_name = $_POST['prod_name'];
        // $prod_qty = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
        $prod_qty = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
 

        // Insert captured information into the database table
        $postQuery = "INSERT INTO rpos_orders (tableno, prod_qty, order_id, order_code, customer_id, customer_name, prod_id, prod_name, prod_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $postStmt = $mysqli->prepare($postQuery);
        if (!$postStmt) {
            $err = "Error in preparing statement: " . $mysqli->error;
            echo $err;
        } else {
            // Bind parameters
            $postStmt->bind_param('sssssssss', $tableno, $prod_qty, $order_id, $order_code, $customer_id, $customer_name, $prod_id, $prod_name, $prod_price);
            // Execute statement
            if (!$postStmt->execute()) {
                $err = "Error executing statement: " . $postStmt->error;
                echo $err;
            } else {
                // Redirect to payments page after successful order placement
                // header("Location: payments.php");
                // exit();
            }
            // Close statement
            $postStmt->close();
        }
    }
}

?>

<body>
    <div class="main-content">
        <!-- Top navbar -->
        <?php require_once('partials/_topnav.php'); ?>

        <!-- Header -->
        <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
            class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body"></div>
            </div>
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <div class="container-fluid">
                <div class="card-header col-12 border-0">
                    <div class="row pt-2 pb-4">
                        <div class="col-md-6">
                            <form method="POST">
                                <div class="input-group m-1">
                                    <input type="text" name="searchTerm" class="form-control"
                                        placeholder="Search for a product...">
                                    <div class="input-group-append">
                                        <button type="submit" name="search" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form id="categoryForm" method="POST">
                                <div class="input-group m-1">
                                    <select id="categorySelect" name="category" class="form-control">
                                        <option value="">All Categories</option>
                                        <?php
                                        // Query to retrieve categories from the database
                                        $stmt = $mysqli->prepare("SELECT cat_name FROM rpos_categories");
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        // Loop through the categories and generate HTML options dynamically
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . $row['cat_name'] . "'>" . $row['cat_name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" name="filter" style="display: none;"></button>
                            </form>
                        </div>
                        <div class="col-md-2">
                            <a href="payments.php" class="btn btn-info btn-block">Order Now
                                <span
                                    class="badge badge-danger badge-lg"><?php echo isset($_SESSION['cnt']) ? $_SESSION['cnt'] : 0; ?></span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col p-0">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                Select Any Product To Make An Order
                            </div>
                            <div class="table-responsive">
                                <table class="table align-items-center table-flush">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">Image</th>
                                            <!-- <th scope="col">Product Code</th> -->
                                            <th scope="col">Name</th>
                                            <th scope="col">Price</th>
                                            <!-- <th scope="col">Quantity</th> -->
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch products based on the selected category or all products if no category selected
                                        if (isset($_POST['filter']) || isset($_POST['category'])) {
                                            $category = isset($_POST['category']) ? $_POST['category'] : '';
                                            if (!empty($category)) {
                                                $ret = "SELECT * FROM rpos_products WHERE fk_cat_id IN (SELECT cat_id FROM rpos_categories WHERE cat_name = ?) ORDER BY `rpos_products`.`created_at` DESC ";
                                                $stmt = $mysqli->prepare($ret);
                                                if ($stmt) {
                                                    $stmt->bind_param('s', $category);
                                                    $stmt->execute();
                                                    $res = $stmt->get_result();
                                                } else {
                                                    echo "Error in preparing statement: " . $mysqli->error;
                                                }
                                            } else {
                                                $ret = "SELECT * FROM rpos_products ORDER BY `rpos_products`.`created_at` DESC ";
                                                $res = $mysqli->query($ret);
                                            }
                                        } elseif (isset($_POST['search'])) {
                                            $searchTerm = '%' . $_POST['searchTerm'] . '%';
                                            $ret = "SELECT * FROM rpos_products WHERE prod_name LIKE ? ORDER BY `rpos_products`.`created_at` DESC ";
                                            $stmt = $mysqli->prepare($ret);
                                            if ($stmt) {
                                                $stmt->bind_param('s', $searchTerm);
                                                $stmt->execute();
                                                $res = $stmt->get_result();
                                            } else {
                                                echo "Error in preparing statement: " . $mysqli->error;
                                            }
                                        } else {
                                            $ret = "SELECT * FROM rpos_products ORDER BY `rpos_products`.`created_at` DESC ";
                                            $res = $mysqli->query($ret);
                                        }
if ($res) {
    while ($prod = $res->fetch_object()) {
        // Check if $_SESSION['quantity'] is an array
        if (!is_array($_SESSION['quantity'])) {
            $_SESSION['quantity'] = array(); // Initialize as empty array if not
        }

        // Now you can safely access $_SESSION['quantity'] as an array
        if (!isset($_SESSION['quantity'][$prod->prod_id])) {
            $_SESSION['quantity'][$prod->prod_id] = 1;
        }
        ?>

                                                <tr>
                                                    <td>
                                                        <?php
                                                        if ($prod->prod_img) {
                                                            echo "<img src='../admin/assets/img/products/$prod->prod_img' height='60' width='60' class='img-thumbnail'>";
                                                        } else {
                                                            echo "<img src='../admin/assets/img/products/default.jpg' height='60' width='60' class='img-thumbnail'>";
                                                        }
                                                        ?>
                                                    </td>
                                                    <!-- <td><?php echo $prod->prod_code; ?></td> -->
                                                    <td><?php echo substr( $prod->prod_name,0,10)?></td>
                                                    <td>$ <?php echo $prod->prod_price; ?></td>
                                                    
                                                    <!-- <td>
                                                        <button class="btn btn-secondary" onclick="subQty(<?php echo $prod->prod_id; ?>)">-</button>
                                                        <span id="quantity-<?php echo $prod->prod_id; ?>"><?php echo isset($_SESSION['quantity'][$prod->prod_id]) ? $_SESSION['quantity'][$prod->prod_id] : 1; ?></span>
                                                        <button class="btn btn-primary" onclick="addQty(<?php echo $prod->prod_id; ?>)">+</button>
                                                        <input type="hidden" id="quantity-hidden-<?php echo $prod->prod_id; ?>" name="quantity" value="<?php echo isset($_SESSION['quantity'][$prod->prod_id]) ? $_SESSION['quantity'][$prod->prod_id] : 1; ?>">
                                                    </td> -->

                                                    <td>
                                                        <form action="orders.php" method="post">
                                                            <input type="hidden" name="customer_id"
                                                                value="<?php echo $_SESSION['customer_id']; ?>">
                                                            <input type="hidden" name="order_code"
                                                                value="<?php echo bin2hex(random_bytes('5')); ?>">
                                                            <input type="hidden" name="prod_price"
                                                                value="<?php echo $prod->prod_price; ?>">
                                                            <input type="hidden" name="prod_id"
                                                                value="<?php echo $prod->prod_id; ?>">
                                                            <input type="hidden" name="prod_name"
                                                                value="<?php echo $prod->prod_name; ?>">
                                                            <!-- Pass the quantity value to the form -->
                                                            <input type="hidden" id="quantity-hidden-<?php echo $prod->prod_id; ?>" name="quantity" value="<?php echo isset($_SESSION['quantity'][$prod->prod_id]) ? $_SESSION['quantity'][$prod->prod_id] : 1; ?>">
                                                            <button name="make" type="submit"
                                                                class="btn btn-sm btn-warning">
                                                                <i class="fas fa-cart-plus"></i>
                                                                 Order
                                                            </button>
                                                        </form>
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
            </div>
            <?php require_once('partials/_footer.php'); ?>
        </div>
    </div>
  
    <script>
        function addQty(productId) {
            updateQuantity(productId, 1);
        }

        function subQty(productId) {
            updateQuantity(productId, -1);
        }

        function updateQuantity(productId, delta) {
            var currentQuantity = parseInt(document.getElementById('quantity-' + productId).innerText);
            var newQuantity = currentQuantity + delta;
            if (newQuantity >= 1) {
                document.getElementById('quantity-' + productId).innerText = newQuantity;
                document.getElementById('quantity-hidden-' + productId).value = newQuantity;
                updateSessionQuantity(productId, newQuantity);
            } else {
                // If new quantity is less than 1, set it to 1
                document.getElementById('quantity-' + productId).innerText = 1;
                document.getElementById('quantity-hidden-' + productId).value = 1;
                updateSessionQuantity(productId, 1);
            }
        }

        function updateSessionQuantity(productId, newQuantity) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_session.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Session quantity updated successfully for product ID: ' + productId);
                    } else {
                        console.log('Error updating session quantity for product ID: ' + productId);
                    }
                }
            };
            xhr.send('product_id=' + productId + '&quantity=' + newQuantity);
        }
    </script>




    <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
