<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_employees = $conn->prepare("DELETE FROM `employee` WHERE id = ?");
   $delete_employees->execute([$delete_id]);
   header('location:employee_accounts.php');
}

// Lọc theo tên nhân viên nếu có tìm kiếm
if (isset($_POST['search_employee'])) {
   $employee_name = $_POST['employee_name'];
} else {
   $employee_name = '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Employees accounts</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/dashboard_style.css">
   <link rel="stylesheet" href="../css/table.css">

</head>

<body>
   <?php include '../components/admin_header.php' ?>
   <!-- employee accounts section starts  -->
   <section class="accounts">
      <h1 class="heading">Employees Management</h1>

      <div class="table_header">
         <p>Employee Details</p>
         <div style="display: flex; flex-direction: row;">
            <!-- Form tìm kiếm theo tên nhân viên -->
            <form method="post">
               <input type="text" name="employee_name" placeholder="Employee name" value="<?= htmlspecialchars($employee_name); ?>">
               <button type="submit" name="search_employee" class="add_new">Search</button>
            </form>
            <div style="padding: 0 5px;">
               <a href="register_employee.php"><button class="add_new">Add Employee</button></a>
            </div>
         </div>
      </div>

      <div>
         <table class="table">
            <thead>
               <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Age</th>
                  <th>Sex</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>Address</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
               <?php
               // Truy vấn tìm kiếm theo tên nhân viên nếu có
               if ($employee_name != '') {
                  $select_account = $conn->prepare("SELECT * FROM `employee` WHERE name LIKE ?");
                  $select_account->execute(["%{$employee_name}%"]);
               } else {
                  $select_account = $conn->prepare("SELECT * FROM `employee`");
                  $select_account->execute();
               }

               if ($select_account->rowCount() > 0) {
                  while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
               ?>
                     <tr>
                        <td><span><?= $fetch_accounts['id']; ?></span></td>
                        <td><span><?= $fetch_accounts['name']; ?></span></td>
                        <td><span><?= $fetch_accounts['age']; ?></span></td>
                        <td><span><?= $fetch_accounts['sex']; ?></span></td>
                        <td><span><?= $fetch_accounts['phone']; ?></span></td>
                        <td><span><?= $fetch_accounts['email']; ?></span></td>
                        <td><span><?= $fetch_accounts['address']; ?></span></td>
                        <td>
                           <a href="update_employee_profile.php?id=<?= $fetch_accounts['id']; ?>"><button><i class="fa-solid fa-pen-to-square"></i></button></a>
                           <a href="employee_accounts.php?delete=<?= $fetch_accounts['id']; ?>" onclick="return confirm('Delete this account?');"><button><i class="fa-solid fa-trash"></i></button></a>
                        </td>
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
   <!-- employee accounts section ends -->

   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

</body>

</html>
