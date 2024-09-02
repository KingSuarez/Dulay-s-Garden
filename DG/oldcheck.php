

<?php

include 'connection.php';

session_start();

$user_id = $_SESSION['user_id'];


if(!isset($user_id)){
   header('location:login.php');
}
// else{
//     $overallTotal = 0;
//     if (isset($_SESSION["Checkout"])) {
//         foreach ($_SESSION["Checkout"] as $item) {
//             $overallTotal += ($item["price"] * $item["quantity"]);
//         }
//     }
//                                             }



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['terms'])) {
        echo "<script>alert('You need to check the box before proceeding.'); window.history.back();</script>";
    } else {
        $overallTotal = 0;
        if (isset($_SESSION['checkout_items']) && !empty($_SESSION['checkout_items'])) {
            foreach ($_SESSION['checkout_items'] as $item_id) {
                $show_products = $conn->prepare("SELECT * FROM `carts` WHERE id = ? AND user_id = ?");
                $show_products->execute([$item_id, $user_id]);

                if ($show_products->rowCount() > 0) {
                    $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
                    $quantity = $fetch_products['quantity'];
                    $price = $fetch_products['price'];
                    $total = $price * $quantity;
                    $overallTotal += $total;
                }
            }
        }

        if ($overallTotal <= 600) {
            // Pay in full
            echo "<script>alert('Paying in full...');</script>";
            // Add your payment processing logic here
            // You can redirect to a payment gateway or handle payment here directly
            // Example: header('location: payment_gateway.php');
        } else {
            // Use downpayment
            $downpayment = $overallTotal * 0.25;
            $balance = $overallTotal - $downpayment;
            echo "<script>alert('Using downpayment...');</script>";
            // Add your payment processing logic here for downpayment
            // Example: header('location: payment_gateway.php');
        }
    }
};

if (isset($_POST['Pay'])) {
    if (!isset($_SESSION['checkout_items']) || !is_array($_SESSION['checkout_items'])) {
        echo "No items selected for checkout.";
        exit();
    }

    $selected_items = $_SESSION['checkout_items'];
    $user_id = $_SESSION['user_id'];

    $overallTotal = 0;
    foreach ($selected_items as $item_id) {
        $show_products = $conn->prepare("SELECT * FROM `carts` WHERE id = ? AND user_id = ?");
        $show_products->execute([$item_id, $user_id]);

        if ($show_products->rowCount() > 0) {
            $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
            $quantity = $fetch_products['quantity'];
            $price = $fetch_products['price'];
            $total = $price * $quantity;
            $overallTotal += $total;
            
            $Downpayment = $overallTotal * 0.25;
            $Balance = $overallTotal - $Downpayment;
        }
    }

    // Calculate expiration date
    $expiration_date = date('Y-m-d', strtotime('+3 weeks'));

    // Insert order data into uorders table
    $stmt = $conn->prepare("INSERT INTO `uorders` (user_id, p_id, pname, quantity, Tprice, status, expiration_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($selected_items as $item_id) {
        $show_products = $conn->prepare("SELECT * FROM `carts` WHERE id = ? AND user_id = ?");
        $show_products->execute([$item_id, $user_id]);

        if ($show_products->rowCount() > 0) {
            $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
            $p_id = $fetch_products['p_id'];
            $pname = $fetch_products['pname'];
            $quantity = $fetch_products['quantity'];
            $Tprice = $fetch_products['price'] * $quantity;
            $status = "Pending";

            // Bind parameters and execute the insert query
            $stmt->execute([$user_id, $p_id, $pname, $quantity, $Tprice, $status, $expiration_date]);

            // Update the products table to decrease the stock
            $update_stock = $conn->prepare("UPDATE `products` SET stock = stock - ? WHERE id = ?");
            $update_stock->execute([$quantity, $p_id]);
        }
    }

    // After successful insertion, delete the selected items from the cart
    foreach ($selected_items as $item_id) {
        $delete_item = $conn->prepare("DELETE FROM `carts` WHERE id = ? AND user_id = ?");
        $delete_item->execute([$item_id, $user_id]);
    }

    // Clear the session data
    unset($_SESSION['checkout_items']);

    // Redirect to a success page or display a success message
    header('location:Ahome.php');
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="Ahomecss/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="Ahomecss/styles.css">


    <!-- Style Sheet for the specific page -->
    <link rel="stylesheet" type="text/css" href="Ahomecss/Categories-Style.css">
    <!-- Style sheet for the upper part of the page that is global for all page -->
    <link rel="stylesheet" type="text/css" href="Ahomecss/All-Style.css">
    <link rel="stylesheet"  type="text/css" href="Ahomecss/HomeStyle.css">

    <!-- Title of the Homepage -->
    <title>Dulay's Product Shop</title>
    <style>
         input[type="text"]{
            border: none;
        }
    </style>
</head>

 <!-- all contents are within this body Id Pagebody -->
<body>
<div id="PageBody">

<!-- div Class Allup contains div Classes such as Container, Homebar and HomebarBottom 1 & 2 -->
<div class="allUp">
<!-- div Class class contains div Classes such as Box1 with Id HomePanelUp  and Box2 with Id HomeProfile -->
<div class=container>
    <div class="box1" id="HomePanelUp">
    <a href="Homepage.php"><img style="margin-top: 10px;" src="Images/IMG_1210 1-1.png" width="190px"></></a>
    </div>

    <div class="box2" id="HomeProfile" style="text-decoration: none;"> 
    <div class="carts">
    <li id='shoppingcart' style="list-style-type:none; font-size:large; margin-top:5px;margin-right:15px; "><a href="Acart.php" style="color: black;"><span class="glyphicon glyphicon-shopping-cart "></span>Cart <span class="badge"></span></a></li>
    </div>
    <div id="Profile">
                 
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                 $select_profile->execute([$user_id]);
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <img  size="100px"  onclick="toggleMenu()" src="img/<?= $fetch_profile['image']; ?>" alt="img" >
            </div>
                               
            <div class="Profile-Sub-Menu-Wrap1" id="subMenu1">
            <div class="Sub-Menu1">
            <div class="user-info">
            <?php
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                 $select_profile->execute([$user_id]);
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <img  size="100px"  onclick="toggleMenu()" src="img/<?= $fetch_profile['image']; ?>" alt="img"  >
                <h2 style=" margin-top: 5px;  font-size: 20px;"><?= $fetch_profile['Fname'],'  ', $fetch_profile['Mname'], '  ', $fetch_profile['Lname'] ; ?></h2>    
                                </div>
                                <hr>
                            <a href="A_user_up.php" class="Sub-Menu-Link1" >
                                <img src="Homepage/Images/Profile.png" alt="">
                                <p>Edit Profile</p>
                                <span>></span>
                            </a>
                            <a href="logout.php" class="Sub-Menu-Link1" >
                                <img src="Homepage/Images/Logout.png" alt="">
                                <p>Log-out</p>
                                <span>></span>
                            </a>
            
                 </div>
                </div>
             </div>
       </div>



<div class="HomeBar">
<ul>
        <!-- list Class Active containing a link -->
        <li><a href="Ahome.php">HOME</a>

        </li>
                        <!-- lists Containing a link -->
        <li class="active"><a href="Ahomeshop.php">SHOP</a>
            <div class="Sub-1">
                <ul>
                    <li><a href="Aplants.php"class=a2>Plants</a></li>
                    <li><a href="Asoils.php"class=a2>Soils</a></li>
                    <li><a href="Apots.php"class=a2>Potters</a></li>
                    <li><a href="#"class=a2>Others</a></li>
                </ul>
            </div>
        </li>

        <li><a href="Top-Sales-Home.php">BEST-SELLERS</a>
            <!-- div Class Sub-1 containing Unorded list and list -->
            <div class="Sub-1">
                <!-- unordered list Containing lists -->
                <ul>
                    <!-- lists Containing a link -->
                    <li><a href="#"class=a2>All Stars</a></li>
                    <li><a href="#"class=a2> All BestBuys</a></li>
                    <li><a href="#" class=a2>Star Categorized</a></li>
                    <li><a href="#" class=a2>BestBuys Categorized</a></li>
                </ul>
            </div>
        </li>

       
 
</ul>
<br>


</div>
</div>


<div class="main-cart" style="background-color:antiquewhite; height:100%;">
<?php 

if (!isset($_SESSION['checkout_items']) || empty($_SESSION['checkout_items'])) {
    exit();
}

$selected_items = $_SESSION['checkout_items'];
$user_id = $_SESSION['user_id']; // Assuming you have the user ID stored in session
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="panel panel-primary text-center">
                <div class="panel-heading" style="background-color:#966e4494; font-size:26px">Paying Order</div>
                <div class="panel-body"></div>
                <div class="row" style="justify-content:center; font-size:18px; margin-left:23%;">
                    <div class="col-md-2"><b>Image</b></div>
                    <div class="col-md-2"><b>Product Name</b></div>
                    <div class="col-md-2"><b>Quantity</b></div>
                    <div class="col-md-2"><b>Total Price</b></div>
                </div>
                <br>

                <?php
                $overallTotal = 0; // Initialize overall total outside the loop

                foreach ($selected_items as $item_id) {
                    $show_products = $conn->prepare("SELECT * FROM `carts` WHERE id = ? AND user_id = ?");
                    $show_products->execute([$item_id, $user_id]);

                    if ($show_products->rowCount() > 0) {
                        $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
                        $quantity = $fetch_products['quantity'];
                        $price = $fetch_products['price'];
                        $total = $price * $quantity;
                        $overallTotal += $total;

                        $Downpayment = $overallTotal * 0.25;
                        $Balance = $overallTotal - $Downpayment;
                ?>
                        <div id='cartdetail' style="margin-left: 24%;">
                            <div class='row' style="text-align: center;">
                                <div class='col-md-2'><img src="prod/<?= $fetch_products['image']; ?>" width='60px' height='60px'></div>
                                <div class='col-md-2' style="padding-top:10px; text-align:center;"><b style="font-size: 14px; text-transform:capitalize"><?= $fetch_products['pname']; ?></b></div>
                                <div class='col-md-2'><input class='form-control qty' style="text-align:center;" type='text' min="1" size='10px' value='<?= $fetch_products['quantity']; ?>' disabled></div>
                                <div class='col-md-2'><input class='form-control price' style="text-align:center;" type='text' min="1" size='10px' value='<?= $total; ?>' disabled></div>
                            </div>
                        </div>
                        <hr>
                <?php
                    }
                }
                ?>
                <div class="row" style="margin-left:230px;">
                    <h2 class='col-md-3'> <b>Downpayment ₱<?= $Downpayment; ?></b></h2>
                    <h2 class='col-md-3' id="overallTotal"><b>Total: ₱<?= $overallTotal; ?></b></h2>
                    <h2 class='col-md-3'> <b>Balance ₱<?= $Balance; ?></b></h2>
                    <br><br><br><br><br>
                    <!-- Button trigger modal -->
                    <button class='btn btn-success btn-lg pull-center' id='checkout_btn' style="font-size:18px; margin-left:-240px;" data-toggle="modal" data-target="#termsModal">Pay Now</button>
                    <br><br>
                </div>
              
                <!-- Modal for Terms and Conditions -->
                <div id="termsModal" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Terms and Conditions</h4>
                            </div>
                            <div class="modal-body">
                                <h4 style="font-size: medium;">To secure your reservation, a non-refundable downpayment is required and must be paid online at the time of booking. This downpayment represents a portion of the total purchase price, with the remaining balance to be paid in full upon item pickup. Payment methods accepted online and at pickup include [list accepted payment methods]. Upon arrival for pickup, please present a valid ID and your reservation confirmation. Failure to pick up the item within [specified time period, e.g., 14 days] will result in cancellation of the reservation and forfeiture of the downpayment. For any changes or inquiries regarding your reservation, please contact us at [contact information] at least [specified time period, e.g., 48 hours] before the scheduled pickup date.</h4>
                            </div>
                            <div class="modal-footer">
                                <form action="" method="POST">
                                    <input type="checkbox" name="terms" style="margin-right:5px;" required> I accept the terms and conditions
                                    <button type="submit" class="btn btn-success" name="Pay">Accept and Pay</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal for Terms and Conditions -->
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>

</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js"></script>
	<script src="assets/bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>


<div id="whole">

    <div class="footer-con">
                        <img src="Images\Dulaysgardenbanner.jpg" alt="">
                        <div class="pre-footer-con">
                            <div class="pre-footer-con-inner">
                                    <h1>Sow the seeds of excitement!</h1>
                                    <h4>become a pioneer in our community of plant lovers.</h4>
                            </div>
                          
                        </div>
                        <div class="main-footer-con">
                            <div class="main-footer-con-inner">
                                <h4>OUR STORY</h4>
                                <p>We are committed to bringing plants within your reach by carefully selecting individual ones that enhance your space. You have the opportunity to pick up these chosen plants from our location. We'll provide you with care guides tailored to the specific needs of your selected plants, ensuring they not only survive but thrive in your care.</p>
                                <div class="main-footer-icon-con">
                                  
                                    <img src="Images\Facebook2.png" alt="" > 
                                   
                                   
                                    <img src="Images\Email2.png" alt="" > 
                                  
                                 
                                </div>   
                            </div>
                            <div class="main-footer-con-inner2">
                                <h4>PLANT GUIDES</h4>
                                <div class="main-footer-bookntitle-con">
                                    <img src="Images\Plant-Care.jpg" alt="">
                                    <div class="main-footer-title-con">
                                        <h5>Plant-Care</h5><br>
                                        <p>January 26,2024</p>
                                    </div>
                                </div>
                                <div class="main-footer-bookntitle-con">
                                <img src="Images\ProperPlacement.jpg" alt="">
                                    <div class="main-footer-title-con">
                                        <h5>Plant-Placement</h5><br>
                                        <p>January 26,2024</p>
                                    </div>
                                </div>
                            </div>
                            <div class="main-footer-con-inner3">
                                <h4>PRODUCT CATEGORIES</h4>
                                <div class="main-footer-categories-con">
                                    <ul>
                                        <li>Plants</li>
                                        <li>Seedlings</li>
                                        <li>Potters</li>
                                        <li>Pumice</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="main-footer-con-inner4">
                                <h4>USEFUL LINKS</h4>
                                <div class="main-footer-links-con">
                                    <ul>
                                        <li>Contact</li>
                                        <li>FAQs</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
    </section>
</div>
</div>  
 
</body>

<!-- JavaScript to handle form submission -->
<script>
    $(document).ready(function(){
        $("#checkout_btn").click(function(){
            if ($("input[name='terms']").is(':checked')) {
                // Form submission
                $("form").submit();
            } 
        });
    });
</script>
 <!-- Include jQuery -->
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Include Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('#PageBody').classList.add('activepage');
    });

    let subMenu = document.getElementById("subMenu1");
    function toggleMenu(){
        subMenu.classList.toggle("open-menu1");
    }
    </script>

    <script >
   
   
    </script>

</html>



<!-- New Checkout -->
<?php
include 'connection.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}

$downpayment = 0;
$balance = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Pay'])) {
    if (!isset($_POST['terms'])) {
        echo "<script>alert('You need to check the box before proceeding.'); window.history.back();</script>";
        exit();
    }

    if (!isset($_POST['payment_option']) || !in_array($_POST['payment_option'], ['full', 'downpayment'])) {
        echo "<script>alert('Invalid payment option.'); window.history.back();</script>";
        exit();
    }

    $payment_option = $_POST['payment_option'];
    $overallTotal = 0;

    if (isset($_SESSION['checkout_items']) && !empty($_SESSION['checkout_items'])) {
        foreach ($_SESSION['checkout_items'] as $item_id) {
            $show_products = $conn->prepare("SELECT * FROM `carts` WHERE id = ? AND user_id = ?");
            $show_products->execute([$item_id, $user_id]);

            if ($show_products->rowCount() > 0) {
                $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
                $quantity = $fetch_products['quantity'];
                $price = $fetch_products['price'];
                $total = $price * $quantity;
                $overallTotal += $total;
            }
        }
    }

    if ($payment_option == 'full') {
        $downpayment = $overallTotal;
        $balance = 0;
        echo "<script>alert('Paying in full: ₱$downpayment');</script>";
    } else {
        $downpayment = $overallTotal * 0.25;
        $balance = $overallTotal - $downpayment;
        echo "<script>alert('Paying with downpayment: ₱$downpayment');</script>";
    }

    $created_at = date('Y-m-d H:i:s');
    $expiration = date('Y-m-d H:i:s', strtotime('+1 month'));

    try {
        $conn->beginTransaction();

        // Insert into orders
        $order_sql = "INSERT INTO `orders` (user_id, payment, amount, balance, status, created_at, expiration) VALUES (?, ?, ?, ?, 'Pending', ?, ?)";
        $stmt = $conn->prepare($order_sql);
        $stmt->execute([$user_id, $payment_option, $overallTotal, $balance, $created_at, $expiration]);

        if ($stmt) {
            $order_id = $conn->lastInsertId();
            $data = [];

            $cart = $conn->prepare("SELECT c.*, p.id as pid, p.price FROM `carts` c INNER JOIN `products` p ON p.id = c.p_id WHERE c.user_id = ?");
            $cart->execute([$user_id]);

            while ($row = $cart->fetch(PDO::FETCH_ASSOC)) {
                $total = $row['price'] * $row['quantity'];
                $data[] = "('{$order_id}', '{$row['pid']}', '{$row['quantity']}', '{$row['price']}', '{$total}')";
            }

            if ($data) {
                $list_sql = "INSERT INTO `order_list` (o_id, p_id, quantity, price, total) VALUES " . implode(', ', $data);
                $save_olist = $conn->exec($list_sql);

                if ($save_olist) {
                    $empty_cart = $conn->prepare("DELETE FROM `carts` WHERE user_id = ?");
                    $empty_cart->execute([$user_id]);

                    // Update sales table
                    $sales_sql = "INSERT INTO `sales` (order_id, total_amount) VALUES (?, ?)";
                    $sales_stmt = $conn->prepare($sales_sql);
                    $sales_stmt->execute([$order_id, $overallTotal]);

                    $conn->commit();
                    echo json_encode(['status' => 'success']);
                    header("location: Acart.php");
                    exit();
                } else {
                    $conn->rollBack();
                    echo json_encode(['status' => 'failed', 'error' => 'Failed to save order list']);
                }
            } else {
                $conn->rollBack();
                echo json_encode(['status' => 'failed', 'error' => 'No cart items found']);
            }
        } else {
            $conn->rollBack();
            echo json_encode(['status' => 'failed', 'error' => 'Failed to insert order']);
        }
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'failed', 'error' => $e->getMessage()]);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="Ahomecss/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="Ahomecss/styles.css">


    <!-- Style Sheet for the specific page -->
    <link rel="stylesheet" type="text/css" href="Ahomecss/Categories-Style.css">
    <!-- Style sheet for the upper part of the page that is global for all page -->
    <link rel="stylesheet" type="text/css" href="Ahomecss/All-Style.css">
    <link rel="stylesheet"  type="text/css" href="Ahomecss/HomeStyle.css">

    <!-- Title of the Homepage -->
    <title>Dulay's Product Shop</title>
    <style>
         input[type="text"]{
            border: none;
        }
    </style>
</head>

 <!-- all contents are within this body Id Pagebody -->
<body>
<div id="PageBody">

<!-- div Class Allup contains div Classes such as Container, Homebar and HomebarBottom 1 & 2 -->
<div class="allUp">
<!-- div Class class contains div Classes such as Box1 with Id HomePanelUp  and Box2 with Id HomeProfile -->
<div class=container>
    <div class="box1" id="HomePanelUp">
    <a href="Homepage.php"><img style="margin-top: 10px;" src="Images/IMG_1210 1-1.png" width="190px"></></a>
    </div>

    <div class="box2" id="HomeProfile" style="text-decoration: none;"> 
    <div class="carts">
    <li id='shoppingcart' style="list-style-type:none; font-size:large; margin-top:5px;margin-right:15px; "><a href="Acart.php" style="color: black;"><span class="glyphicon glyphicon-shopping-cart "></span>Cart <span class="badge"></span></a></li>
    </div>
    <div id="Profile">
                 
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                 $select_profile->execute([$user_id]);
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <img  size="100px"  onclick="toggleMenu()" src="img/<?= $fetch_profile['image']; ?>" alt="img" >
            </div>
                               
            <div class="Profile-Sub-Menu-Wrap1" id="subMenu1">
            <div class="Sub-Menu1">
            <div class="user-info">
            <?php
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                 $select_profile->execute([$user_id]);
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <img  size="100px"  onclick="toggleMenu()" src="img/<?= $fetch_profile['image']; ?>" alt="img"  >
                <h2 style=" margin-top: 5px;  font-size: 20px;"><?= $fetch_profile['Fname'],'  ', $fetch_profile['Mname'], '  ', $fetch_profile['Lname'] ; ?></h2>    
                                </div>
                                <hr>
                            <a href="A_user_up.php" class="Sub-Menu-Link1" >
                                <img src="Homepage/Images/Profile.png" alt="">
                                <p>Edit Profile</p>
                                <span>></span>
                            </a>
                            <a href="logout.php" class="Sub-Menu-Link1" >
                                <img src="Homepage/Images/Logout.png" alt="">
                                <p>Log-out</p>
                                <span>></span>
                            </a>
            
                 </div>
                </div>
             </div>
       </div>



<div class="HomeBar">
<ul>
        <!-- list Class Active containing a link -->
        <li><a href="Ahome.php">HOME</a>

        </li>
                        <!-- lists Containing a link -->
        <li class="active"><a href="Ahomeshop.php">SHOP</a>
            <div class="Sub-1">
                <ul>
                    <li><a href="Aplants.php"class=a2>Plants</a></li>
                    <li><a href="Asoils.php"class=a2>Soils</a></li>
                    <li><a href="Apots.php"class=a2>Potters</a></li>
                    <li><a href="#"class=a2>Others</a></li>
                </ul>
            </div>
        </li>

        <li><a href="Top-Sales-Home.php">BEST-SELLERS</a>
            <!-- div Class Sub-1 containing Unorded list and list -->
            <div class="Sub-1">
                <!-- unordered list Containing lists -->
                <ul>
                    <!-- lists Containing a link -->
                    <li><a href="#"class=a2>All Stars</a></li>
                    <li><a href="#"class=a2> All BestBuys</a></li>
                    <li><a href="#" class=a2>Star Categorized</a></li>
                    <li><a href="#" class=a2>BestBuys Categorized</a></li>
                </ul>
            </div>
        </li>

       
 
</ul>
<br>


</div>
</div>


<div class="main-cart" style="background-color:antiquewhite; height:100%;">
<?php 

if (!isset($_SESSION['checkout_items']) || empty($_SESSION['checkout_items'])) {
    exit();
}

$selected_items = $_SESSION['checkout_items'];
$user_id = $_SESSION['user_id']; // Assuming you have the user ID stored in session
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="panel panel-primary text-center">
                <div class="panel-heading" style="background-color:#966e4494; font-size:26px">Paying Order</div>
                <div class="panel-body"></div>
                <div class="row" style="justify-content:center; font-size:18px; margin-left:23%;">
                    <div class="col-md-2"><b>Image</b></div>
                    <div class="col-md-2"><b>Product Name</b></div>
                    <div class="col-md-2"><b>Quantity</b></div>
                    <div class="col-md-2"><b>Total Price</b></div>
                </div>
                <br>

                <?php
                $overallTotal = 0; // Initialize overall total outside the loop

                foreach ($selected_items as $item_id) {
                    $show_products = $conn->prepare("SELECT * FROM `carts` WHERE id = ? AND user_id = ?");
                    $show_products->execute([$item_id, $user_id]);

                    if ($show_products->rowCount() > 0) {
                        $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
                        $quantity = $fetch_products['quantity'];
                        $price = $fetch_products['price'];
                        $total = $price * $quantity;
                        $overallTotal += $total;

                        $Downpayment = $overallTotal * 0.25;
                        $Balance = $overallTotal - $Downpayment;
                ?>
                        <div id='cartdetail' style="margin-left: 24%;">
                            <div class='row' style="text-align: center;">
                                <div class='col-md-2'><img src="prod/<?= $fetch_products['image']; ?>" width='60px' height='60px'></div>
                                <div class='col-md-2' style="padding-top:10px; text-align:center;"><b style="font-size: 14px; text-transform:capitalize"><?= $fetch_products['pname']; ?></b></div>
                                <div class='col-md-2'><input class='form-control qty' style="text-align:center;" type='text' min="1" size='10px' value='<?= $fetch_products['quantity']; ?>' disabled></div>
                                <div class='col-md-2'><input class='form-control price' style="text-align:center;" type='text' min="1" size='10px' value='<?= $total; ?>' disabled></div>
                            </div>
                        </div>
                        <hr>
                <?php
                    }
                }
                ?>
                <div class="row" style="margin-left:230px;">
                    <h2 class='col-md-3'> <b>Downpayment ₱<?= $Downpayment; ?></b></h2>
                    <h2 class='col-md-3' id="overallTotal"><b>Total: ₱<?= $overallTotal; ?></b></h2>
                    <h2 class='col-md-3'> <b>Balance ₱<?= $Balance; ?></b></h2>
                    <br><br><br><br><br>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#checkoutModal">
                        Checkout
                    </button>
                        <br><br>
                </div>
                            
                <!-- Modal Structure -->
                <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="checkoutModalLabel">Checkout</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="" method="post" id="checkout-form">
                                    <div class="form-group">
                                        <label for="terms">
                                            <input type="checkbox" id="terms" name="terms">
                                            I agree to the terms and conditions
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="payment-option">Payment Option:</label>
                                        <select id="payment-option" name="payment_option" class="form-control">
                                            <option value="full">Full Payment</option>
                                            <option value="downpayment" id="downpayment-option">Downpayment</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="Pay" class="btn btn-primary">Pay</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal for Terms and Conditions -->
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>

</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js"></script>
	<script src="assets/bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>


<div id="whole">

    <div class="footer-con">
                        <img src="Images\Dulaysgardenbanner.jpg" alt="">
                        <div class="pre-footer-con">
                            <div class="pre-footer-con-inner">
                                    <h1>Sow the seeds of excitement!</h1>
                                    <h4>become a pioneer in our community of plant lovers.</h4>
                            </div>
                          
                        </div>
                        <div class="main-footer-con">
                            <div class="main-footer-con-inner">
                                <h4>OUR STORY</h4>
                                <p>We are committed to bringing plants within your reach by carefully selecting individual ones that enhance your space. You have the opportunity to pick up these chosen plants from our location. We'll provide you with care guides tailored to the specific needs of your selected plants, ensuring they not only survive but thrive in your care.</p>
                                <div class="main-footer-icon-con">
                                  
                                    <img src="Images\Facebook2.png" alt="" > 
                                   
                                   
                                    <img src="Images\Email2.png" alt="" > 
                                  
                                 
                                </div>   
                            </div>
                            <div class="main-footer-con-inner2">
                                <h4>PLANT GUIDES</h4>
                                <div class="main-footer-bookntitle-con">
                                    <img src="Images\Plant-Care.jpg" alt="">
                                    <div class="main-footer-title-con">
                                        <h5>Plant-Care</h5><br>
                                        <p>January 26,2024</p>
                                    </div>
                                </div>
                                <div class="main-footer-bookntitle-con">
                                <img src="Images\ProperPlacement.jpg" alt="">
                                    <div class="main-footer-title-con">
                                        <h5>Plant-Placement</h5><br>
                                        <p>January 26,2024</p>
                                    </div>
                                </div>
                            </div>
                            <div class="main-footer-con-inner3">
                                <h4>PRODUCT CATEGORIES</h4>
                                <div class="main-footer-categories-con">
                                    <ul>
                                        <li>Plants</li>
                                        <li>Seedlings</li>
                                        <li>Potters</li>
                                        <li>Pumice</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="main-footer-con-inner4">
                                <h4>USEFUL LINKS</h4>
                                <div class="main-footer-links-con">
                                    <ul>
                                        <li>Contact</li>
                                        <li>FAQs</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
    </section>
</div>
</div>  
 
</body>
<script>
    // Assuming overallTotal is available in the JavaScript context
    var overallTotal = <?php echo $overallTotal; ?>;

    document.addEventListener("DOMContentLoaded", function() {
        var downpaymentOption = document.getElementById('downpayment-option');
        if (overallTotal <= 600) {
            downpaymentOption.disabled = true;
        }
    });
</script>
<!-- JavaScript to handle form submission -->
<script>
    $(document).ready(function(){
        $("#checkout_btn").click(function(){
            if ($("input[name='terms']").is(':checked')) {
                // Form submission
                $("form").submit();
            } 
        });
    });
    
</script>
 <!-- Include jQuery -->
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Include Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('#PageBody').classList.add('activepage');
    });

    let subMenu = document.getElementById("subMenu1");
    function toggleMenu(){
        subMenu.classList.toggle("open-menu1");
    }
    </script>

    <script>
   
   
    </script>

</html>
