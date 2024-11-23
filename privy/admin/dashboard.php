<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location: ad_login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="../css//ad_style.css">
</head>

<body>

    <?php include './ad_header.php' ?>

    <!-- admin dashboard section starts -->

    <section class="dashboard">

        <h1 class="heading">Dashboard</h1>

        <div class="box-container">

            <div class="box">
                <h3>Profile</h3>
                <p><?= isset($fetch_profile['name']) ? $fetch_profile['name'] : ''; ?></p>
                <a href="./up_prof.php" class="btn">Update Profile</a>
            </div>

            <!-- <div class="box">
                <?php
                // Fetch the number of all posts
                $select_posts = $conn->prepare("SELECT * FROM `posts`");
                $select_posts->execute();
                $numbers_of_posts = $select_posts->rowCount();
                ?>
                <h3><?= $numbers_of_posts; ?></h3>
                <p>Posts Added</p>
                <a href="../admin/add_post.php" class="btn">Add New Post</a>
            </div> -->

            <div class="box">
                <?php
                // Fetch the number of active posts
                $select_active_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ?");
                $select_active_posts->execute(['active']);
                $numbers_of_active_posts = $select_active_posts->rowCount();
                ?>
                <h3><?= $numbers_of_active_posts; ?></h3>
                <p>Active Posts</p>
                <a href="./view.php" class="btn">See Posts</a>
            </div>

            <div class="box">
                <?php
                // Fetch the number of inactive posts
                $select_inactive_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ?");
                $select_inactive_posts->execute(['inactive']);
                $numbers_of_inactive_posts = $select_inactive_posts->rowCount();
                ?>
                <h3><?= $numbers_of_inactive_posts; ?></h3>
                <p>Inactive Posts</p>
                <a href="./view.php" class="btn">See Posts</a>
            </div>

            <div class="box">
                <?php
                // Fetch the number of users
                $select_users = $conn->prepare("SELECT * FROM `users`");
                $select_users->execute();
                $numbers_of_users = $select_users->rowCount();
                ?>
                <h3><?= $numbers_of_users; ?></h3>
                <p>Users Account</p>
                <a href="./user_acc.php" class="btn">See Users</a>
            </div>

            <div class="box">
                <?php
                // Fetch the number of admins
                $select_admins = $conn->prepare("SELECT * FROM `admin`");
                $select_admins->execute();
                $numbers_of_admins = $select_admins->rowCount();
                ?>
                <h3><?= $numbers_of_admins; ?></h3>
                <p>Admins Account</p>
                <a href="./account.php" class="btn">See Admins</a>
            </div>

        </div>

    </section>

    <!-- admin dashboard section ends -->

    <!-- custom js file link -->
    <script src="../js/ad_script.js"></script>

</body>

</html>
