<?php

include '../components/connect.php';

session_start();

$employee_id = $_SESSION['employee_id'];

if (!isset($employee_id)) {
   header('location:employee_login.php');
}

if (isset($_POST['update_payment'])) {
   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_status->execute([$payment_status, $order_id]);
   $message[] = 'Payment status updated!';
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
}

// Kiểm tra nếu có tìm kiếm
$search_order_id = isset($_POST['search_order_id']) ? $_POST['search_order_id'] : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Placed orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/dashboard_style.css">
   <link rel="stylesheet" href="../css/table.css">

</head>

<body>

   <?php include '../components/employee_header.php' ?>

   <!-- placed orders section starts  -->

   <section class="placed-orders">

      <h1 class="heading">placed orders</h1>

      <div class="table_header">
         <p>Order Details</p>
         <div>
            <form method="post" action="">
               <input type="text" name="search_order_id" placeholder="Order number" value="<?= htmlspecialchars($search_order_id); ?>">
               <button type="submit" class="add_new">Search</button>
            </form>
         </div>
      </div>

      <div>
         <table class="table">
            <thead>
               <tr>
                  <th>ID</th>
                  <th>Date</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Address</th>
                  <th>Products</th>
                  <th>Price</th>
                  <th>PaymentType</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
               <?php
               // Truy vấn SQL với điều kiện tìm kiếm
               $query = "SELECT * FROM `orders` WHERE 1";
               $params = [];

               if ($search_order_id != '') {
                  $query .= " AND id = ?";
                  $params[] = $search_order_id;
               }

               $select_orders = $conn->prepare($query);
               $select_orders->execute($params);

               if ($select_orders->rowCount() > 0) {
                  while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
               ?>
                     <tr>
                        <td><?= $fetch_orders['id']; ?></td>
                        <td><?= $fetch_orders['placed_on']; ?></td>
                        <td><?= $fetch_orders['name']; ?></td>
                        <td><?= $fetch_orders['email']; ?></td>
                        <td><?= $fetch_orders['number']; ?></td>
                        <td><?= $fetch_orders['address']; ?></td>
                        <td><?= $fetch_orders['total_products']; ?></td>
                        <td><?= $fetch_orders['total_price']; ?></td>
                        <td><?= $fetch_orders['method']; ?></td>
                        <td>
                           <form action="" method="POST">
                              <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                              <select name="payment_status" class="drop-down">
                                 <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
                                 <option value="pending">Pending</option>
                                 <option value="completed">Completed</option>
                              </select>
                              <div class="flex-btn">
                                 <input type="submit" value="update" class="btn" name="update_payment">
                                 <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Delete this order?');">delete</a>
                              </div>
                           </form>
                        </td>
                     </tr>
               <?php
                  }
               } else {
                  echo '<p class="empty">No orders found!</p>';
               }
               ?>
            </tbody>
         </table>
      </div>

   </section>

   <!-- placed orders section ends -->

   <script src="../js/admin_script.js"></script>

</body>

</html>
