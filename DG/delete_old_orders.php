<?php
// Connection to the database
include 'connection.php';

try {
    // Delete orders older than 1 week
    $delete_query = $conn->prepare("DELETE FROM uorders WHERE deleted_at IS NOT NULL AND deleted_at <= NOW() - INTERVAL 1 WEEK");
    $delete_query->execute();

    echo "Old orders deleted successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
