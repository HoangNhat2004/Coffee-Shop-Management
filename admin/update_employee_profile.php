<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id']; // Giữ nguyên vì đây là admin xác thực để vào trang

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

// Lấy ID của employee từ URL
$employee_id = $_GET['id'] ?? null;

if ($employee_id && isset($_POST['submit'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   // Kiểm tra mật khẩu hiện tại
   $select_old_pass = $conn->prepare("SELECT password FROM `employee` WHERE id = ?");
   $select_old_pass->execute([$employee_id]);
   $fetch_prev_pass = $select_old_pass->fetch(PDO::FETCH_ASSOC);
   $prev_pass = $fetch_prev_pass['password'];
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);

   // Chỉ cho phép cập nhật nếu mật khẩu hiện tại chính xác
   if ($old_pass != $prev_pass) {
      $message[] = 'Old password not matched!';
   } else {
      // Kiểm tra nếu tên người dùng đã tồn tại trong bảng employee (không trùng với ID hiện tại)
      if (!empty($name)) {
         $select_name = $conn->prepare("SELECT * FROM `employee` WHERE name = ? AND id != ?");
         $select_name->execute([$name, $employee_id]);
         if ($select_name->rowCount() > 0) {
            $message[] = 'Username already taken!';
         } else {
            $update_name = $conn->prepare("UPDATE `employee` SET name = ? WHERE id = ?");
            $update_name->execute([$name, $employee_id]);
            $message[] = 'Username updated successfully!';
         }
      }

      // Cập nhật mật khẩu mới nếu được yêu cầu
      if (!empty($_POST['new_pass'])) {
         $new_pass = $_POST['new_pass'];
         $new_pass_hashed = sha1($new_pass);
         $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
         $confirm_pass = sha1($_POST['confirm_pass']);
         $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

         if (strlen($_POST['new_pass']) < 8) {
            $message[] = 'New password must be at least 8 characters!';
         } elseif ($new_pass_hashed != $confirm_pass) {
            $message[] = 'Confirm password not matched!';
         } else {
            $update_pass = $conn->prepare("UPDATE `employee` SET password = ? WHERE id = ?");
            $update_pass->execute([$new_pass_hashed, $employee_id]);
            $message[] = 'Password updated successfully!';
         }
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Profile update</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/dashboard_style.css">
</head>
<body>

   <?php include '../components/admin_header.php' ?>

   <section class="form-container">
      <form action="" method="POST">
         <h3>update profile</h3>
         <input type="text" name="name" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="Enter new username">
         <input type="password" name="old_pass" maxlength="20" placeholder="Enter your old password" class="box" required>
         <input type="password" name="new_pass" maxlength="20" placeholder="Enter your new password" class="box">
         <input type="password" name="confirm_pass" maxlength="20" placeholder="Confirm your new password" class="box">
         <input type="submit" value="update now" name="submit" class="btn">
      </form>
   </section>

   <script src="../js/admin_script.js"></script>
</body>
</html>
