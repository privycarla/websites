<?php

include './components/connect.php';

session_start();

$user_id = $_SESSION['user_id'];

// if(!isset($user_id)){
//    header('location:ad_login.php');
// }

$get_id = $_GET['post_id'];

if(isset($_POST['delete'])){

   $p_id = $_POST['post_id'];
   $p_id = filter_var($p_id, FILTER_SANITIZE_SPECIAL_CHARS);
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
   $delete_image->execute([$p_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if($fetch_delete_image['image'] != ''){
      unlink('../uploaded_img/'.$fetch_delete_image['image']);
   }
   $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
   $delete_post->execute([$p_id]);
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
   $delete_comments->execute([$p_id]);
   header('location:view.php');

}

if(isset($_POST['delete_comment'])){

   $comment_id = $_POST['comment_id'];
   $comment_id = filter_var($comment_id, FILTER_SANITIZE_SPECIAL_CHARS);
   $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
   $delete_comment->execute([$comment_id]);
   $message[] = 'comment deleted!';

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Posts</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css//ad_style.css">

</head>
<body>

<?php include './ad_header.php' ?>

<section class="read-post">

   <?php
      $select_posts = $conn->prepare("SELECT posts.*, users.image AS user_image, users.username AS user_name, posts.created_at AS post_created_at FROM `posts` INNER JOIN `users` ON posts.user_id = users.id WHERE posts.id = ?");
      $select_posts->execute([$get_id]);
      if($select_posts->rowCount() > 0){
         while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
            $post_id = $fetch_posts['id'];

            $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
            $count_post_comments->execute([$post_id]);
            $total_post_comments = $count_post_comments->rowCount();

            $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
            $count_post_likes->execute([$post_id]);
            $total_post_likes = $count_post_likes->rowCount();

   ?>
   <form method="post">
      <input type="hidden" name="post_id" value="<?= $post_id; ?>">
      <div class="status" style="background-color:<?php if($fetch_posts['status'] == 'active'){echo 'limegreen'; }else{echo 'coral';}; ?>;"><?= $fetch_posts['status']; ?></div>
      <?php if($fetch_posts['image'] != ''){ ?>
         <img src="../uploaded_img/<?= $fetch_posts['image']; ?>" class="image" alt="">
      <?php } ?>
      <div class="viewpost-admin">
         <?php if (!empty($fetch_posts['user_image'])) { ?>
                  <img src="../uploaded_img/<?= $fetch_posts['user_image']; ?>" class="user-profile-image" style="width:  4.7rem ; height: 4.5rem;">
             <?php } else { ?>
                  <i class="fas fa-user"></i>
          <?php } ?>
            <div>
               <a href="#"><?= $fetch_posts['user_name']; ?></a>
               <div><?= $fetch_posts['post_created_at']; ?></div>
            </div>
         </div>
           
      <div class="title"><?= $fetch_posts['title']; ?></div>
      <div class="content"><?= $fetch_posts['content']; ?></div>
      <div class="icons">
         <div class="likes"><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
         <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
      </div>
      <div class="flex-btn">
         <!-- <a href="../admin/edit.php " class="inline-option-btn">edit</a> -->
         <button type="submit" name="delete" class="inline-delete-btn" onclick="return confirm('delete this post?');">delete</button>
         <a href="./view.php" class="inline-option-btn">go back</a>
      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">no posts added yet! <a href="add_post.php" class="btn" style="margin-top:1.5rem; display: inline-block;">add post</a></p>';
      }
   ?>

</section>

<section class="comments" style="padding-top: 0;">
   
   <p class="comment-title">post comments</p>
   <div class="box-container">
   <?php
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
         $select_comments->execute([$get_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)){
      ?>
   <div class="box">
      <div class="user">
         <i class="fas fa-user"></i>
         <div class="user-info">
            <span><?= $fetch_comments['user_name']; ?></span>
            <div><?= $fetch_comments['commented_at']; ?></div>
         </div>
      </div>
      <div class="text"><?= $fetch_comments['comment']; ?></div>
      <form action="" method="POST">
         <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
         <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delete this comment?');">delete comment</button>
      </form>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">no comments added yet!</p>';
      }
   ?>
   </div>

</section>

<!-- custom js file link  -->
<script src="../js/ad_script.js"></script>

</body>
</html>
