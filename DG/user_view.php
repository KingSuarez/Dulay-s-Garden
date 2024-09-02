<!-- 
// include 'connection.php';

// if(isset($_POST['click_view_btn'])){

//     $id = $_POST['user_id'];

//     $select_users = $conn->prepare("SELECT FROM `users` id = ?");
//     $select_usersexecute([$id]);
//     if($select_users->rowCount() > 0){
//         while($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)){
//             echo '
//             <h6>' . $fetch_users['id'] . '</h6>
//             <h6>' . $fetch_users['Fname'] . '</h6>
//             <h6>' . $fetch_users['Lname'] . '</h6>
//             <h6>' . $fetch_users['contact'] . '</h6>
//             <h6>' . $fetch_users['email'] . '</h6>
//             ';
//         }
//     }
//     else{
//         echo'<h4>no record found</h4>';
//     }
// }
-->
<?php

include 'connection.php';

if (isset($_POST['view_u'])) {
    $user_id = $_POST['user_id'];

    $select_users = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_users->execute([$user_id]);

    if ($select_users instanceof PDOStatement) {
        $fetch_users = $select_users->fetch(PDO::FETCH_ASSOC);
        while($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)){
            $output='
            <h6>' . $fetch_users['id'] . '</h6>
            <h6>' . $fetch_users['Fname'] . '</h6>
            <h6>' . $fetch_users['Lname'] . '</h6>
            <h6>' . $fetch_users['contact'] . '</h6>
            <h6>' . $fetch_users['email'] . '</h6>
            ';
        }
    } else {
        echo "Error: Query execution failed.";
    }
}
?>
