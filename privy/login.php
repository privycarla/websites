<?php

include './components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL); // Use FILTER_SANITIZE_EMAIL to sanitize the email input
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   try {
      $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
      $select_user->execute([$email, $pass]);
      $row = $select_user->fetch(PDO::FETCH_ASSOC);

      if($select_user->rowCount() > 0){
         $_SESSION['user_id'] = $row['id'];
         header('location:home.php');
      }else{
         $message[] = 'Incorrect username or password!';
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
   <title>Login</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include './components/user_header.php'; ?>
<!-- header section ends -->
<div class="container">
<section class="form-container">

   <form action="" method="post">
         <h3>LOGIN</h3>
       
            
               <input type="email" name="email" required placeholder="Email" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">                     
               <input type="password" name="pass" required placeholder="Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">                                  

               <input type="submit" value="Login" name="submit" class="btn">
               <p>Don't have an account? <a href="reg.php">Sign up </a></p>
         </div>
   </form>

   <?php
   // Display the message if it exists
   if(isset($message)){
      echo "<p>$message</p>";
   }
   ?>

</section>
</div>

<?php
// Display the results from the SELECT query
if(isset($row)){
   echo "<h2>User Details:</h2>";
   echo "<p>Name: " . $row['name'] . "</p>";
   echo "<p>Email: " . $row['email'] . "</p>";
}
?>



<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>