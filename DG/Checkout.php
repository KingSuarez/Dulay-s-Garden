
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

    if (!isset($_POST['payment_option']) || !in_array($_POST['payment_option'], ['Fullpayment', 'Downpayment'])) {
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

    if ($payment_option == 'Fullpayment') {
        
        $downpayment = $overallTotal;
        $balance = 0;
        
        echo "<script>alert('Paying in Fullpayment: ₱$downpayment');</script>";
    } else {
        $downpayment = $overallTotal * 0.25;
        $balance = $overallTotal - $downpayment;
        echo "<script>alert('Paying with Downpayment: ₱$downpayment');</script>";
    }
    date_default_timezone_set('Asia/Manila');
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
                
                // Update the products table to decrease the stock
                $update_stock = $conn->prepare("UPDATE `products` SET stock = stock - ? WHERE id = ?");
                $update_stock->execute([$row['quantity'], $row['pid']]);
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
                    header("location: ureserve.php");
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

                            <a href="ureserve.php" class="Sub-Menu-Link1" >
                                <img src="Homepage/Images/Profile.png" alt="">
                                <p>Reserve Order</p>
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

        <li><a href="Best-seller.php">BEST-SELLERS</a>
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
                <div class="row" style="margin-left:250px;">
                    <!-- <?php if ($overallTotal < 600): ?>
                        <h2 class='col-md-3 ' id="overallTotal" style="display: flex; flex-direction: column; align-items: center;justify-content: center;margin-left: 230px;text-align: center;">
                            <b>Total: ₱<?= $overallTotal; ?></b></h2>
                    <?php else: ?>
                        <h2 class='col-md-3 '> <b>Downpayment ₱<?= $Downpayment; ?></b></h2>
                        <h2 class='col-md-3 ' id="overallTotal"><b>Total: ₱<?= $overallTotal; ?></b></h2>
                        <h2 class='col-md-3 '> <b>Balance ₱<?= $Balance; ?></b></h2>
                    <?php endif; ?> -->
                    <h2 class='col-md-3 ' id="overallTotal" style="display: flex; flex-direction: column; align-items: center;justify-content: center;margin-left: 230px;text-align: center;">
                    <b>Total: ₱<?= $overallTotal; ?></b></h2>
                    <br><br><br><br><br>
                    <!-- Button trigger modal -->
                    <button type="button" class='btn btn-success btn-lg pull-center' style="margin-left: -250px;" data-toggle="modal" data-target="#checkoutModal">
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
                                            <input type="checkbox" id="terms" name="terms" required>
                                            "I agree to the terms and conditions. There will be no refund on the downpayment,
                                            so please ensure that you claim your item in the store. If you have a balance,
                                             you must pay it at the cashier before claiming your item. 
                                             You have 30 days to claim your item before it expires. 
                                             There will be no option to reclaim or adjust the date to claim your item.
                                             The downpayment will appear if you have 600 above the total payment then it will 25% for downpayment."
                                        </label>
                                    </div>
                                    <div class="form-group" style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                                        <label for="payment-option">Payment Option:</label>
                                        <select id="payment-option" name="payment_option" class="form-control" onchange="updatePaymentDetails()">
                                            <option value="Fullpayment">Full Payment</option>
                                            <option id="downpayment-option" value="Downpayment">Downpayment</option>
                                        </select>
                                    </div>


                                    <div class="row" style="margin-left:250px;" id="payment-details">
                                    <h2 class="col-md-8" id="fullpayment" style="display: none;"><b>Total: ₱ <?= number_format($overallTotal, 2); ?></b></h2>
                                        <h2 class="col-md-8" id="downpayment" style="display: none;"><b>Downpayment: ₱ <?= number_format($Downpayment, 2); ?></b></h2>
                                        <h2 class="col-md-8" id="total" style="display: none;"><b>Total: ₱<?= number_format($overallTotal, 2);  ?></b></h2>

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
function updatePaymentDetails() {
    var paymentOption = document.getElementById('payment-option').value;
    var fullPaymentDiv = document.getElementById('fullpayment');
    var downpaymentDiv = document.getElementById('downpayment');
    var totalDiv = document.getElementById('total');
    var overallTotal = <?= $overallTotal; ?>;  // Get the total amount from PHP

    if (overallTotal < 600) {
        document.getElementById('downpayment-option').style.display = 'none';  // Hide the Downpayment option
        document.getElementById('payment-option').value = 'Fullpayment';  // Set default to Fullpayment
        fullPaymentDiv.style.display = 'block';
        downpaymentDiv.style.display = 'none';
        totalDiv.style.display = 'none';
    } else {
        document.getElementById('downpayment-option').style.display = 'block';  // Show the Downpayment option

        if (paymentOption === 'Fullpayment') {
            fullPaymentDiv.style.display = 'block';
            downpaymentDiv.style.display = 'none';
            totalDiv.style.display = 'none';
        } else if (paymentOption === 'Downpayment') {
            fullPaymentDiv.style.display = 'none';
            downpaymentDiv.style.display = 'block';
            totalDiv.style.display = 'block';
        }
    }
}

document.addEventListener("DOMContentLoaded", function() {
    updatePaymentDetails(); // Initialize based on the default or current selection
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
