<?php
// Start the session
session_start();

// Include necessary files
include 'connection.php';

// Fetch products from the database
$sql = "SELECT * FROM products"; // Assuming you have a products table
$stmt = $conn->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0 ,maximum-scale=1">

                                    <!-- icon -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Style sheet for the upper part of the page that is global for all page -->
        <link rel="stylesheet" type="text/css" href="Ahomecss/All-Style.css">
        <link rel="stylesheet" type="text/css" href="Ahomecss/Categories-Style.css">

        <!-- Style Sheet for the specific page -->
        <link rel="stylesheet" type="text/css" href="Ahomecss/HomeStyle.css">

        <!-- Title of the Homepage -->
        <title>Dulay's Garden Home</title>
    <style>
        .Pn{
            text-align: center;
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
    <a href="Homepage.php"><img style="margin-top: 10px;" src="Images/IMG_1210 1-1.png" width="190px"></></a>
    </div>

    <div class="box2" id="HomeProfile" style="text-decoration: none;"> 
    <div id="Profile">
        <a href="login.php" class="Reference"> LOGIN |</a>
        <a href="registration.php" class="Reference">REGISTER |</a>
       
        </div> 
    <!-- <div id="Profile">
                 
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
                            <a href="#" class="Sub-Menu-Link1" >
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
                </div> -->
             </div>
            
         </div>
       

    
                    <!-- div Class Homebar contains Unordered list and list -->
                <div class="HomeBar">
                        <!-- unordered list Containing lists -->
                    <ul>
                            <!-- list Class Active containing a link -->
                        <li class="active"><a href="index.php">HOME</a>

                        </li>
                                        <!-- lists Containing a link -->
                        <li><a href="index_shop.php">SHOP</a>
                            <div class="Sub-1">
                                <ul>
                                    <li><a href="index_plant.php"class=a2>Plants</a></li>
                                    <li><a href="index_soil.php"class=a2>Soils</a></li>
                                    <li><a href="index_pot.php"class=a2>Potters</a></li>
                                    <li><a href="index_fertilizer.php"class=a2>Fertilizer</a></li>
                                </ul>
                            </div>
                        </li>

                        <li><a href="index_best.php">BEST-SELLERS</a>
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
                            <button onclick="location.href='index_shop.php'">Get Plants</button>
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
                            <button onclick="location.href='index_shop.php'">Discover</button>
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
                            <button>Follow Us</button>
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
                                <!-- <div class="main-footer-icon-con">
                                    <img src="Images\Facebook2.png" alt="" >
                                    <img src="Images\Email2.png" alt="" >
                                </div>    -->
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
                                        <br>
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