<?php
include './components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
};

if (isset($_POST['submit'])) {

   $firstname = $_POST['firstname'];
   $firstname = filter_var($firstname, FILTER_SANITIZE_SPECIAL_CHARS);

   $lastname = $_POST['lastname'];
   $lastname = filter_var($lastname, FILTER_SANITIZE_SPECIAL_CHARS);

   $username = $_POST['username'];
   $username = filter_var($username, FILTER_SANITIZE_SPECIAL_CHARS);

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL); // Use FILTER_SANITIZE_EMAIL to sanitize the email input

   $pass = $_POST['pass'];
   $pass = filter_var($pass, FILTER_SANITIZE_SPECIAL_CHARS);

   $cpass = $_POST['cpass'];
   $cpass = filter_var($cpass, FILTER_SANITIZE_SPECIAL_CHARS);

   $pass = sha1($pass);
   $cpass = sha1($cpass);

   
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_SPECIAL_CHARS);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img' . $image;

   try {
      $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
      $select_user->execute([$email]);
      $row = $select_user->fetch(PDO::FETCH_ASSOC);

      if ($select_user->rowCount() > 0) {
         $message[] = 'Email already exists!';
      } else {
         if ($pass != $cpass) {
            $message[] = 'Confirm password does not match!';
         } else {
            if ($image_size > 2000000) {
               $message[] = 'Image size is too large!';
            } else {
               if (move_uploaded_file($image_tmp_name, $image_folder)) {
                  $insert_user = $conn->prepare("INSERT INTO `users`(firstname, lastname, username, email, password, image) VALUES(?,?,?,?,?,?)");
                  $insert_user->execute([$firstname, $lastname, $username, $email, $cpass, $image_folder]);

                  $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
                  $select_user->execute([$email, $pass]);
                  $row = $select_user->fetch(PDO::FETCH_ASSOC);

                  if ($select_user->rowCount() > 0) {
                     $_SESSION['user_id'] = $row['id'];
                     header('location:home.php');
                  }
               } else {
                  $message[] = 'Failed to upload the image!';
               }
            }
         }
      }
   } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Sign Up</title>

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
      <h3>Sign up</h3>
      <input type="text" name="firstname" required placeholder="Enter First name" class="box" maxlength="100">
      <input type="text" name="lastname" required placeholder="Enter Last name" class="box" maxlength="100">
      <input type="text" name="username" required placeholder="Enter Username" class="box" maxlength="50">
      <input type="email" name="email" required placeholder="Enter Email" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Enter Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="Confirm your Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
      <input type="submit" value="Register" name="submit" class="btn">
      <p>Already have an account? <a href="login.php">Login</a></p>
   </form>

</section>



















<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>