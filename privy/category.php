<?php

include './components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_GET['category'])){
   $category = $_GET['category'];
}else{
   $category = '';
}

include './components/like_post.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Category</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include './components/user_header.php'; ?>
<!-- header section ends -->


<section class="posts-container">

   <!-- <h1 class="heading">post categories</h1> -->

   <div class="postbox-container">

      <?php
         $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE category = ? and status = ?");
         $select_posts->execute([$category, 'active']);
         if($select_posts->rowCount() > 0){
            while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
               
            $post_id = $fetch_posts['id'];

            $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
            $count_post_comments->execute([$post_id]);
            $total_post_comments = $count_post_comments->rowCount(); 

            $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
            $count_post_likes->execute([$post_id]);
            $total_post_likes = $count_post_likes->rowCount();

            $confirm_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND post_id = ?");
            $confirm_likes->execute([$user_id, $post_id]);

            $select_user_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_user_profile->execute([$fetch_posts['user_id']]);
            $fetch_user_profile = $select_user_profile->fetch(PDO::FETCH_ASSOC);
      ?>
      <form class="postbox" method="post">
         <input type="hidden" name="post_id" value="<?= $post_id; ?>">
         <input type="hidden" name="user_id" value="<?= $fetch_posts['user_id']; ?>">
         <div class="post-admin">
         <?php if (!empty($fetch_user_profile) && $fetch_user_profile['image'] != '') { ?>
                  <img src="uploaded_img/<?= $fetch_user_profile['image']; ?>" class="user-profile-image" style="width:  4.7rem ; height: 4.5rem;">
             <?php } else { ?>
                  <i class="fas fa-user"></i>
          <?php } ?>
            <div>
               <a href="#"><?= $fetch_posts['name']; ?></a>
               <div><?=(($fetch_posts['created_at'])); ?></div>
            </div>
         </div>
         
         <?php
            if($fetch_posts['image'] != ''){  
         ?>
         <img src="uploaded_img/<?= $fetch_posts['image']; ?>" class="post-image" alt="">
         <?php
         }
         ?>
         <div class="post-title"><?= $fetch_posts['title']; ?></div>
         <div class="post-content content-150"><?= $fetch_posts['content']; ?></div>
         <a href="view_post.php?post_id=<?= $post_id; ?>" class="inline-btn">read more</a>
         <div class="icons">
            <a href="view_post.php?post_id=<?= $post_id; ?>"><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></a>
            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if($confirm_likes->rowCount() > 0){ echo 'color:var(--red);'; } ?>  "></i><span>(<?= $total_post_likes; ?>)</span></button>
         </div>
      
      </form>
      <?php
         }
      }else{
         echo '<p class="empty">no posts found for this category!</p>';
      }
      ?>
   </div>

</section>


















<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>