<?php
include("connection.php");

if (isset($_POST['request'])) {
    $category = $_POST['request'];

    $result = $conn->prepare("SELECT * FROM `products` WHERE category = ?");
    $result->execute([$category]);

    if ($result->rowCount() > 0) {
?>
        <table  class="display" style="width:100%;">
            <thead>
                <tr style="text-align: center;">
                    <th>Product</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($fetch_products = $result->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <tr>
                        <td> <img src="prod/<?= $fetch_products['image']; ?>" alt="Product Image"> </td>
                        <td><?= $fetch_products['pname'] ?></td>
                        <td>₱<?= $fetch_products['price'] ?></td>
                        <td><?= $fetch_products['stock'] ?></td>
                        <td><?= $fetch_products['category'] ?></td>
                        <td>
                        <a href="product.php?delete=<?= $fetch_products['id']; ?>" onclick="return confirm('Delete this product?');">
                        <i class="fa-solid fa-trash-can fa-lg" style="color: #D04848;"></i>
                        </a>

                            <a href="Pmodal.php?id=<?=$fetch_products['id']; ?>">
                      <i class="fa-solid fa-edit fa-lg" style="color: #7EA1FF;"></i></a>
                      
              
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    <?php
    } else {
        echo "No Record Found";
    }
}
?>
