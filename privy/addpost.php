<?php

include './components/connect.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['publish'])){

   $name = $_POST['name'];
   $name = filter_var($name,FILTER_SANITIZE_SPECIAL_CHARS);
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_SPECIAL_CHARS);
   $content = $_POST['content'];
   $content = filter_var($content, FILTER_SANITIZE_SPECIAL_CHARS);
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_SPECIAL_CHARS);
   $status = 'active';
   
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_SPECIAL_CHARS);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img'.$image;

   $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND user_id = ?");
   $select_image->execute([$image, $user_id]);

   if(isset($image)){
      if($select_image->rowCount() > 0 AND $image != ''){
         $message[] = 'image name repeated!';
      }elseif($image_size > 2000000){
         $message[] = 'images size is too large!';
      }else{
         move_uploaded_file($image_tmp_name, $image_folder);
      }
   }else{
      $image = '';
   }

   if($select_image->rowCount() > 0 AND $image != ''){
      $message[] = 'please rename your image!';
   }else{
      $insert_post = $conn->prepare("INSERT INTO `posts`(user_id, name, title, content, category, image, status) VALUES(?,?,?,?,?,?,?)");
      $insert_post->execute([$user_id, $name, $title, $content, $category, $image, $status]);
      $message[] = 'post published!';
   }
   
}

// if(isset($_POST['draft'])){

//    $name = $_POST['name'];
//    $name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
//    $title = $_POST['title'];
//    $title = filter_var($title, FILTER_SANITIZE_SPECIAL_CHARS);
//    $content = $_POST['content'];
//    $content = filter_var($content, FILTER_SANITIZE_SPECIAL_CHARS);
//    $category = $_POST['category'];
//    $category = filter_var($category, FILTER_SANITIZE_SPECIAL_CHARS);
//    $status = 'deactive';
   
//    $image = $_FILES['image']['name'];
//    $image = filter_var($image, FILTER_SANITIZE_SPECIAL_CHARS);
//    $image_size = $_FILES['image']['size'];
//    $image_tmp_name = $_FILES['image']['tmp_name'];
//    $image_folder = '../uploaded_img/'.$image;

//    $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND user_id = ?");
//    $select_image->execute([$image, $user_id]); 

//    if(isset($image)){
//       if($select_image->rowCount() > 0 AND $image != ''){
//          $message[] = 'image name repeated!';
//       }elseif($image_size > 2000000){
//          $message[] = 'images size is too large!';
//       }else{
//          move_uploaded_file($image_tmp_name, $image_folder);
//       }
//    }else{
//       $image = '';
//    }

//    if($select_image->rowCount() > 0 AND $image != ''){
//       $message[] = 'please rename your image!';
//    }else{
//       $insert_post = $conn->prepare("INSERT INTO `posts`(user_id, name, title, content, category, image, status) VALUES(?,?,?,?,?,?,?)");
//       $insert_post->execute([$user_id, $name, $title, $content, $category, $image, $status]);
//       $message[] = 'draft saved!';
//    }

// }

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
   <link rel="stylesheet" href="./css/style.css">

</head>
<body>


<?php include './components/user_header.php' ?>

<section class="post-editor">

   <!-- <h1 class="heading">add new post</h1> -->

   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="post_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">
      <input type="hidden" name="name" value="<?= $fetch_profile['username']; ?>">
      <p>Title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="Add post title" class="box">
      <p>Content <span>*</span></p>
      <textarea style="overflow: auto;" name="content" class="box" required maxlength="10000" placeholder="Write your content" cols="30" rows="10"></textarea>
      <p>Category <span>*</span></p>
      <select name="category" class="box" required>
         <option value="" selected disabled>-- select category* </option>
         <option value="nature">nature</option>
         <option value="arts">arts</option>
         <option value="memes">meme</option>
         <option value="travel">travel</option>
         <option value="photography">photography</option>
         <option value="poems">poems</option>
         <option value="fitness">fitness</option>
         <option value="design">design</option>
         <option value="fashion">fashion</option>
         <option value="cottagecore">cottagecore</option>
         <option value="music">music</option>
         <option value="moodboard">moodboard</option>
         <option value="kpop">kpop</option>
         <option value="entertainment">entertainment</option>
         <option value="foods">food</option>
         <option value="lifestyle">lifestyle</option>
         <option value="movie">movie</option>
         <option value="books">book</option>
         <option value="shopping">shopping</option>
         <option value="animations">animations</option>
      </select>
      <p>Image</p>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
      <div class="flex-btn">
         <input type="submit" value="publish" name="publish" class="btn">
         <!-- <input type="submit" value="save draft" name="draft" class="option-btn"> -->
      </div>
   </form>

</section>










<!-- custom js file link  -->
<script src="./js/script.js"></script>

</body>
</html>