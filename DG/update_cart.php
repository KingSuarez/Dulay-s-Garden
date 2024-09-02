<?php
// update_cart.php

// Include your database connection code here
$show_products = $conn->prepare("SELECT * FROM `carts` ORDER BY 'id' DESC");
$show_products->execute();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST["product_id"];
    $quantity = $_POST["quantity"];

    // Update the quantity in the database for the specified product_id

    // Calculate the new item total
    $new_item_total = $price * $quantity;

    // Return the new item total to update the front-end
    echo number_format($new_item_total, 2);
}
?>
