<?php
include '../components/connect.php';
session_start();

// Uncomment this block if you want to ensure the user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header('location: login.php');
//     exit();
// }

// Get the current user's ID if they are logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($user_id) {
    // Fetch user profile if the user is logged in
    $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_profile->execute([$user_id]);
    $user_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
} else {
    $user_profile = null;
}

if (isset($_POST['delete'])) {
    $p_id = filter_var($_POST['post_id'], FILTER_SANITIZE_NUMBER_INT);
    $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
    $delete_image->execute([$p_id]);
    $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
    if ($fetch_delete_image['image'] != '') {
        unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }
    $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
    $delete_post->execute([$p_id]);
    $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
    $delete_comments->execute([$p_id]);
    $message[] = 'Post deleted successfully!';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="../css/ad_style.css">
</head>

<body>

    <?php include '../admin/ad_header.php' ?>

    <section class="show-posts">

        <!-- <h1 class="heading">Posts</h1> -->

        <div class="box-container">
            <?php
            // Fetch all posts
            $select_posts = $conn->prepare("SELECT posts.*, users.username FROM `posts` INNER JOIN `users` ON posts.user_id = users.id");
            $select_posts->execute();

            // Debugging: Check if query execution returned any errors
            if ($select_posts->errorCode() != '00000') {
                echo "SQL Error: " . implode(", ", $select_posts->errorInfo());
                exit();
            }

            if ($select_posts->rowCount() > 0) {
                while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
                    $post_id = $fetch_posts['id'];

                    $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
                    $count_post_comments->execute([$post_id]);
                    $total_post_comments = $count_post_comments->rowCount();

                    $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
                    $count_post_likes->execute([$post_id]);
                    $total_post_likes = $count_post_likes->rowCount();
            ?>
                    <form method="post" class="box">
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id); ?>">
                        <?php if (!empty($fetch_posts['image'])) { ?>
                            <img src="../uploaded_img/<?= htmlspecialchars($fetch_posts['image']); ?>" class="image" alt="">
                        <?php } ?>
                        <div class="status" style="background-color:<?php if ($fetch_posts['status'] == 'active') {
                                                                            echo 'limegreen';
                                                                        } else {
                                                                            echo 'coral';
                                                                        }; ?>;"><?= htmlspecialchars($fetch_posts['status']); ?></div>
                        <div class="title"><?= htmlspecialchars($fetch_posts['username']); ?></div>
                        <div class="username"><?= htmlspecialchars($fetch_posts['title']); ?></div>
                        <div class="posts-content"><?= htmlspecialchars($fetch_posts['content']); ?></div>
                        <div class="icons">
                            <div class="likes"><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
                            <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
                        </div>
                        <?php if ($user_id) { // Show delete button only if a user is logged in ?>
                            <div class="flex-btn">
                                <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Delete this post?');">Delete</button>
                            </div>
                        <?php } ?>
                        <a href="read.php?post_id=<?= htmlspecialchars($post_id); ?>" class="btn">View Post</a>
                    </form>
            <?php
                }
            } else {
                echo '<p class="empty">No posts added yet!</p>';
            }
            ?>
        </div>

    </section>

    <!-- custom js file link -->
    <script src="../js/ad_script.js"></script>

</body>

</html>
