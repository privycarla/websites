<?php

include './components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
   exit();
}

if(isset($_POST['submit'])){

   $firstname = $_POST['firstname'];
   $firstname = filter_var($firstname, FILTER_SANITIZE_SPECIAL_CHARS);

   $lastname = $_POST['lastname'];
   $lastname = filter_var($lastname, FILTER_SANITIZE_SPECIAL_CHARS);

   $username = $_POST['username'];
   $username = filter_var($username, FILTER_SANITIZE_SPECIAL_CHARS);

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_SPECIAL_CHARS);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;

   if(!empty($firstname)){
      $update_firstname = $conn->prepare("UPDATE `users` SET firstname = ? WHERE id = ?");
      $update_firstname->execute([$firstname, $user_id]);
   }

   if(!empty($lastname)){
      $update_lastname = $conn->prepare("UPDATE `users` SET lastname = ? WHERE id = ?");
      $update_lastname->execute([$lastname, $user_id]);
   }

   if(!empty($username)){
      $update_name = $conn->prepare("UPDATE `users` SET username = ? WHERE id = ?");
      $update_name->execute([$username, $user_id]);
   }

   if(!empty($email)){
      $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
      $select_email->execute([$email]);
      if($select_email->rowCount() > 0){
         $message[] = 'Email already taken!';
      }else{
         $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
         $update_email->execute([$email, $user_id]);
      }
   }

   if(!empty($image)){
      // Check if image is of valid size
      if($image_size > 2000000){
         $message[] = 'Image size is too large!';
      } else {
         // Delete old profile picture if exists
         $select_old_image = $conn->prepare("SELECT image FROM `users` WHERE id = ?");
         $select_old_image->execute([$user_id]);
         $fetch_old_image = $select_old_image->fetch(PDO::FETCH_ASSOC);
         $old_image = $fetch_old_image['image'];
         if(!empty($old_image) && file_exists('uploaded_img/' . $old_image)){
            if(!unlink('uploaded_img/' . $old_image)){
               $message[] = 'Failed to delete old image!';
            }
         }

         // Upload new profile picture
         if(move_uploaded_file($image_tmp_name, $image_folder)){
            // Update image field in the database
            $update_image = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
            $update_image->execute([$image, $user_id]);
         } else {
            $message[] = 'Failed to upload new image!';
         }
      }
   }

   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE id = ?");
   $select_prev_pass->execute([$user_id]);
   $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);
   $prev_pass = $fetch_prev_pass['password'];
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_SPECIAL_CHARS);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_SPECIAL_CHARS);
   $confirm_pass = sha1($_POST['confirm_pass']);
   $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_SPECIAL_CHARS);

   if($old_pass != $empty_pass){
      if($old_pass != $prev_pass){
         $message[] = 'Old password not matched!';
      }elseif($new_pass != $confirm_pass){
         $message[] = 'Confirm password not matched!';
      }else{
         if($new_pass != $empty_pass){
            $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_pass->execute([$confirm_pass, $user_id]);
            $message[] = 'Password updated successfully!';
         }else{
            $message[] = 'Please enter a new password!';
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
   <title>Update</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include './components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Profile</h3>
      <input type="text" name="firstname" placeholder="<?= $fetch_profile['firstname']; ?>" class="box" maxlength="100">
      <input type="text" name="lastname" placeholder="<?= $fetch_profile['lastname']; ?>" class="box" maxlength="100">
      <input type="text" name="username" placeholder="<?= $fetch_profile['username']; ?>" class="box" maxlength="50">
      <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="old_pass" placeholder="Enter your old password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="Enter your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" placeholder="Confirm your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
      <input type="submit" value="update" name="submit" class="btn">
   </form>

</section>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
