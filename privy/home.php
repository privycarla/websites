<?php

include './components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

// Handle like post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['like_post'])) {
    $post_id = $_POST['post_id'];

    // Check if the user has already liked the post
    $confirm_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND post_id = ?");
    $confirm_likes->execute([$user_id, $post_id]);

    if ($confirm_likes->rowCount() > 0) {
        // If already liked, remove the like
        $delete_like = $conn->prepare("DELETE FROM `likes` WHERE user_id = ? AND post_id = ?");
        $delete_like->execute([$user_id, $post_id]);
    } else {
        // If not liked yet, add the like
        $insert_like = $conn->prepare("INSERT INTO `likes`(user_id, post_id) VALUES(?, ?)");
        $insert_like->execute([$user_id, $post_id]);
    }

    // Refresh the page to reflect the like/unlike action
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch user profile data
$fetch_profile = [];
if ($user_id != '') {
    $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_profile->execute([$user_id]);
    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="./css/style.css">

</head>
<body>

<?php include './components/user_header.php'; ?>

<section class="home-grid">

   <div class="box-container">
      <div class="box">
         <p>Categories</p>
         <div class="flex-box">
            <a href="category.php?category=nature" class="links">nature</a>
            <a href="category.php?category=arts" class="links">art</a>
            <a href="category.php?category=memes" class="links">meme</a>
            <a href="category.php?category=travel" class="links">travel</a>
            <a href="category.php?category=photography" class="links">photography</a>
            <a href="category.php?category=poems" class="links">poem</a>
            <a href="category.php?category=fitness" class="links">fitness</a>
            <a href="category.php?category=design" class="links">design</a>
            <a href="category.php?category=fashion" class="links">fashion</a>
            <a href="category.php?category=cottagecore" class="links">cottagecore</a>
            <a href="category.php?category=music" class="links">music</a>
            <a href="category.php?category=moodboard" class="links">moodboard</a>
            <a href="category.php?category=entertainment" class="links">entertainment</a>
            <a href="category.php?category=kpop" class="links">kpop</a>
            <a href="category.php?category=foods" class="links">food</a>
            <a href="category.php?category=lifestyle" class="links">lifestyle</a>
            <a href="category.php?category=movie" class="links">movie</a>
            <a href="category.php?category=books" class="links">book</a>
            <a href="category.php?category=shopping" class="links">shopping</a>
            <a href="category.php?category=animations" class="links">animation</a>
         </div>
      </div>
   </div>

   <div class="addpost">
      <div class="box">
         <?php
            $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE user_id = ?");
            $select_posts->execute([$user_id]);
            $numbers_of_posts = $select_posts->rowCount();
         ?>
         <a href="addpost.php" class="btn">add new post</a>
      </div>
   </div>

   <div class="posts-container">
      <div class="postbox-container">
         <?php
            $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ? ORDER BY created_at DESC LIMIT 6 ");
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
         <form class="postbox" method="post">
            <input type="hidden" name="post_id" value="<?= $post_id; ?>">
            <input type="hidden" name="user_id" value="<?= $fetch_posts['user_id']; ?>">
            <div class="post-admin">
               <?php if (!empty($fetch_user_profile) && $fetch_user_profile['image'] != '') { ?>
                    <img src="uploaded_img/<?= $fetch_user_profile['image']; ?>" class="user-profile-image" style="width: 4.7rem; height: 4.5rem;">
               <?php } else { ?>
                    <i class="fas fa-user"></i>
               <?php } ?>
               <div>
                  <a href="#"><?= $fetch_posts['name']; ?></a>
                  <div><?= $fetch_posts['created_at']; ?></div>
               </div>
            </div>
            
            <?php if($fetch_posts['image'] != '') { ?>
                <img src="uploaded_img/<?= $fetch_posts['image']; ?>" class="post-image" alt="">
            <?php } ?>
            <div class="post-title"><?= $fetch_posts['title']; ?></div>
            <div class="post-content content-150"><?= $fetch_posts['content']; ?></div>
            <a href="view_post.php?post_id=<?= $post_id; ?>" class="inline-btn">read more</a>
            <a href="category.php?category=<?= $fetch_posts['category']; ?>" class="post-cat"><i class="fas fa-tag"></i> <span><?= $fetch_posts['category']; ?></span></a>
            <div class="icons">
               <a href="view_post.php?post_id=<?= $post_id; ?>"><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></a>
               <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if($confirm_likes->rowCount() > 0){ echo 'color:var(--red);'; } ?>"></i><span>(<?= $total_post_likes; ?>)</span></button>
            </div>
         </form>
         <?php
            }
         } else {
            echo '<p class="empty">No posts added yet!</p>';
         }
         ?>
      </div>
   </div>
</section>

<script src="js/script.js"></script>

</body>
</html>
