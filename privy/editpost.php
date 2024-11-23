<?php

// Start the session at the beginning
session_start();

// Verify if the connection file exists and include it
// if (file_exists('../components/connect.php')) {
//     // include '../components/connect.php';
// } else {
//     die("Error: Connection file not found.");
// }

include './components/connect.php';

// Check if the connection variable is set
if (!isset($conn)) {
    die("Error: Database connection not established.");
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['save'])) {

    if (isset($_GET['id'])) {
        $post_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        $title = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
        $content = filter_var($_POST['content'], FILTER_SANITIZE_SPECIAL_CHARS);
        $category = filter_var($_POST['category'], FILTER_SANITIZE_SPECIAL_CHARS);
        $status = filter_var($_POST['status'], FILTER_SANITIZE_SPECIAL_CHARS);

        $update_post = $conn->prepare("UPDATE `posts` SET title = ?, content = ?, category = ?, status = ? WHERE id = ?");
        $update_post->execute([$title, $content, $category, $status, $post_id]);

        $message[] = 'Post updated!';

        $old_image = $_POST['old_image'];
        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_SPECIAL_CHARS);
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_img/' . $image;

        $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND user_id = ?");
        $select_image->execute([$image, $user_id]);

        if (!empty($image)) {
            if ($image_size > 2000000) {
                $message[] = 'Image size is too large!';
            } elseif ($select_image->rowCount() > 0 && $image != '') {
                $message[] = 'Please rename your image!';
            } else {
                $update_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
                move_uploaded_file($image_tmp_name, $image_folder);
                $update_image->execute([$image, $post_id]);
                if ($old_image != $image && $old_image != '') {
                    unlink('../uploaded_img/' . $old_image);
                }
                $message[] = 'Image updated!';
            }
        }
    } else {
        $message[] = 'Post ID not provided.';
    }
}

if (isset($_POST['delete_post'])) {
   $post_id = $_POST['post_id'];
   $post_id = filter_var($post_id, FILTER_SANITIZE_SPECIAL_CHARS);
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
   $delete_image->execute([$post_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if($fetch_delete_image['image'] != ''){
      unlink('../uploaded_img/'.$fetch_delete_image['image']);
   }
   $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
   $delete_post->execute([$post_id]);
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
   $delete_comments->execute([$post_id]);
   $message[] = 'post deleted successfully!';

}

if (isset($_POST['delete_image'])) {
   $empty_image = '';
   $post_id = $_POST['post_id'];
   $post_id = filter_var($post_id, FILTER_SANITIZE_SPECIAL_CHARS);
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
   $delete_image->execute([$post_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if($fetch_delete_image['image'] != ''){
      unlink('../uploaded_img/'.$fetch_delete_image['image']);
   }
   $unset_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
   $unset_image->execute([$empty_image, $post_id]);
   $message[] = 'image deleted successfully!';

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="./css/style.css">

</head>
<body>

<?php 
    include './components/user_header.php'; 
?>

<section class="post-editor">

    <!-- <h1 class="heading">Edit Post</h1> -->

    <?php
    if (isset($_GET['id'])) {
        $post_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
        $select_posts->execute([$post_id]);
        if ($select_posts->rowCount() > 0) {
            while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="old_image" value="<?= htmlspecialchars($fetch_posts['image']); ?>">
                    <input type="hidden" name="post_id" value="<?= htmlspecialchars($fetch_posts['id']); ?>">
                    <p>Post Status <span>*</span></p> 
                     <select name="status" class="box" required>
                        <option value="<?= htmlspecialchars($fetch_posts['status']); ?>" selected><?= htmlspecialchars($fetch_posts['status']); ?></option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select> 
                    <p>Title <span>*</span></p>
                    <input type="text" name="title" maxlength="100" required placeholder="Add post title" class="box" value="<?= htmlspecialchars($fetch_posts['title']); ?>">
                    <p>Content <span>*</span></p>
                    <textarea name="content" class="box" required maxlength="10000" placeholder="Write your content" cols="30" rows="10"><?= ($fetch_posts['content']); ?></textarea>
                    <p>Category <span>*</span></p>
                    <select name="category" class="box" required>
                        <option value="<?= htmlspecialchars($fetch_posts['category']); ?>" selected><?= htmlspecialchars($fetch_posts['category']); ?></option>
                        <option value="nature">Nature</option>
                        <option value="arts">Art</option>
                        <option value="memes">Meme</option>
                        <option value="travel">Travel</option>
                        <option value="photography">Photography</option>
                        <option value="poems">Poem</option>
                        <option value="fitness">Fitness</option>
                        <option value="design">Design</option>
                        <option value="fashion">Fashion</option>
                        <option value="cottagecore">Cottagecore</option>
                        <option value="music">Music</option>
                        <option value="moodboard">Moodboard</option>
                        <option value="kpop">Kpop</option>
                        <option value="entertainment">Entertainment</option>
                        <option value="foods">Foods</option>
                        <option value="lifestyle">Lifestyle</option>
                        <option value="movie">Movie</option>
                        <option value="books">Book</option>
                        <option value="shopping">Shopping</option>
                        <option value="animations">Animation</option>
                    </select>
                    <p>Image</p>
                    <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
                    <?php if ($fetch_posts['image'] != '') { ?>
                        <img src="../uploaded_img/<?= htmlspecialchars($fetch_posts['image']); ?>" class="image" alt="">
                        <input type="submit" value="Delete Image" class="inline-delete-btn" name="delete_image">
                    <?php } ?>
                    <div class="flex-btn">
                        <input type="submit" value="Save Post" name="save" class="btn">
                        <input type="submit" value="Delete Post" class="delete-btn" name="delete_post">
                    </div>
                </form>
                <?php
            }
        } else {
            echo '<p class="empty">No posts found!</p>';
        }
    } else {
        echo '<p class="empty">Post ID not provided.</p>';
    }
    ?>

</section>

<script src="./js/script.js"></script>
</body>
</html>
