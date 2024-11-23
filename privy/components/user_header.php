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

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

// Fetch user details including image
if ($user_id) {
    $select_user = $conn->prepare("SELECT image FROM `users` WHERE id = ?");
    $select_user->execute([$user_id]);
    $user = $select_user->fetch(PDO::FETCH_ASSOC);
    $user_image = $user['image'];
} else {
    $user_image = ''; // Default image or placeholder
}

?>

<header class="header">

   <section class="flex">

      <a href="home.php" class="logo">privy</a>

      <form action="search.php" method="POST" class="search-form">
         <input type="text" name="search_box" class="box" maxlength="100" placeholder="search privy" required>
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>

      <div class="icons">
         <!-- <div id="menu-btn" class="fas fa-bars"></div> -->
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn">
         <?php if ($user_image) : ?>
            <img src="<?= $user_image; ?>" alt="User Image" class="user-profile-image" style="width: 100% ; height: 100%">
          
         <?php else : ?>
            <div id="search-btn" class="fas fa-search"></div>
            <div id="user-btn" class="fas fa-user"></div> <!-- Default icon if no user image -->
         <?php endif; ?>
         </div>
      </div>

      <!-- <div class="icons"> -->
         
      <!-- </div>  -->
      <!-- <nav class="navbar">
         <a href="home.php"> <i class="fas fa-angle-right"></i> home</a>
         <a href="post.php"> <i class="fas fa-angle-right"></i> posts</a>
         <a href="all_category.php"> <i class="fas fa-angle-right"></i> category</a>
         <a href="authors.php"> <i class="fas fa-angle-right"></i> authors</a>
        
      </nav> -->

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p class="name"><?= $fetch_profile['username']; ?></p>
         <a href="update.php" class="btn">update profile</a>
         <a href="profile.php" class="btn">Profile</a>
         <!-- <div class="flex-btn">
            <a href="login.php" class="option-btn">login</a>
            <a href="reg.php" class="option-btn">register</a>
         </div>  -->
         <a href="logout.php" onclick="return confirm('Want to leave this website?');" class="delete-btn">logout</a>
         <?php
            }else{
         ?>
            <p class="name">Want to discover more?</p>
            <a href="login.php" class="option-btn">login</a>
         <?php
            }
         ?>
      </div>

   </section>

</header>