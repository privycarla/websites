<?php
include './components/connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:home.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch user details
$query_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
if ($query_user === false) {
    die("Error preparing query: " . htmlspecialchars($conn->errorInfo()[2]));
}

$query_user->execute([$user_id]);
if ($query_user->errorCode() !== '00000') {
    die("Error executing query: " . htmlspecialchars($query_user->errorInfo()[2]));
}

$user = $query_user->fetch(PDO::FETCH_ASSOC);

// Check if user data is retrieved
if (!$user) {
    die("Error: User not found.");
}

// Fetch user posts
$query_posts = $conn->prepare("SELECT * FROM `posts` WHERE user_id = ? ORDER BY id DESC");
if ($query_posts === false) {
    die("Error preparing query: " . htmlspecialchars($conn->errorInfo()[2]));
}

$query_posts->execute([$user_id]);
if ($query_posts->errorCode() !== '00000') {
    die("Error executing query: " . htmlspecialchars($query_posts->errorInfo()[2]));
}

if (isset($_POST['delete_post'])) {
    if (isset($_POST['post_id'])) {
        $post_id = (int)$_POST['post_id']; // Cast to integer
        if ($post_id <= 0) {
            die("Invalid post ID.");
        }
    } else {
        die("Post ID not provided.");
    }

    $post_id = filter_var($post_id, FILTER_SANITIZE_NUMBER_INT);
    $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ? AND user_id = ?");
    $delete_image->execute([$post_id, $user_id]);
    $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);

    // Check if the fetch was successful
    if ($fetch_delete_image && isset($fetch_delete_image['image']) && $fetch_delete_image['image'] != '') {
        $image_path = '../uploaded_img/' . $fetch_delete_image['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        } else {
            echo "Warning: Image file not found at $image_path";
        }
    }
    

    $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ? AND user_id = ?");
    $delete_post->execute([$post_id, $user_id]);

    if ($delete_post->errorCode() !== '00000') {
        die("Error deleting post: " . htmlspecialchars($delete_post->errorInfo()[2]));
    }

    $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
    $delete_comments->execute([$post_id]);

    $message[] = 'Post deleted successfully!';
}

$posts = $query_posts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User </title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- header section starts  -->
<?php include './components/user_header.php'; ?>
<!-- header section ends -->

<section class="dashboard-container">
    
<h1> <?= htmlspecialchars($fetch_profile['firstname'] ?? '') . ' ' . htmlspecialchars($fetch_profile['lastname'] ?? 'User'); ?></h1>
    
<div class="user-info">
        <h2>Your Information</h2>
        <p><strong>First Name:</strong> <?= htmlspecialchars($fetch_profile['firstname'] ?? ''); ?></p>
        <p><strong>Last Name:</strong> <?= htmlspecialchars($fetch_profile['lastname'] ?? ''); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($fetch_profile['email'] ?? ''); ?></p>
        <p><strong>Username:</strong> <?= htmlspecialchars($fetch_profile['username'] ?? ''); ?></p>
        <?php if (!empty($user['image'])): ?>
            <img src="uploaded_img/<?= htmlspecialchars($user['image']); ?>" alt="Profile Picture" class="profile-pic">
        <?php endif; ?>
    </div>

    <div class="user-posts">
        <h2>Your Posts</h2>
        <?php if (count($posts) > 0): ?>
            <ul>
                <?php foreach ($posts as $post): ?>
                    <li>
                        <h3><?= htmlspecialchars($post['title']); ?></h3>
                        <p><?= ($post['content']); ?></p>
                        <?php if (isset($post['created_at'])): ?>
                            <small>Posted on: <?= htmlspecialchars($post['created_at']); ?></small>
                        <?php endif; ?>
                        <?php if (!empty($post['image'])): ?>
                            <img src="uploaded_img/<?= htmlspecialchars($post['image']); ?>" alt="Post Image">
                        <?php endif; ?>

                        <!-- Form for delete post -->
                        <form action="" method="post">
                            <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
                            <input type="submit" value="Delete Post" class="delete-btn" name="delete_post">
                        </form>
                        
                        <!-- Link for edit post -->
                        <a href="./editpost.php?= $post['id']; ?>" class="btn">Edit Post</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>You have not made any posts yet.</p>
        <?php endif; ?>
    </div>
</section>

<!-- custom js file link -->
<script src="js/script.js"></script>

</body>
</html>
