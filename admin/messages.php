<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
   $delete_message->execute([$delete_id]);
   header('location:messages.php');
}

// Lọc theo tên khách hàng nếu có tìm kiếm
if (isset($_POST['search_message'])) {
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
   <title>Messages</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/dashboard_style.css">
   <link rel="stylesheet" href="../css/table.css">

</head>

<body>

   <?php include '../components/admin_header.php' ?>

   <!-- messages section starts  -->

   <section class="messages">

      <h1 class="heading">Messages Management</h1>

      <div class="table_header">
         <p>Message Details</p>
         <div>
            <!-- Form tìm kiếm theo tên khách hàng -->
            <form method="post">
               <input type="text" name="customer_name" placeholder="Customer name" value="<?= htmlspecialchars($customer_name); ?>">
               <button type="submit" name="search_message" class="add_new">Search</button>
            </form>
         </div>
      </div>

      <div>
         <table class="table">
            <thead>
               <tr>
                  <th>Name</th>
                  <th>Number</th>
                  <th>Email</th>
                  <th>Message</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
               <?php
               // Truy vấn tìm kiếm theo tên khách hàng nếu có
               if ($customer_name != '') {
                  $select_messages = $conn->prepare("SELECT * FROM `messages` WHERE name LIKE ?");
                  $select_messages->execute(["%{$customer_name}%"]);
               } else {
                  $select_messages = $conn->prepare("SELECT * FROM `messages`");
                  $select_messages->execute();
               }

               if ($select_messages->rowCount() > 0) {
                  while ($fetch_messages = $select_messages->fetch(PDO::FETCH_ASSOC)) {
               ?>
                     <tr>
                        <td><span><?= $fetch_messages['name']; ?></span></td>
                        <td><span><?= $fetch_messages['number']; ?></span></td>
                        <td><span><?= $fetch_messages['email']; ?></span></td>
                        <td><span><?= $fetch_messages['message']; ?></span></td>
                        <td><a href="messages.php?delete=<?= $fetch_messages['id']; ?>" onclick="return confirm('Delete this message?');"><button><i class="fa-solid fa-trash"></i></button></a></td>
                     </tr>
               <?php
                  }
               } else {
                  echo '<p class="empty">No messages found</p>';
               }
               ?>
            </tbody>
         </table>
      </div>

   </section>

   <!-- messages section ends -->

   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

</body>

</html>
