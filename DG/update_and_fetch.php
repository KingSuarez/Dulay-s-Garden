<?php
require 'connection.php'; // Your DB connection file

// Check if the threshold is provided and valid
if (isset($_POST['threshold']) && is_numeric($_POST['threshold'])) {
    $new_threshold = (int)$_POST['threshold'];

    // Update the threshold in the database
    $stmt = $conn->prepare("UPDATE critical SET critical_stock = :threshold WHERE id = 1");
    $stmt->bindParam(':threshold', $new_threshold, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        // After successful update, fetch products below the new threshold
        $fetchStmt = $conn->prepare("SELECT pname, stock FROM products WHERE stock < :threshold");
        $fetchStmt->bindParam(':threshold', $new_threshold, PDO::PARAM_INT);
        $fetchStmt->execute();
        $results = $fetchStmt->fetchAll(PDO::FETCH_ASSOC);

        // Output the updated product list as table rows
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td style="padding: 8px; border-bottom: 1px solid #ddd; text-transform: capitalize;">' . htmlspecialchars($row["pname"]) . '</td>';
            echo '<td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($row["stock"]) . '</td>';
            echo '</tr>';
        }
    } else {
        echo 'Error updating the threshold.';
    }
} else {
    echo 'Invalid threshold value.';
}
?>
