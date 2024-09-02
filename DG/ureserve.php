<?php
include 'connection.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}
// Check if the form has been submitted
if (isset($_POST['cancel_order']) && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    try {
        // Begin a transaction to ensure atomicity
        $conn->beginTransaction();

        // Prepare the SQL statement to update the order status to 'Cancelled'
        $sql = "UPDATE `orders` SET `status` = 'Cancelled' WHERE `id` = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);

        // Execute the status update statement
        if ($stmt->execute()) {
            // Fetch the order items from the order_list table
            $order_items_stmt = $conn->prepare("SELECT p_id, quantity FROM `order_list` WHERE o_id = ?");
            $order_items_stmt->execute([$order_id]);

            // Update the stock for each product in the order
            while ($item = $order_items_stmt->fetch(PDO::FETCH_ASSOC)) {
                $update_stock_stmt = $conn->prepare("UPDATE `products` SET `stock` = `stock` + ? WHERE `id` = ?");
                $update_stock_stmt->execute([$item['quantity'], $item['p_id']]);
            }

            // Commit the transaction after successfully updating the status and stock
            $conn->commit();

            // Redirect or output success message
            echo "<script>alert('Order cancelled successfully.');</script>";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Rollback the transaction if the status update fails
            $conn->rollBack();
            echo "<script>alert('Error canceling the order.');</script>";
        }
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs during the process
        $conn->rollBack();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0 ,maximum-scale=1">

                                    <!-- icon -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- Style sheet for the upper part of the page that is global for all page -->
        <link rel="stylesheet" type="text/css" href="Ahomecss/ureserve.css">
        <link rel="stylesheet" type="text/css" href="Ahomecss/Categories-Style.css">
        <!-- Style Sheet for the specific page -->
        <link rel="stylesheet" type="text/css" href="Ahomecss/HomeStyle.css">

<!-- jQuery Library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>


        <!-- Title of the Homepage -->
        <title>My Order</title>
    <style>
        .Pn{
            text-align: center;
        }
        .conr{
            width: 100%;
        background-color: white;
        padding: 10px;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            
        }

        table th {
            text-align: center;
            background-color: #f2f2f2;
        }

        .status-form {
            display: inline;
        }
        
        .vbtn {
    padding: 8px 15px;
    border-radius: 15px;
    border: none;
    background-color: #04AA6D;
    color: white;
    font-size: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  .cbtn {
    padding: 8px 15px;
    border-radius: 15px;
    border: none;
    background-color: #f44336;
    color: white;
    font-size: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
/* Referring to the button search when it is hovered*/
.vbtn:hover {
    background-color: #d37e42;
    transform: scale(1.1); 
  }
  
.cbtn:hover {
    background-color: #d37e42;
    transform: scale(1.1); 
  }

  .modal-dialog {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(90% - 1.75rem);
}

.modal-content {
    width: 100%;
    max-width: 800px; /* Adjust as needed */
}

.tablist {
    display: flex;
    justify-content: space-around;
    border-bottom: 2px solid #f2f2f2;
    margin-bottom: 20px;
}

.tab-button {
    background-color: transparent;
    border: none;
    outline: none;
    padding: 10px 20px;
    cursor: pointer;
    font-size: 16px;
    color: #555;
    position: relative;
}

.tab-button:hover {
    color: #674636; /* Change to your theme color */
}

.tab-button:focus {
    color: #674636;
    border-bottom: 2px solid #674636;
}

.tab-button:active {
    color: #674636;
    border-bottom: 2px solid #674636;
}

.tab-button.active {
    color: #674636;
    border-bottom: 2px solid #674636;
}

.tab-button::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background-color: transparent;
    transition: background-color 0.3s ease;
}

.tab-button:hover::after,
.tab-button:focus::after,
.tab-button:active::after,
.tab-button.active::after {
    background-color: #674636;
}

    </style>

    <body >
        <div id="PageBody">
            <!-- div Class Allup contains div Classes such as Container, Homebar and HomebarBottom 1 & 2 -->
            <div class="allUp">
    
        
<!-- div Class class contains div Classes such as Box1 with Id HomePanelUp  and Box2 with Id HomeProfile -->
<div class=container>
    <div class="box1" id="HomePanelUp">
    <a href="Ahomepage.php"><img style="margin-top: 10px;" src="Images/IMG_1210 1-1.png" width="190px"></a>
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
                        <li class=""><a href="Ahome.php">HOME</a>

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
            <div >

                <br>
                <!-- Section Id HomeSection containing div Classes PostContainer -->
                <section id="HomeSection" style="height: fit-content;">
                <br> 
    <div class="card-body" style="background-color: white;     min-height: 1px; padding: 1.95rem;" >
                <h2 class="card-title" style="text-align: center; color: #674636;"><b>My Orders</b></h2>
                <br>

                <div class="tablist">
    <form method="GET" action="">
        <input type="hidden" name="status" value="Pending">
        <button type="submit" class="tab-button" id="tab-pending">Pending</button>
    </form>
    <form method="GET" action="">
        <input type="hidden" name="status" value="Processing">
        <button type="submit" class="tab-button" id="tab-processing">Processing</button>
    </form>
    <form method="GET" action="">
        <input type="hidden" name="status" value="Ready-To-Pick-Up">
        <button type="submit" class="tab-button" id="tab-ready">Ready To Pick Up</button>
    </form>
    <form method="GET" action="">
        <input type="hidden" name="status" value="Completed">
        <button type="submit" class="tab-button" id="tab-completed">Completed</button>
    </form>
    <form method="GET" action="">
        <input type="hidden" name="status" value="Cancelled">
        <button type="submit" class="tab-button" id="tab-cancelled">Cancelled</button>
    </form>
    <form method="GET" action="">
        <input type="hidden" name="status" value="Unclaim">
        <button type="submit" class="tab-button" id="tab-unclaimed">Unclaimed</button>
    </form>
</div>

</div>

                <div class="conr" >
                    <table id="orderTable" class="display table table-bordered table-stripped">
                        <colgroup>
                            <col width="5%">
                            <col width="10%">
                            <col width="15%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="9%">
                        </colgroup>
                        <thead >
                            <tr >
                                <th>#</th>
                                <th>Date Order</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Balance</th>
                                <th>Expiration</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                        // Start session and check user ID as before
                        $user_id = $_SESSION['user_id'];

                        // Set default status to 'Pending' if no status filter is applied
                        $status_filter = isset($_GET['status']) ? $_GET['status'] : 'Pending';

                        // Prepare the query to filter orders for the logged-in user based on the status
                        $qry = $conn->prepare("SELECT o.*, CONCAT(c.Fname, ' ', c.Lname) AS users 
                                                FROM `orders` o 
                                                INNER JOIN users c ON c.id = o.user_id 
                                                WHERE o.user_id = :user_id AND o.status = :status 
                                                ORDER BY unix_timestamp(o.created_at) DESC");
                        $qry->bindParam(':status', $status_filter, PDO::PARAM_STR);
                        $qry->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                        $qry->execute();

    $i = 1;
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)):
    ?>
    <tr>
        <td class="text-center"><?php echo $i++; ?></td>
        <td><?php echo htmlspecialchars(date("Y-m-d", strtotime($row['created_at']))); ?></td>
        <td style="text-transform:capitalize;"><?php echo htmlspecialchars($row['users']); ?></td>
        <td class="text-center"> ₱ <?php echo number_format($row['amount'],2); ?></td>
        <td class="text-center">₱ <?php echo htmlspecialchars($row['balance'],2); ?></td>
        <td><?php echo htmlspecialchars(date("Y-m-d", strtotime($row['expiration']))); ?></td>

        <td class="text-center">
            <?php
            switch ($row['status']) {
                case 'Pending':
                    echo "<span class='badge badge-light'>Pending</span>";
                    break;
                case 'Processing':
                    echo "<span class='badge badge-primary'>Processing</span>";
                    break;
                case 'Ready-To-Pick-Up':
                    echo "<span class='badge badge-primary'>Ready-To-Pick-Up</span>";
                    break;
                case 'Completed':
                    echo "<span class='badge badge-success'>Completed</span>";
                    break;
                case 'Cancelled':
                    echo "<span class='badge badge-danger'>Cancelled</span>";
                    break;
                case 'Unclaim':
                    echo "<span class='badge badge-secondary'>Unclaim</span>";
                    break;
            }
            ?>
        </td>

        <td class="text-center">
            <?php if ($row['payment'] == 'Fullpayment'): ?>
                <span class="badge badge-success">Full payment</span>
            <?php else: ?>
                <span class="badge badge-light">Downpayment</span>
            <?php endif; ?>
        </td>

        <td align="center">
        <div class="btn-group">
            <div>
                <button type="button" class="vbtn" data-id="<?php echo $row['id']; ?>" onclick="viewOrderDetails(<?php echo $row['id']; ?>)">View</button>
                <?php if ($row['status'] !== 'Completed' && $row['status'] !== 'Cancelled' && $row['status'] !== 'Unclaimed') { ?>
                    <form action="" method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <button type="submit" name="cancel_order" onclick="return confirm('Are you sure you want to cancel this order?');" class="cbtn">Cancel</button>
                    </form>
                <?php } ?>
            </div>
        </div>
        </td>
    </tr>
    <?php endwhile; ?>
</tbody>

                    </table>
                </div>
            </section>
            
   
        <!-- Order Details Modal -->
        <div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div > <h2 style="color: #765827; text-align:center; font-weight: 700;"> Dulay's Garden </h2>
                        </div>

                        <h3 class="modal-title" id="orderDetailsModalLabel"> <b>Order List </b></h3>
                    </div>
                    <div class="modal-body">
                        <!-- Content will be loaded here from AJAX -->
                        <div id="orderDetailsContent"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


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

$(document).ready(function () {
    $("#orderTable").DataTable();
});
</script>

    <script>
        function viewOrderDetails(order_id) {
            $.ajax({
                url: 'view_uorder.php',
                type: 'GET',
                data: { id: order_id },
                success: function(response) {
                    $('#orderDetailsContent').html(response);
                    $('#orderDetailsModal').modal('show');
                },
                error: function() {
                    alert('Failed to retrieve order details. Please try again.');
                }
            });
        }
    </script>
    
    <script>
    // Function to activate the correct tab based on the current status
    function activateTab(status) {
        const tabs = document.querySelectorAll('.tab-button');
        tabs.forEach(tab => tab.classList.remove('active'));

        if (status === 'Pending') {
            document.getElementById('tab-pending').classList.add('active');
        } else if (status === 'Processing') {
            document.getElementById('tab-processing').classList.add('active');
        } else if (status === 'Ready-To-Pick-Up') {
            document.getElementById('tab-ready').classList.add('active');
        } else if (status === 'Completed') {
            document.getElementById('tab-completed').classList.add('active');
        } else if (status === 'Cancelled') {
            document.getElementById('tab-cancelled').classList.add('active');
        } else if (status === 'Unclaim') {
            document.getElementById('tab-unclaimed').classList.add('active');
        }
    }

    // Get the current status from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status') || 'Pending'; // Default to 'Pending' if no status is found

    // Activate the correct tab
    activateTab(status);
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