<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:ad_login.php');
}

if(isset($_POST['delete'])){
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
   $delete_image->execute([$admin_id]);
   while($fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC)){
      unlink('../uploaded_img/'.$fetch_delete_image['image']);
   }
   $delete_posts = $conn->prepare("DELETE FROM `posts` WHERE admin_id = ?");
   $delete_posts->execute([$admin_id]);
   $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE admin_id = ?");
   $delete_likes->execute([$admin_id]);
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE admin_id = ?");
   $delete_comments->execute([$admin_id]);
   $delete_admin = $conn->prepare("DELETE FROM `admin` WHERE id = ?");
   $delete_admin->execute([$admin_id]);
   header('location:ad_logout.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admins account</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css//ad_style.css">

</head>
<body>

<?php include './ad_header.php' ?>

<!-- admins accounts section starts  -->

<section class="accounts">

   <!-- <h1 class="heading">Admins account</h1> -->

   <div class="box-container">

   <div class="box" style="order: -2;">
      <p>Add new admin</p>
      <a href="./register.php" class="option-btn" style="margin-bottom: .5rem;">Sign Up</a>
   </div>

   <?php
      $select_account = $conn->prepare("SELECT * FROM `admin`");
      $select_account->execute();
      if($select_account->rowCount() > 0){
         while($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)){ 

            // $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE user_id = ?");
            // $count_admin_posts->execute([$fetch_accounts['id']]);
            // $total_admin_posts = $count_admin_posts->rowCount();

   ?>
   <div class="box" style="order: <?php if($fetch_accounts['id'] == $admin_id){ echo '-1'; } ?>;">
      <p> Admin id : <span><?= $fetch_accounts['id']; ?></span> </p>
      <p> Username : <span><?= $fetch_accounts['name']; ?></span> </p>
      <!-- <p> Total posts : <span><?= $total_admin_posts; ?></span> </p> -->
      <div class="flex-btn">
         <?php
            if($fetch_accounts['id'] == $admin_id){
         ?>
            <a href="../admin/up_prof.php" class="option-btn" style="margin-bottom: .5rem;">update</a>
            <form action="" method="POST">
               <input type="hidden" name="post_id" value="<?= $fetch_accounts['id']; ?>" on>
               <button type="submit" name="delete"onclick="return confirm('delete the account?');" class="delete-btn" style="margin-bottom: .5rem;">delete</button>
            </form>
         <?php
            }
         ?>
      </div>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">no accounts available</p>';
   }
   ?>

   </div>

</section>

<!-- admins accounts section ends -->




















<!-- custom js file link  -->
<script src="../js/ad_script.js"></script>


</body>
</html>