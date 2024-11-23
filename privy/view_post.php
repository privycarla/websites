<?php

include './components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include './components/like_post.php';

$get_id = $_GET['post_id'];

if(isset($_POST['add_comment'])){

   $user_id = $_POST['user_id'];
   $user_id = filter_var($user_id, FILTER_SANITIZE_SPECIAL_CHARS);
   $user_name = $_POST['user_name'];
   $user_name = filter_var($user_name, FILTER_SANITIZE_SPECIAL_CHARS);
   $comment = $_POST['comment'];
   $comment = filter_var($comment, FILTER_SANITIZE_SPECIAL_CHARS);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ? AND user_id = ? AND user_name = ? AND comment = ?");
   $verify_comment->execute([$get_id, $user_id, $user_name, $comment]);

   if($verify_comment->rowCount() > 0){
      $message[] = 'comment already added!';
   }else{
      $insert_comment = $conn->prepare("INSERT INTO `comments`(post_id, user_id, user_name, comment) VALUES(?,?,?,?)");
      $insert_comment->execute([$get_id, $user_id, $user_name, $comment]);
      $message[] = 'new comment added!';
   }

}

if(isset($_POST['edit_comment'])){
   $edit_comment_id = $_POST['edit_comment_id'];
   $edit_comment_id = filter_var($edit_comment_id, FILTER_SANITIZE_SPECIAL_CHARS);
   $comment_edit_box = $_POST['comment_edit_box'];
   $comment_edit_box = filter_var($comment_edit_box, FILTER_SANITIZE_SPECIAL_CHARS);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE comment = ? AND id = ?");
   $verify_comment->execute([$comment_edit_box, $edit_comment_id]);

   if($verify_comment->rowCount() > 0){
      $message[] = 'comment already added!';
   }else{
      $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
      $update_comment->execute([$comment_edit_box, $edit_comment_id]);
      $message[] = 'your comment edited successfully!';
   }
}

if(isset($_POST['delete_comment'])){
   $delete_comment_id = $_POST['comment_id'];
   $delete_comment_id = filter_var($delete_comment_id, FILTER_SANITIZE_SPECIAL_CHARS);
   $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
   $delete_comment->execute([$delete_comment_id]);
   $message[] = 'comment deleted successfully!';
}

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
   <title>view post</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include './components/user_header.php'; ?>
<!-- header section ends -->

<?php
   if(isset($_POST['open_edit_box'])){
   $comment_id = $_POST['comment_id'];
   $comment_id = filter_var($comment_id, FILTER_SANITIZE_SPECIAL_CHARS);
?>
   <section class="comment-edit-form">
   <p>Edit comment</p>
   <?php
      $select_edit_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
      $select_edit_comment->execute([$comment_id]);
      $fetch_edit_comment = $select_edit_comment->fetch(PDO::FETCH_ASSOC);
   ?>
   <form action="" method="POST">
      <input type="hidden" name="edit_comment_id" value="<?= $comment_id; ?>">
      <textarea name="comment_edit_box" required cols="30" rows="10" placeholder="please enter your comment"><?= $fetch_edit_comment['comment']; ?></textarea>
      <button type="submit" class="inline-btn" name="edit_comment">edit comment</button>
      <div class="inline-option-btn" onclick="window.location.href = 'view_post.php?post_id=<?= $get_id; ?>';">cancel edit</div>
   </form>
   </section>
<?php
   }
?>

<section class="viewposts-container" style="padding-bottom: 0;">

   <div class="viewbox-container">

      <?php
         $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ? AND id = ?");
         $select_posts->execute(['active', $get_id]);
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
               <a href="#"><?= $fetch_posts['name']; ?></a>
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
         <div class="viewpost-content"><?= $fetch_posts['content']; ?></div>
         <div class="viewicons">
            <div><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></div>
            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if($confirm_likes->rowCount() > 0){ echo 'color:var(--red);'; } ?>  "></i><span>(<?= $total_post_likes; ?>)</span></button>
         </div>
      
      </form>
      <?php
         }
      }else{
         echo '<p class="empty">no posts found!</p>';
      }
      ?>
   </div>

</section>

<section class="comments-container">

   <p class="comment-title">add comment</p>
   <?php
      if($user_id != ''){  
         $select_user_id = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
         $select_user_id->execute([$get_id]);
         $fetch_user_id = $select_user_id->fetch(PDO::FETCH_ASSOC);
   ?>
   <form action="" method="post" class="add-comment">
      <input type="hidden" name="user_id" value="<?= $user_id; ?>">
      <input type="hidden" name="user_name" value="<?= $fetch_profile['username']; ?>">
      <?php if (!empty($fetch_profile) && $fetch_profile['image'] != '') { ?>
                    <p class="user"><img src="uploaded_img/<?= $fetch_profile['image']; ?>" class="user-profile-image" style="width: 4.7rem; height: 4.5rem;"> <a href="#"><?= $fetch_profile['username']; ?></a></p>
               <?php } else { ?>
                    <i class="fas fa-user"></i>
               <?php } ?>
      <textarea name="comment" maxlength="1000" class="comment-box" cols="30" rows="10" placeholder="write your comment" required></textarea>
      <input type="submit" value="add comment" class="inline-btn" name="add_comment">
   </form>
   <?php
   }else{
   ?>
   <div class="add-comment">
      <p>please login to add or edit your comment</p>
      <a href="login.php" class="inline-btn">login now</a>
   </div>
   <?php
      }
   ?>
   
   <p class="comment-title">post comments</p>
   <div class="user-comments-container">
      <?php
         $select_comments = $conn->prepare("SELECT comments.*, users.username, users.image AS user_image FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ?");
         $select_comments->execute([$get_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="show-comments" style="<?php if($fetch_comments['user_id'] == $user_id){echo 'order:-1;'; } ?>">
         <div class="comment-user">
            <?php if (!empty($fetch_comments['user_image'])) { ?>
               <img src="uploaded_img/<?= $fetch_comments['user_image']; ?>" class="user-profile-image" style="width:  4.7rem ; height: 4.5rem;">
            <?php } else { ?>
               <i class="fas fa-user"></i>
            <?php } ?>
            <div>
               <span><?= $fetch_comments['user_name']; ?></span>
               <div><?= $fetch_comments['commented_at']; ?></div>
            </div>
         </div>
         <div class="comment-box" style="<?php if($fetch_comments['user_id'] == $user_id){echo 'color:var(--white); background:var(--black);'; } ?>"><?= $fetch_comments['comment']; ?></div>
         <?php
            if($fetch_comments['user_id'] == $user_id){  
         ?>
         <form action="" method="POST">
            <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
            <button type="submit" class="inline-option-btn" name="open_edit_box">edit comment</button>
            <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delete this comment?');">delete comment</button>
         </form>
         <?php
         }
         ?>
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
<script src="js/script.js"></script>

</body>
</html>
