<?php

include './components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include './components/like_post.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>search page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include './components/user_header.php'; ?>

<?php
   if(isset($_POST['search_box']) OR isset($_POST['search_btn'])){
?>
<section class="viewposts-container">

   <div class="viewbox-container">

      <?php
         $search_box = $_POST['search_box'];
         $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE title LIKE '%{$search_box}%' OR category LIKE '%{$search_box}%' AND status = ?");
         $select_posts->execute(['active']);
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
      <form class="viewbox" method="post">
         <input type="hidden" name="post_id" value="<?= $post_id; ?>">
         <input type="hidden" name="user_id" value="<?= $fetch_posts['user_id']; ?>">
         <div class="viewpost-admin">
         <?php if (!empty($fetch_user_profile) && $fetch_user_profile['image'] != '') { ?>
                  <img src="uploaded_img/<?= $fetch_user_profile['image']; ?>" class="user-profile-image" style="width:  4.7rem ; height: 4.5rem;">
             <?php } else { ?>
                  <i class="fas fa-user"></i>
          <?php } ?>
               <div>
                  <a href=" #"><?= $fetch_posts['name']; ?></a>
                  <div><?= $fetch_posts['created_at']; ?></div>
               </div>
            </div>
         
         <?php
            if($fetch_posts['image'] != ''){  
         ?>
         <img src="uploaded_img/<?= $fetch_posts['image']; ?>" class="viewpost-image" alt="">
         <?php
         }
         ?>
         <div class="viewpost-title"><?= $fetch_posts['title']; ?></div>
         <div class="viewpost-content content-150"><?= $fetch_posts['content']; ?></div>
         <a href="view_post.php?post_id=<?= $post_id; ?>" class="inline-btn">read more</a>
         <a href="category.php?category=<?= $fetch_posts['category']; ?>" class="post-cat"> <i class="fas fa-tag"></i> <span><?= $fetch_posts['category']; ?></span></a>
         <div class="viewicons">
            <a href="view_post.php?post_id=<?= $post_id; ?>"><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></a>
            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if($confirm_likes->rowCount() > 0){ echo 'color:var(--red);'; } ?>  "></i><span>(<?= $total_post_likes; ?>)</span></button>
         </div>
      
      </form>
      <?php
         }
      }else{
         echo '<p class="empty">no result found!</p>';
      }
      ?>
   </div>

</section>

<?php
   }else{
      echo '<section><p class="empty">search something!</p></section>';
   }
?>
   


<script src="js/script.js"></script>

</body>
</html>