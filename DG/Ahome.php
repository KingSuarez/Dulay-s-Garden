<?php
include 'connection.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

// if (isset($_POST['Pay'])) {
//     $user_id = $_SESSION['user_id'];

//     // Delete all items from the user's cart
//     $delete_all_cart_items = $conn->prepare("DELETE FROM `carts` WHERE user_id = ?");
//     $delete_all_cart_items->execute([$user_id]);

//     if ($delete_all_cart_items->rowCount() > 0) {
//         echo '<script>alert("All items have been successfully Paid from your cart.");</script>';
//     } else {
//         echo '<script>alert("Your cart is already empty.");</script>';
//     }
// }

// if (isset($_POST['Pay'])) {
//     if (!isset($_SESSION['checkout_items']) || !is_array($_SESSION['checkout_items'])) {
//         echo "No items selected for checkout.";
//         exit();
//     }

//     $selected_items = $_SESSION['checkout_items'];
//     $user_id = $_SESSION['user_id']; // Assuming you have the user ID stored in session

//     // Process payment logic here

//     // Calculate overall total again in case it changed during payment process
//     $overallTotal = 0;
//     foreach ($selected_items as $item_id) {
//         $show_products = $conn->prepare("SELECT * FROM `carts` WHERE id = ? AND user_id = ?");
//         $show_products->execute([$item_id, $user_id]);

//         if ($show_products->rowCount() > 0) {
//             $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
//             $quantity = $fetch_products['quantity'];
//             $price = $fetch_products['price'];
//             $total = $price * $quantity;
//             $overallTotal += $total;
            
//             $Downpayment = $overallTotal * 0.25;
//             $Balance = $overallTotal - $Downpayment;
//         }
//     }

//     // Insert order data into uorders table
//     $stmt = $conn->prepare("INSERT INTO `uorders` (user_id, p_id, pname, quantity, Tprice, status) VALUES (?, ?, ?, ?, ?, ?)");
    
//     foreach ($selected_items as $item_id) {
//         $show_products = $conn->prepare("SELECT * FROM `carts` WHERE id = ? AND user_id = ?");
//         $show_products->execute([$item_id, $user_id]);

//         if ($show_products->rowCount() > 0) {
//             $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
//             $p_id = $fetch_products['p_id'];
//             $pname = $fetch_products['pname'];
//             $quantity = $fetch_products['quantity'];
//             $Tprice = $fetch_products['price'] * $quantity;
//             $status = "Pending";

//             // Bind parameters and execute the insert query
//             $stmt->execute([$user_id, $p_id, $pname, $quantity, $Tprice, $status]);

//             // Update the products table to decrease the stock
//             $update_stock = $conn->prepare("UPDATE `products` SET stock = stock - ? WHERE id = ?");
//             $update_stock->execute([$quantity, $p_id]);
//         }
//     }

//     // After successful insertion, delete the selected items from the cart
//     foreach ($selected_items as $item_id) {
//         $delete_item = $conn->prepare("DELETE FROM `carts` WHERE id = ? AND user_id = ?");
//         $delete_item->execute([$item_id, $user_id]);
//     }

//     // Clear the session data
//     unset($_SESSION['checkout_items']);

//     // Redirect to a success page or display a success message
//     // echo "
// }

// if (isset($_POST['Pay'])) {
//     if (!isset($_SESSION['checkout_items']) || !is_array($_SESSION['checkout_items'])) {
//         echo "No items selected for checkout.";
//         exit();
//     }

//     $selected_items = $_SESSION['checkout_items'];
//     $user_id = $_SESSION['user_id']; // Assuming you have the user ID stored in session

//     // Process payment logic here

//     // Calculate overall total again in case it changed during the payment process
//     $overallTotal = 0;
//     foreach ($selected_items as $item_id => $quantity) {
//         $show_products = $conn->prepare("SELECT * FROM `carts` WHERE id = ? AND user_id = ?");
//         $show_products->execute([$item_id, $user_id]);

//         if ($show_products->rowCount() > 0) {
//             $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
//             $price = $fetch_products['price'];
//             $total = $price * $quantity;
//             $overallTotal += $total;
//         }
//     }

//     // Update product stock in the `products` table
//     foreach ($selected_items as $item_id => $quantity) {
//         $update_product = $conn->prepare("UPDATE `products` SET stock = stock - ? WHERE id = ?");
//         $update_product->execute([$quantity, $item_id]);
//     }

//     // Insert order data into `uorders` table
//     $stmt = $conn->prepare("INSERT INTO `uorders` (user_id, p_id, pname, quantity, Tprice, status) VALUES (?, ?, ?, ?, ?, ?)");
    
//     foreach ($selected_items as $item_id => $quantity) {
//         $show_products = $conn->prepare("SELECT pname FROM `carts` WHERE id = ? AND user_id = ?");
//         $show_products->execute([$item_id, $user_id]);

//         if ($show_products->rowCount() > 0) {
//             $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
//             $pname = $fetch_products['pname'];
//             $p_id = $item_id;
//             $Tprice = $price * $quantity;
//             $status = "Pending"; // You can set the status as per your requirements
            
//             // Bind parameters and execute the insert query
//             $stmt->execute([$user_id, $p_id, $pname, $quantity, $Tprice, $status]);
//         }
//     }

//     // After successful insertion, delete the selected items from the cart
//     foreach ($selected_items as $item_id => $quantity) {
//         $delete_item = $conn->prepare("DELETE FROM `carts` WHERE id = ? AND user_id = ?");
//         $delete_item->execute([$item_id, $user_id]);
//     }

//     // Clear the session data
//     unset($_SESSION['checkout_items']);

//     // Redirect to a success page or display a success message
//     // echo "Payment successful. Order placed.";
// }




?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0 ,maximum-scale=1">
                                    <!-- icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- Style sheet for the upper part of the page that is global for all page -->
        <link rel="stylesheet" type="text/css" href="Ahomecss/All-Style.css">
        <link rel="stylesheet" type="text/css" href="Ahomecss/Categories-Style.css">

        <!-- Style Sheet for the specific page -->
        <link rel="stylesheet" type="text/css" href="Ahomecss/HomeStyle.css">
<!-- Bootstrap 3.3.7 JS (add to your HTML file if not already included) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="style/modalfooter.css">

        <!-- Title of the Homepage -->
        <title>Dulay's Garden Home</title>
    <style>
        .Pn{
            text-align: center;
        }
        
/* For small screens (max-width: 480px) */  
@media only screen and (max-width: 480px) {  
  /* Adjust font sizes and margins for better readability */  
  body {  
   font-size: 12px;  
   zoom: 0.5;  

  }  
  h1, h2, h3, h4, h5, h6 {  
   font-size: 16px;  
  }  
  .allUp {  
   flex-direction: column;  
  }  
  .container {  
   width: 100%;  
  }  
  .box1, .box2 {  
   width: 100%;  
  }  
  .HomeBar {  
   width: 100%;  
  }  
  .HomeBar ul {  
   flex-direction: row;  
  }  
  .HomeBar li {  
   width: 100%;  
  }  
  #whole {  
   width: 100%;  
  }  
  #CategoriesSection {  
   width: 100%;  
  }  
  .CategoriesSection_inner {  
   flex-direction: row;  
  }  
  .card {  
   width: 100%;  
  }  
  #Allproductsearch {  
   width: 100%;  
  }  
  #productModal {  
   width: 100%;  
  }  
  .modal-content {  
   width: 100%;  
  }  
  .footer-con {  
   width: 100%;  
  }  
  .pre-footer-con {  
   width: 100%;  
  }  
  .main-footer-con {  
   width: 100%;  
  }  
  .main-footer-con-inner, .main-footer-con-inner2, .main-footer-con-inner3, .main-footer-con-inner4 {  
   width: 100%;  
  }  
}  
  
/* For extra small screens (max-width: 320px) */  
@media only screen and (max-width: 320px) {  
  /* Adjust font sizes and margins for better readability */  
  body {  
   font-size: 10px;  
   zoom: 0.3;  
  }  
  h1, h2, h3, h4, h5, h6 {  
   font-size: 14px;  
  }  
  .allUp {  
   flex-direction: column;  
  }  
  .container {  
   width: 100%;  
  }  
  .box1, .box2 {  
   width: 100%;  
  }  
  .HomeBar {  
   width: 100%;  
  }  
  .HomeBar ul {  
   flex-direction: row;  
  }  
  .HomeBar li {  
   width: 100%;  
  }  
  #whole {  
   width: 100%;  
  }  
  #CategoriesSection {  
   width: 100%;  
  }  
  .CategoriesSection_inner {  
   flex-direction: row;  
  }  
  .card {  
   width: 100%;  
  }  
  #Allproductsearch {  
   width: 100%;  
  }  
  #productModal {  
   width: 100%;  
  }  
  .modal-content {  
   width: 100%;  
  }  
  .footer-con {  
   width: 100%;  
  }  
  .pre-footer-con {  
   width: 100%;  
  }  
  .main-footer-con {  
   width: 100%;  
  }  
  .main-footer-con-inner, .main-footer-con-inner2, .main-footer-con-inner3, .main-footer-con-inner4 {  
   width: 100%;  
  }  
}

    </style>

        <!-- all contents are within this body Id Pagebody -->
    <body >
        <div id="PageBody">
            <!-- div Class Allup contains div Classes such as Container, Homebar and HomebarBottom 1 & 2 -->
            <div class="allUp">
    
        
<!-- div Class class contains div Classes such as Box1 with Id HomePanelUp  and Box2 with Id HomeProfile -->
<div class=container>
    <div class="box1" id="HomePanelUp">
    <a href="Ahome.php"><img style="margin-top: 10px;" src="Images/IMG_1210 1-1.png" width="190px"></a>
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
       

    
                    <!-- div Class Homebar contains Unordered list and list -->
                <div class="HomeBar">
                        <!-- unordered list Containing lists -->
                    <ul>
                            <!-- list Class Active containing a link -->
                        <li class="active"><a href="#">HOME</a>

                        </li>
                                        <!-- lists Containing a link -->
                        <li><a href="Ahomeshop.php">SHOP</a>
                            <div class="Sub-1">
                                <ul>
                                    <li><a href="Aplants.php"class=a2>Plants</a></li>
                                    <li><a href="Asoils.php"class=a2>Soils</a></li>
                                    <li><a href="Apots.php"class=a2>Potters</a></li>
                                    <li><a href="Afertilizer.php"class=a2>Fertilizer</a></li>
                                </ul>
                            </div>
                        </li>

                        <li><a href="Best-seller.php">BEST-SELLERS</a>
                        </li>

                       
                    </ul><br>
                </div>
            </div>

            <!-- div Id whole containing Section Id HomeSection and Section Id homeBotomSection -->
            <div id="whole">

                <br>
                <!-- Section Id HomeSection containing div Classes PostContainer -->
                <Section id="HomeSection" >

                    <div class="Homelayout">
                        <div class="TagnButton-con">
                            <h1>Indoor Elegance, Outdoor Bliss</h1>
                            <p>Dulay's Garden Collection</p>
                            <div class="Tag-Button-con">
                            <button onclick="location.href='Ahomeshop.php'">Get Plants</button>
                            </div>
                        </div>
                        <div class="Side-Picture-con">
                            <img src="Images\Garden 1 edited.jpg" alt="">
                        </div>
                    </div>

                    <div class="Homelayout2">
                        <h1>Find what you need</h1>
                        <div class="TagnLink-con">
                            <div class="picturnlink-con">
                                <div class="picture-con">
                                 <img src="Images\Pots[4].jpg" alt="" class="Child"><br>
                                </div>
                                 <a href="">Potters</a>
                            </div>
                            <div class="picturnlink-con">
                                <div class="picture-con">
                                 <img src="Images\Denborium.jpg" alt="" class="Child"><br>
                                </div>
                                <a href="">Flora</a>
                            </div>
                            <div class="picturnlink-con">
                                <div class="picture-con">
                                 <img src="Images\plants.jpg" alt="" class="Child"><br>
                                </div>
                                <a href="">Plants</a>
                            </div>
                            <div class="picturnlink-con">
                                <div class="picture-con">
                                 <img src="Images\Garden1c.jpg" alt="" class="Child"><br>
                                </div>
                                <a href="">All</a>
                            </div>
                        </div>
                    </div>

                    <div class="Homelayout3">
                        <h1>Suggested favorites</h1>
                      
                        <div class="scroller" data-direction="right" data-speed="slow">
                            <div class="scroller__inner">
                                <div class="Pn"><p>Bromeliads</p>
                                <img src="Images\Bromeliads.jpg" alt="" width="180px"/></div>
                                <div class="Pn"><p>Denborium</p>
                                <img src="Images\Denborium2.jpg" alt=""width="180px"/></div>
                                <div class="Pn"><p>Boncel</p>
                                <img src="Images\Boncel.jpg" alt="" width="180px"/></div>
                                <div class="Pn"><p>Fittonia</p>
                                <img src="Images\Fittonia.jpg" alt="" width="180px"/></div>
                                <div class="Pn"><p>Robusta</p>
                                <img src="Images\Robusta.jpg" alt="" width="180px"/></div>
                                <div class="Pn"><p>Syngonium</p>
                                <img src="Images\Syngonium.jpg" alt="" width="180px"/></div>
                            </div>
                        </div>
                        <div class="btnflex">
                            <button onclick="location.href='Ahomeshop.php'">Discover</button>
                        </div>
                    </div>
                    
                    <div class="Homelayout4">

                        <div class="IconnDes-con">
                            <div class="IconnDes-con-inner">
                                <img src="Images\Pickup-icon.png" alt="">
                                <h4>Pick-Up</h4>
                                <p>Your plants will be taken care of, until the day of your pick-up</p>
                            </div>
                            <div class="IconnDes-con-inner">
                                <img src="Images\Plant-icon.png" alt="">
                                <h4>Plant Library</h4>
                                <p>We are happy to see your plant grow, check our plant-library for your plant proper guides and placements</p>
                            </div>
                            <div class="IconnDes-con-inner">
                                <img src="Images\Root-icon.png" alt="">
                                <h4>Establish Roots</h4>
                                <p>All plants are nurtured and cared at our own nursery</p>
                            </div>
                        </div>

                    </div>
                    

                    <div class="Homelayout5">
                        <div class="Monpic-con">
                            <div class="Monpic-con-inner">
                                <img src="images/MonPic1.jpg" alt="">
                                <img src="images/MonPic2.jpg" alt="">
                                <img src="images/MonPic12.jpg" alt="">
                                <img src="images/MonPic3.jpg" alt="">
                                <img src="images/MonPic4.jpg" alt="">
                                <img src="images/MonPic11.jpg" alt="">
                                <img src="images/MonPic5.jpg" alt="">
                                <img src="images/MonPic6.jpg" alt="">
                                <img src="images/MonPic7.jpg" alt="">
                                <img src="images/MonPic10.jpg" alt="">
                                <img src="images/MonPic8.jpg" alt="">
                                <img src="images/MonPic9.jpg" alt="">
                            </div>
                        </div>
                        <div class="btnflex">
                        <button onclick="location.href='https://www.facebook.com/dulaysgarden'">Follow Us</button>
                        </div>
                    </div>


                </Section> 
                <br> 
                
                <!-- Section Id Home-bottomSection containing Section Class Dulays History -->
                <section id="Home-BottomSection">
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
                                        <li>Fertilizer</li>
                                    </ul>
                                </div>
                                <div>
    <h4>Location</h4>
 <!-- Button to open the modal -->
<div class="main-footer-links-con">
    <a href="#" role="link" style="color: black;" id="openModal">
        <i class="fa-solid fa-location-dot fa-2xl"></i> Brgy. Dila 4033 Bay, Philippines
    </a>
</div>

<!-- Modal Structure -->
<div id="locationModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h4>Store Location</h4>
        <div class="location"></div>
        <p> Address <br>
             Brgy. Dila 4033 Bay, Philippines.</p>
        <a href="https://www.google.com/maps/dir/?api=1&destination=14.179062476712%2C121.28703856665" target="_blank" class="btn">
            Get Direction
        </a>
    </div>
</div>

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
                </section>
        </div>
</div>
        
    </body>
    
    <script>
  // Get the modal
var modal = document.getElementById("locationModal");

// Get the button that opens the modal
var btn = document.getElementById("openModal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
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
    <script src="js/Ahomepage.js"></script>
    

</html>