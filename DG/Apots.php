<?php

include 'connection.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}


// Initialize alert count if not set
if (!isset($_SESSION['alert_count'])) {
    $_SESSION['alert_count'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['selected_items'])) {
        $_SESSION['checkout_items'] = $_POST['selected_items'];
        header("Location: checkout.php");
        exit();
    } else {
        // Set the alert only if the count is less than 10
        if ($_SESSION['alert_count'] < 1) {
            $_SESSION['show_alert'] = true;
            $_SESSION['alert_count']++;  // Increment alert count
        } else {
            $_SESSION['show_alert'] = false;
        }
    }
}

if (isset($_POST['Acart'])) {
    $product_id = $_POST['Acart'];

    // Check if the product is already in the cart
    $check_cart = $conn->prepare("SELECT * FROM `carts` WHERE user_id = ? AND p_id = ?");
    $check_cart->execute([$_SESSION['user_id'], $product_id]);

    if ($check_cart->rowCount() > 0) {
        // Product already in the cart, update the quantity
        $update_quantity = $conn->prepare("UPDATE `carts` SET quantity = quantity + 1 WHERE user_id = ? AND p_id = ?");
        $update_quantity->execute([$_SESSION['user_id'], $product_id]);
    } else {
        // Product not in the cart, insert a new row
        $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
        $select_products->execute([$product_id]);

        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            if ($select_products->rowCount() > 0) {
                $insert_products = $conn->prepare("INSERT INTO `carts`(user_id, p_id, pname, price, quantity, image) VALUES(?,?,?,?,?,?)");
                $insert_products->execute([$_SESSION['user_id'], $fetch_products['id'], $fetch_products['pname'], $fetch_products['price'], 1, $fetch_products['image']]);
            }
        }
    }
};

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Style Sheet for the specific page -->
    <link rel="stylesheet" type="text/css" href="Ahomecss/Categories-Style.css">
    <!-- Style sheet for the upper part of the page that is global for all page -->
    <link rel="stylesheet" type="text/css" href="Ahomecss/All-Style.css">
    <link rel="stylesheet" type="text/css" href="style/modal.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Title of the Homepage -->
    <title>Dulay's Product Shop</title>
</head>


<style>
 
.slider {
    position: relative;
    overflow: hidden;
    width: 100%;
    height: 100%;
}

.slides {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.slide {
    min-width: 100%;
    box-sizing: border-box;
}

.slide img {
    width: 100%;
    height: 323px;
    object-fit: contain;
}

.prev, .next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
}

.prev {
    left: 25px;
}

.next {
    right: 15px;
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
<body>
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
                    <li><a href="Afertilizer.php"class=a2>Fertilizer</a></li>
                </ul>
            </div>
        </li>

        <li><a href="Best-seller.php">BEST-SELLERS</a>
        </li>

       
 

    </ul><br>


</div>   
</div>  


<div id="whole">
<section id="CategoriesSection" style="border-radius: 0; margin-top: 0px; ">
<h3 style="font-family: Tangerine, cursive; color: black; font-size: 40px; text-align:center; font-weight: 900;">All Pots</h3>
<form method="GET" action="" style="text-align: right;">
    <input type="text" name="search" style="width: 210px;" placeholder="Search Product" value="<?= htmlspecialchars($search_query) ?>">
    <button type="submit" style="margin-right: 10px; padding: 10px; background-color: #966e4494; color: white; border: none; border-radius: 5px; cursor: pointer;">Search</button>
</form>

  <div class="CategoriesSection_con">

  <?php
// Search Filter
$sql = "SELECT * FROM `products` WHERE category = 'Pot'";
if (!empty($search_query)) {
    $sql .= " AND (pname LIKE :search OR detail LIKE :search OR careP LIKE :search)";
}
$sql .= " ORDER BY id DESC";

$show_products = $conn->prepare($sql);
if (!empty($search_query)) {
    $search_term = '%' . $search_query . '%';
    $show_products->bindParam(':search', $search_term, PDO::PARAM_STR);
}
$show_products->execute();

if ($show_products->rowCount() > 0) {
    while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
?>
     <div class="SpecificProductCard" data-stock="<?= $fetch_products['stock']; ?>" >
    <form action="" method="POST">
        <img src="prod/<?= $fetch_products['image']; ?>" alt="Product Image" name="image" class="img" style="width:100%; object-fit:cover;">
        <h1 style="font-size: 24px; font-family:poppins; font-weight:bolder;" class="pname" name="pname"><?= $fetch_products['pname'] ?></h1>
        <h6 style="padding: 0%;"><?= $fetch_products['category'] ?></h6>
        <h1 style="font-size: medium; background-color:bisque; position:left;">Stock: <?= $fetch_products['stock'] ?></h1>
        <h6 name="price">₱ <?= $fetch_products['price'] ?></h6>
        <input type="hidden" name="Product_id" value="<?= $fetch_products['id']; ?>">
        <div style="margin-right: 20px;">
            <p><button id="<?= $fetch_products['id']; ?>" class="Cart" value="<?= $fetch_products['id']; ?>" name="Acart" type="submit">Add To Cart</button></p>
            <p><button class="open-modal-btn" type="button" data-product-id="<?= $fetch_products['id']; ?>">View Product</button></p>
        </div>
    </form>
</div>

<?php
    }
} else {
    echo '<p class="empty"> No Found Product!</p>';
}
?>

</div>




<div id="productModal" class="modal" style="height: 100%; overflow-y: hidden;">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="modalProductName" class="Mname" style="text-transform: capitalize;"></h2>
        <hr>
        <form action="" method="POST">
            <div class="pcontainer">
                <div class="Pimage" style="width: 80%;">
                    <div class="slider">
                        <div class="slides">
                            <div class="slide"><img id="modalProductImage" src="" alt="Product Image" class="Mimage" style="width: 70%; height: 323px; object-fit: contain;"></div>
                            <div class="slide"><img id="modalProductImage1" src="" alt="Product Image" class="Mimage" style="width: 70%; height: 323px; object-fit: contain;"></div>
                            <div class="slide"><img id="modalProductImage2" src="" alt="Product Image" class="Mimage" style="width: 70%; height: 323px; object-fit: contain;"></div>
                        </div>
                        <button class="prev" onclick="moveSlide(event, -1)">&#10094;</button>
                        <button class="next" onclick="moveSlide(event, 1)">&#10095;</button>
                    </div>
                </div>
                <div class="Mright">
                    <input type="hidden" id="modalProductId" name="Product_id">
                    <b style="font-size: 18px;">Product Details</b>
                    <p id="modalProductDetail" class="Mdetail" style="text-align: justify; font-size: 13px;">Product Details</p>
                    <b style="font-size: 18px;">Plant Care</b>
                    <p id="modalProductCare" class="Mcare" style="text-align: justify; font-size: 13px;">Care Plant</p>
                    <h1 id="modalProductStock" class="Mstock" style="font-size: medium; position: left; padding: 0%; font-weight:900;"> <b>Stock: </b></h1>
                    <h4 id="modalProductPrice" class="Mprice" style="font-weight:900;">₱</h4>
                </div>
            </div>
            <p><button class="Cart" name="Acart" type="submit">Add To Cart</button></p>
        </form>
        
    </div>
</div>

    </Section> 

    <br> 
    <section id="Categories-BottomSection">



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
    </section>
</div>
</div>  

</body>


<!-- Sweeralert -->
<script>
        <?php if (isset($_SESSION['show_alert']) && $_SESSION['show_alert']): ?>

        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: "No Refund!",
                text: " no-refund.",
                icon: "warning",
                // showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Okay"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "Acart.php"; // Redirect to the cart page
                }
            });
        });
        <?php unset($_SESSION['show_alert']); endif; ?>

    </script>

<script>
    // JavaScript to hide products with stock = 0
    document.addEventListener("DOMContentLoaded", function() {
        // Get all product elements
        const products = document.querySelectorAll('.SpecificProductCard');

        // Loop through each product and check the stock attribute
        products.forEach(function(product) {
            const stock = product.getAttribute('data-stock');

            // Hide product if stock is 0
            if (parseInt(stock) === 0) {
                product.style.display = 'none';
            }
        });
    });
</script>

<script>
// Get the modal
var modal = document.getElementById("productModal");

// Get the button that opens the modal
var btns = document.querySelectorAll(".open-modal-btn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btns.forEach(function(btn) {
    btn.onclick = function() {
        var productId = this.getAttribute("data-product-id");

        // Fetch product details using AJAX
        fetch('get_product_details.php?id=' + productId)
            .then(response => response.json())
            .then(product => {
                // Populate modal content
                document.getElementById("modalProductName").innerText = product.pname;
                document.getElementById("modalProductImage").src = 'prod/' + product.image;
                document.getElementById("modalProductImage1").src = 'prod/' + product.image2;
                document.getElementById("modalProductImage2").src = 'prod/' + product.image3;
                document.getElementById("modalProductDetail").innerText = product.detail;
                document.getElementById("modalProductCare").innerText = product.careP;
                document.getElementById("modalProductStock").innerText = "Stock: " + product.stock;
                document.getElementById("modalProductPrice").innerText = '₱ ' + product.price;
                document.getElementById("modalProductId").value = product.id; // Set hidden input value

                modal.style.display = "block";
            })
            .catch(error => console.error('Error fetching product details:', error));
    }
});

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

let currentSlide = 0;

function showSlide(index) {
    const slides = document.querySelector('.slides');
    const totalSlides = document.querySelectorAll('.slide').length;

    // Update currentSlide within bounds
    if (index >= totalSlides) {
        currentSlide = totalSlides - 1;
    } else if (index < 0) {
        currentSlide = 0;
    } else {
        currentSlide = index;
    }

    // Update slide position
    const offset = -currentSlide * 100;
    slides.style.transform = `translateX(${offset}%)`;

    // Show or hide arrows based on current slide
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');

    if (currentSlide === 0) {
        prevButton.style.display = 'none';
    } else {
        prevButton.style.display = 'block';
    }

    if (currentSlide === totalSlides - 1) {
        nextButton.style.display = 'none';
    } else {
        nextButton.style.display = 'block';
    }
}

function moveSlide(event, direction) {
    event.preventDefault();
    showSlide(currentSlide + direction);
}

document.addEventListener('DOMContentLoaded', () => {
    showSlide(currentSlide);
});

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
    
    <!-- <script>
        cart_count();

function cart_count(){
    $.ajax({
        url: 'action.php',
        method: 'POST',
        data: {cartcount:1},
        success: function(data){
            $('.badge').html(data);
            $('.badge').fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
        }
    })
}
    </script> -->
    </html>