<?php
require_once 'db_connect.php'; // Your database connection file

if(!isset($_GET['id'])){
    echo 'No order ID provided.';
    exit;
}

$order = $conn->query("SELECT o.*, concat(c.Fname,' ',c.Lname) as users FROM `orders` o inner join users c on c.id = o.user_id where o.id = '{$_GET['id']}' ");
if($order->num_rows > 0){
    foreach($order->fetch_assoc() as $k => $v){
        $$k = $v;
    }
}else{
    echo 'Order ID provided is unknown.';
    exit;
}
?>

<div class="container-fluid">
    <p><b>Client Name: <?php echo $client ?></b></p>
    <?php if($order_type == 1): ?>
    <p><b>Delivery Address: <?php echo $delivery_address ?></b></p>
    <?php endif; ?>
    <table class="table-striped table table-bordered" id="list">
        <colgroup>
            <col width="15%">
            <col width="35%">
            <col width="25%">
            <col width="25%">
        </colgroup>
        <thead>
            <tr>
                <th>QTY</th>
                <th>Product</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $olist = $conn->query("SELECT o.*,p.name,b.name as pname FROM order_list o inner join products p on o.product_id = p.id inner join id b on p.id = b.id where o.o_id = '{$id}' ");
                while($row = $olist->fetch_assoc()):
                foreach($row as $k => $v){
                    $row[$k] = trim(stripslashes($v));
                }
            ?>
            <tr>
                <td><?php echo $row['quantity'] ?></td>
                <td>
                    <p class="m-0"><?php echo $row['name']?></p>
                    <p class="m-0"><small>Brand: <?php echo $row['pname']?></small></p>
                </td>
                <td class="text-right"><?php echo number_format($row['price']) ?></td>
                <td class="text-right"><?php echo number_format($row['price'] * $row['quantity']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan='3' class="text-right">Total</th>
                <th class="text-right"><?php echo number_format($amount) ?></th>
            </tr>
        </tfoot>
    </table>
    <div class="row">
        <div class="col-6">
            <p>Payment Method: <?php echo $payment_method ?></p>
            <p>Payment Status: <?php echo $paid == 0 ? '<span class="badge badge-light text-dark">Unpaid</span>' : '<span class="badge badge-success">Paid</span>' ?></p>
        </div>
    </div>
</div>
