<?php
    session_start();
    
    $users = json_decode(file_get_contents('../json/users.json'), true);
    $user = $users[$_SESSION['user-id']]['username'];


    

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="../css/style.css">
  <script src="../js/script.js" defer></script>

  <title>Admin - User polls</title>
</head>

<body>

  <div class="image-container">
    <img class="img" src="../img/dark-background.jpg" alt="">
  </div>

  <div class="header">
    <div class="header-title">
      <img src="../img/poll_logo_white.png" alt="logo">
    </div>
    <div class="header-auth">
      <a href='logout.php'><?= $user ?></a>
    </div>
  </div>

  <div class="admin-container">
    <div class="admin-create">
      <a href="admin_create.php">new poll</a>
    </div>
    <div class="admin-delete">
      <a href="admin_delete.php">delete poll</a>
    </div>
    <div class="admin-edit">
      <a href="admin_edit.php">edit poll</a>
    </div>
  </div>




</body>

</html>