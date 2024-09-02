<?php
include 'connection.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $select_product->execute([$product_id]);

    if ($select_product->rowCount() > 0) {
        $product = $select_product->fetch(PDO::FETCH_ASSOC);
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid product ID']);
};

?>
