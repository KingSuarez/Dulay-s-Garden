<?php
include 'connection.php';


// Update stock to 0 if it's negative
$update_negative_stock = $conn->prepare("UPDATE `products` SET stock = 0 WHERE stock < 0");
$update_negative_stock->execute();

// Prepare and execute the query to fetch all product names and their stock
$select_products = $conn->prepare("SELECT pname, stock FROM `products`");
$select_products->execute();

// Fetch all product names and stock and store them in an associative array
$products = $select_products->fetchAll(PDO::FETCH_ASSOC);

// Extract product names and stock into separate arrays for easier use
$product_names = array_column($products, 'pname');
$product_stock = array_column($products, 'stock');

// Get the number of products
$number_of_products = count($product_names);

$select_users = $conn->prepare("SELECT * FROM `users` WHERE user_type = ?");
$select_users->execute(['user']);
$number_of_users = $select_users->rowCount();

$number_of_reserve = 0; // Assuming reserve is 0 as in your original code

// Function to fetch products based on category and exclude those with stock 0
function fetchProductsByCategory($conn, $category) {
    $show_products = $conn->prepare("SELECT pname, stock FROM `products` WHERE category = ? AND stock > 0");
    $show_products->execute([$category]);
    return $show_products->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch products for each category
$plants = fetchProductsByCategory($conn, 'Plant');
$soils = fetchProductsByCategory($conn, 'Soil');
$pots = fetchProductsByCategory($conn, 'Pot');
$fertilizers = fetchProductsByCategory($conn, 'Fertilizer');

// Extract product names and stock into separate arrays for each category
$plant_names = array_column($plants, 'pname');
$plant_stock = array_column($plants, 'stock');

$soil_names = array_column($soils, 'pname');
$soil_stock = array_column($soils, 'stock');

$pot_names = array_column($pots, 'pname');
$pot_stock = array_column($pots, 'stock');

$fertilizer_names = array_column($fertilizers, 'pname');
$fertilizer_stock = array_column($fertilizers, 'stock');

// Get the number of products for each category
$number_of_plants = count($plant_names);
$number_of_soils = count($soil_names);
$number_of_pots = count($pot_names);
$number_of_fertilizers = count($fertilizer_names);


// Output data for Chart.js
$data = [
    'products' => $number_of_products,
    'users' => $number_of_users,
    'reserve' => $number_of_reserve,
    'product_names' => $product_names, 
    'product_stock' => $product_stock,
    
    'plants' => $number_of_plants,
    'soils' => $number_of_soils,
    'pots' => $number_of_pots,
    'fertilizers' => $number_of_fertilizers,
    'plant_names' => $plant_names,
    'plant_stock' => $plant_stock,
    'soil_names' => $soil_names, // Added soil names
    'soil_stock' => $soil_stock, // Added soil 
    'pot_names' => $pot_names,
    'pot_stock' => $pot_stock,
    'fertilizer_names' => $fertilizer_names,
    'fertilizer_stock' => $fertilizer_stock,

];



?>

<!-- This is Chart Of Sales -->

