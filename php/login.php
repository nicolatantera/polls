<?php
    session_start();

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $errors = [];
    if ($_POST) {

        if (trim($username) === '') {
            $errors['username'] = 'Username required';
        } 
        else if (count(explode(' ', $username)) > 1) {
            $errors['username'] = 'Spaces are not accepted';
        }

        if (trim($password) === '') {
            $errors['password'] = 'Password required';
        }
        else if (count(explode(' ', $password)) > 1) {
            $errors['password'] = 'Spaces are not accepted';
        }

        if (count($errors) == 0) {
            $users = json_decode(file_get_contents('../json/users.json'), true);
            $user = array_values(array_filter($users, fn($u) => $u['username'] === $username));
            if (!$user) {
                // ERROR USER
                $errors['username'] = 'Invalid username and/or password';
                $errors['password'] = 'Invalid username and/or password';
            }
            else {
                if (!password_verify($password, $user[0]['password'])) {
                    // ERROR PASSWORD
                    $errors['username'] = 'Invalid username and/or password';
                    $errors['password'] = 'Invalid username and/or password';
                }
                else {
                    
                    // SUCCESSFUL LOGIN
                    $_SESSION['user-id'] = $user[0]['id'];
                    
                    if (isset($_GET['poll'])) {
                        $poll = $_GET['poll'];
                        header("location: voting.php?poll=$poll");
                        exit();
                    }
                    else {
                        header("location: index.php");
                        exit();
                    }
                }
            }
        }
        

        
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="../css/style.css">
  <script src="../js/script.js" defer></script>

  <title>Login - User polls</title>
</head>

<body>

  <div class="image-container">
    <img class="img" src="../img/dark-background.jpg" alt="">
  </div>

  <div class="header">
    <div class="header-title">
      <img src="../img/poll_logo_white.png" alt="logo">
    </div>
  </div>

  <div class="login-container">
    <div class="logo"><img src="../img/poll_logo.png" alt="logo"></div>
    <div class="login-title">sign in</div>
    <form action="" method="post" novalidate>
      <input class="login-username" type="text" name="username" value="" placeholder=" ">
      <span class="floating-username">Username</span>
      <span class="error error-login-username"><?= $errors['username'] ?? '' ?></span>

      <input class="login-password" type="password" name="password" value="" placeholder=" ">
      <span class="floating-password">Password</span>
      <span class="error error-login-password"><?= $errors['password'] ?? '' ?></span>

      <button type="submit">sign in</button>
    </form>
    <hr>
    <p>Don't have an account?</p>
    <div class="go-to-register">
      <form action="register.php" method='post' novalidate>
        <input type='submit' name='go-to-register' value='Create new account'>
      </form>
    </div>

    <a href="index.php">homepage</a>
  </div>

</body>

</html>