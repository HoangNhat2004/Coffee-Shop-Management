<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_users = $conn->prepare("DELETE FROM `users` WHERE id = ?");
   $delete_users->execute([$delete_id]);
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE user_id = ?");
   $delete_order->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart->execute([$delete_id]);
   header('location:users_accounts.php');
}

// Lọc theo tên khách hàng nếu có tìm kiếm
if (isset($_POST['search_customer'])) {
   $customer_name = $_POST['customer_name'];
} else {
   $customer_name = '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Users accounts</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/dashboard_style.css">
   <link rel="stylesheet" href="../css/table.css">

</head>

<body>

   <?php include '../components/admin_header.php' ?>

   <!-- user accounts section starts  -->

   <section class="accounts">

      <h1 class="heading">User Management</h1>

      <div class="table_header">
         <p>User Details</p>
         <div>
            <!-- Form tìm kiếm theo tên khách hàng -->
            <form method="post">
               <input type="text" name="customer_name" placeholder="Customer name" value="<?= htmlspecialchars($customer_name); ?>">
               <button type="submit" name="search_customer" class="add_new">Search</button>
            </form>
         </div>
      </div>

      <div>
         <table class="table">
            <thead>
               <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Address</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
               <?php
               // Truy vấn tìm kiếm theo tên khách hàng nếu có
               if ($customer_name != '') {
                  $select_account = $conn->prepare("SELECT * FROM `users` WHERE name LIKE ?");
                  $select_account->execute(["%{$customer_name}%"]);
               } else {
                  $select_account = $conn->prepare("SELECT * FROM `users`");
                  $select_account->execute();
               }

               if ($select_account->rowCount() > 0) {
                  while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
               ?>
                     <tr>
                        <td><span><?= $fetch_accounts['id']; ?></span></td>
                        <td><span><?= $fetch_accounts['name']; ?></span></td>
                        <td><span><?= $fetch_accounts['email']; ?></span></td>
                        <td><span><?= $fetch_accounts['address']; ?></span></td>
                        <td><a href="users_accounts.php?delete=<?= $fetch_accounts['id']; ?>" onclick="return confirm('Delete this account?');"><button><i class="fa-solid fa-trash"></i></button></a></td>
                     </tr>
               <?php
                  }
               } else {
                  echo '<p class="empty">no accounts available</p>';
               }
               ?>
            </tbody>
         </table>
      </div>

   </section>

   <!-- user accounts section ends -->

   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

</body>

</html>
