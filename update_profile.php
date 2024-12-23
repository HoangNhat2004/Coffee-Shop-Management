<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:home.php');
}

// Fetch the current profile data
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);

   $update_allowed = true;
   $changes_made = false;

   // Password validation block
   $prev_pass = $fetch_profile['password'];
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
   $new_pass = $_POST['new_pass'];
   $confirm_pass = sha1($_POST['confirm_pass']);
   $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

   if (!empty($new_pass)) {
      if ($old_pass != $prev_pass) {
         $message[] = 'Old password not matched!';
         $update_allowed = false;
      } elseif ($new_pass != $_POST['confirm_pass']) {
         $message[] = 'Confirm password not matched!';
         $update_allowed = false;
      } elseif (strlen($new_pass) < 8) {
         $message[] = 'New password must be at least 8 characters long!';
         $update_allowed = false;
      } else {
         $new_pass = sha1($new_pass);
         $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_pass->execute([$new_pass, $user_id]);
         $message[] = 'Password updated successfully!';
         $changes_made = true;
      }
   }

   if ($update_allowed) {
      // Username validation and update
      if (!empty($name) && $name != $fetch_profile['name']) {
         $select_name = $conn->prepare("SELECT * FROM `users` WHERE name = ? AND id != ?");
         $select_name->execute([$name, $user_id]);
         if ($select_name->rowCount() > 0) {
            $message[] = 'Username already taken! Please choose a different one.';
            $update_allowed = false;
         } else {
            $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
            $update_name->execute([$name, $user_id]);
            $changes_made = true;
         }
      }

      // Email validation and update
      if (!empty($email) && $email != $fetch_profile['email']) {
         $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND id != ?");
         $select_email->execute([$email, $user_id]);
         if ($select_email->rowCount() > 0) {
            $message[] = 'Email already taken!';
            $update_allowed = false;
         } else {
            $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
            $update_email->execute([$email, $user_id]);
            $changes_made = true;
         }
      }

      // Number validation and update
      if (!empty($number) && $number != $fetch_profile['number']) {
         $select_number = $conn->prepare("SELECT * FROM `users` WHERE number = ? AND id != ?");
         $select_number->execute([$number, $user_id]);
         if ($select_number->rowCount() > 0) {
            $message[] = 'Number already taken!';
            $update_allowed = false;
         } else {
            $update_number = $conn->prepare("UPDATE `users` SET number = ? WHERE id = ?");
            $update_number->execute([$number, $user_id]);
            $changes_made = true;
         }
      }

      if ($changes_made) {
         $message[] = 'Profile updated successfully!';
         header('Location: checkout.php');
         exit; 
      } else if (!$changes_made && $update_allowed) {
         $message[] = 'No changes were made.';
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
   <title>Update Profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <!-- header section starts  -->
   <?php include 'components/user_header.php'; ?>
   <!-- header section ends -->

   <section class="form-container update-form">

      <form action="" method="post">
         <h3>Update Profile</h3>
         <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" class="box" maxlength="50">
         <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="number" name="number" placeholder="<?= $fetch_profile['number']; ?>" class="box" min="0" max="9999999999" maxlength="10">
         <input type="password" name="old_pass" placeholder="Enter your old password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="new_pass" placeholder="Enter your new password (min 8 characters)" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="confirm_pass" placeholder="Confirm your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="submit" value="Update Now" name="submit" class="btn">
      </form>

   </section>

   <?php include 'components/footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>
