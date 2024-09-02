
<?php

if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}

?>


   <header class="header">

   <div class="flex">

      <a class="logo">Admin<span>Panel</span></a>

      <nav class="navbar">
         <a href="dashboard.php">Home</a>
         <a href="product.php">Products</a>
         <a href="#">Orders</a>
         <a href="user_a.php">Users</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="img/<?= $fetch_profile['image']; ?>" alt="">
         <p><?= $fetch_profile['Fname']; ?></p>
         <a href="info_up.php" class="btn">update profile</a>
         <a href="logout.php" class="delete-btn">logout</a>
      </div>

   </div>

</header>
