<?php

include 'connection.php';

session_start();

$user_id = $_SESSION['user_id'];


if(!isset($user_id)){
   header('location:login.php');
}

 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['selected_items'])) {
        $_SESSION['checkout_items'] = $_POST['selected_items'];
        header("Location: Checkout.php");
        exit();
        }
    // } else {
    //     //localhost will display or confirmation
    //    echo '<script>alert("Success")</script>';
    // }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $user_id = $_SESSION['user_id'];

        // Update the quantity in the database
        $update_cart = $conn->prepare("UPDATE `carts` SET quantity = ? WHERE id = ? AND user_id = ?");
        $update_cart->execute([$quantity, $product_id, $user_id]);

        // Optionally, update the session cart if you are using sessions
        foreach ($_SESSION['checkout_items'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity'] = $quantity;
                break;
            }
        }

        // Respond to the AJAX request
        echo json_encode(['success' => true]);
        exit();
    }
}

$user_id = $_SESSION['user_id']; // Assuming you have the user ID stored in session

if(isset($_GET['delete'])){

    $delete_id = $_GET['delete'];
    $delete_products = $conn->prepare("DELETE FROM `carts` WHERE id = ?");
    $delete_products->execute([$delete_id]);
 
    header('location:Acart.php');
 }


 if (isset($_POST['checkout_btn'])) {
    if (!isset($_POST['check'])) {
        echo '<script>alert("Please check the Agreement of terms and condition if you agree");</script>';
    } else {
        echo '<script>alert("Thanks for your order in this online store. You can contact us on Facebook and Instagram.");</script>';
    }
};

// if (isset($_GET['id'])) {
//     // Your code that uses $_GET['id']
//     if (!in_array($_GET['id'], $_SESSION['cart'])) {
//         // Add $_GET['id'] to $_SESSION['cart'] if it's not already there
//         $_SESSION['cart'][] = $_GET['id'];
//         $_SESSION['message'] = 'Product added to cart';
//     }
// } else {
//     // Handle the case where $_GET['id'] is not set or empty
//     // For example, display an error message or redirect to another page
//     echo 'Error: Product ID is not set';
// }


//  if(isset($_POST['Acart'])){


//     $Product_id = $_GET['id'];
//     $Product_img = $_POST['image'];
//     $Product_name = $_POST['pname'];
//     $Product_price = $_POST['price'];
//     $_SESSION['cart'][] = array('productId'=>$Product_id, 
//                                 'productImg'=>$Product_img, 
//                                 'productName'=>$Product_name, 
//                                 'productPrice'=>$Product_price, 
//                                 'Productqty'=>$Product_qty ,
//                                 print_r($_SESSION['cart'])
//                              );
    
//     };
// if(isset($_POST['Acart'])){


//     $Product_id = $_GET['id'];
//     $Product_img = $_POST['image'];
//     $Product_name = $_POST['pname'];
//     $Product_price  = $_POST['price'];
//     $Product_qty = $_POST['quantity'];
//     $_SESSION['cart'][] = array('productId'=>$Product_id, 
//                                 'productImg'=>$Product_img, 
//                                 'productName'=>$Product_name, 
//                                 'productId'=>$Product_qty,
//                                 'productPrice'=>$Product_price
                             
//                                  );
//     };



// if (isset($_POST['Acart'])) {
//     $product_id = $_POST['Product_id'];

//     // Check if the product is already in the cart
//     $check_cart = $conn->prepare("SELECT * FROM `carts` WHERE user_id = ? AND p_id = ?");
//     $check_cart->execute([$_SESSION['user_id'], $product_id]);

//     if ($check_cart->rowCount() > 0) {
//         // Product already in the cart, update the quantity
//         $update_quantity = $conn->prepare("UPDATE `carts` SET quantity = quantity + 1 WHERE user_id = ? AND p_id = ?");
//         $update_quantity->execute([$_SESSION['user_id'], $product_id]);
//     } else {
//         // Product not in the cart, insert a new row
//         $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
//         $select_products->execute([$product_id]);

//         if ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
//             $insert_products = $conn->prepare("INSERT INTO `carts` (user_id, p_id, pname, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
//             $insert_products->execute([$_SESSION['user_id'], $fetch_products['id'], $fetch_products['pname'], $fetch_products['price'], 1, $fetch_products['image']]);
//         }
//     }
// }


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
    <title>Dulay's Cart Shop</title>
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
                    <li><a href="Afertilizer.php"class=a2>fertilizer</a></li>
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


<div class="container-fluid">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <form action="Acart.php" method="POST">
                    <div class="panel panel-primary text-center">
                        <div class="panel-heading" style="background-color:#966e4494;">Cart Checkout</div>
                        <br>
                        <div class="row" style="text-align:center; font-size:15px;">
                            <div class="col-md-2"><b>Action</b></div>
                            <div class="col-md-2"><b>Product Image</b></div>
                            <div class="col-md-2"><b>Product Name</b></div>
                            <div class="col-md-2" style="right:1.5%;"><b>Product Price</b></div>
                            <div class="col-md-2" style="right:1.5%;"><b>Quantity</b></div>
                            <div class="col-md-2" style="right:1.5%;"><b>Amount ₱</b></div>
                        </div>
                        <br>
                        <?php
                        $show_products = $conn->prepare("SELECT * FROM `carts` WHERE user_id = ?");
                        $show_products->execute([$user_id]);
                        $overallTotal = 0;

                        if ($show_products->rowCount() > 0) {
                            while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
                                $quantity = $fetch_products['quantity'];
                                $price = $fetch_products['price'];
                                $total = $price * $quantity;
                                $overallTotal += $total;
                        ?>
                        <div id='cartdetail'>
                            <div class='row' style="text-align: center;">
                                <div class='col-md-2' style="margin-top: 10px;">
                                    <input type="checkbox" name="selected_items[]" value="<?= $fetch_products['id']; ?>" class="item-checkbox" checked onclick="updateOverallTotal()">
                                    <a href="Acart.php?delete=<?= $fetch_products['id']; ?>" onclick="return confirm('Delete this item?');" class='btn btn-danger'><span class='glyphicon glyphicon-trash'></span></a>
                                </div>
                                <div class='col-md-2'><img src="prod/<?=$fetch_products['image'];?>" width='60px' height='60px'></div>
                                <div class='col-md-2' style="padding-top:10px;"><h4><?= $fetch_products['pname']?></h4></div>
                                <div class='col-md-2'><input class='form-control price' style="text-align: center;" type='text' value='<?=$fetch_products['price']?>' disabled></div>
                                <div class='col-md-2'><input class='form-control qty' name="qty" style="text-align: center;" type='number' min="1" value='<?= $fetch_products['quantity'] ?>' onchange="subTotal(this)"></div>
                                <div class='col-md-2'><input class='form-control total' style="text-align: center;" type='text' value='<?=$total?>' disabled></div>
                            </div>
                        </div>
                        <hr>
                        <?php
                            }
                        }
                        ?>
                        <div class="panel-footer">
                            <div class="row" style="align-content: right;">
                                <div class="col-md-4" style="margin-left:780px;">
                                    <b><h2 style="margin-left:120px;" id="overallTotal">Total: ₱ <?=$overallTotal; ?></h2></b>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class='btn btn-success btn-lg pull-right' id='checkout_btn' style="margin-right: 20px;">Checkout</button>
                    </div>
                </form>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>


</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js"></script>
	<script src="assets/bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
    <!-- <script src="js/main.js"></script> -->


<div id="whole">

    <div class="footer-con">
                        <img src="Images\Dulaysgardenbanner.jpg" alt="">
                        <div class="pre-footer-con">
                            <div class="pre-footer-con-inner">
                                    <h1>Sow the seeds of excitement!</h1>
                                    <h4>become a pioneer in our community of plant lovers.</h4>
                            </div>
                            <!-- <div class="pre-footer-con-inner2">
                                <form action="" method="get">
                                    <input type="text" placeholder="Your email" class="footerinput">
                                    <button type="submit">Sign-Up</button>
                                </form>
                            </div> -->
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
                                    <li><h4>Contact </h4></li><br>
                                        <div>
                                        <a href="https://www.messenger.com/t/100057185270623"><i class="fa-brands fa-facebook-messenger fa-2xl"></i> Dulay's Garden </a> <br> <br>
                                        <a href="https://mail.google.com/mail/u/0/#inbox?compose=new"><i class="fa-solid fa-envelope fa-2xl"></i> Dulay's Garden Email</a>
                                    </div>
                                        <li>FAQs</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
</div>
</div>  
 
</body>
<!-- <script>
    function updateTotal(itemId) {
        // Get the quantity input value
        let quantity = document.querySelector(`input.qty[data-itemid="${itemId}"]`).value;
        // Calculate the new total
        let price = parseFloat(document.querySelector(`input.price[data-itemid="${itemId}"]`).value);
        let newTotal = quantity * price;
        // Update the total input value
        document.querySelector(`input.total[data-itemid="${itemId}"]`).value = newTotal;
    }
</script> -->
<!-- <script>
    function subTotal(input) {
        var price = input.parentNode.previousElementSibling.querySelector('.price').value;
        var qty = input.value;
        var total = price * qty;

        input.parentNode.nextElementSibling.querySelector('.total').value = total.toFixed(2);
        updateOverallTotal();
    }

    function updateOverallTotal() {
        var totalInputs = document.querySelectorAll('.total');
        var overallTotal = 0;

        totalInputs.forEach(function(input) {
            var row = input.closest('.row');
            var checkbox = row.querySelector('.item-checkbox');
            if (checkbox.checked) {
                overallTotal += parseFloat(input.value.replace(/,/g, ''));
            }
        });

        document.getElementById('overallTotal').textContent = 'Total: ₱' + overallTotal.toFixed(2);
    }
</script> -->

<script>
            function subTotal(input) {
            var price = input.parentNode.previousElementSibling.querySelector('.price').value;
            var qty = input.value;
            var total = price * qty;

            // Format total with commas for thousands separators
            var formattedTotal = total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

            input.parentNode.nextElementSibling.querySelector('.total').value = formattedTotal;
            updateOverallTotal();

            // Send an AJAX request to update the quantity in the session or database
            var productId = input.closest('.row').querySelector('.item-checkbox').value;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("product_id=" + productId + "&quantity=" + qty);
        }

        function updateOverallTotal() {
            var totalInputs = document.querySelectorAll('.total');
            var overallTotal = 0;

            totalInputs.forEach(function(input) {
                var row = input.closest('.row');
                var checkbox = row.querySelector('.item-checkbox');
                if (checkbox.checked) {
                    overallTotal += parseFloat(input.value.replace(/,/g, ''));
                }
            });

            // Format overallTotal with commas for thousands separators
            var formattedOverallTotal = overallTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

            document.getElementById('overallTotal').textContent = 'Total: ₱' + formattedOverallTotal;
        }
    </script>



<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('#PageBody').classList.add('activepage');
    });

    let subMenu = document.getElementById("subMenu1");
    function toggleMenu(){
        subMenu.classList.toggle("open-menu1");
    }
    </script>

    <script type="text/javascript">
    </script>



    </html>