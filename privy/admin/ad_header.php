<?php
  
    if(isset($_SESSION['admin_id'])){
        $admin_id = $_SESSION['admin_id'];
    } else {
        $admin_id = ''; // Set a default value if admin_id is not set
    }
?>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <a href="./dashboard.php" class="logo">Admin<span>Panel</span></a>

   <div class="profile">
      <?php
         $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
         $select_profile->execute([$admin_id]);
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
      ?>
      <p><?= $fetch_profile['name']; ?></p>
     <?php
      if(isset($fetch_profile) && is_array($fetch_profile)){
         // Check if $fetch_profile is an array before accessing its elements
         $name = isset($fetch_profile['name']) ? $fetch_profile['name'] : '';
         // Other operations using $fetch_profile
      }
?>
      <a href="./up_prof.php" class="btn">update profile</a>
   </div>

   <nav class="navbar">
      <a href="./dashboard.php"><i class="fas fa-home"></i> <span>home</span></a>
      <!-- <a href="../admin/add_post.php"><i class="fas fa-pen"></i> <span>add posts</span></a> -->
      <a href="./view.php"><i class="fas fa-eye"></i> <span>posts</span></a>
      <a href="./account.php"><i class="fas fa-user"></i> <span>admin</span></a>
      <a href="./ad_logout.php" style="color:var(--red);" onclick="return confirm('logout from the website?');"><i class="fas fa-right-from-bracket"></i><span>logout</span></a>
   </nav>

   <div class="flex-btn">
      <a href="./ad_login.php" class="option-btn">login</a>
      <a href="./register.php" class="option-btn">sign up</a>
   </div>

</header>

<div id="menu-btn" class="fas fa-bars"></div>