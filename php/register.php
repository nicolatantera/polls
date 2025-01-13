<?php
    session_start();

    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';

    $errors = [];
    if ($_POST) {
        if (trim($username) === '') {
            $errors['username'] = 'Username required';
        } 
        else if (count(explode(' ', $username)) > 1) {
            $errors['username'] = 'Spaces are not accepted';
        }
        else {
            $users = json_decode(file_get_contents('../json/users.json'), true);
            if (count(array_filter($users, fn($user) => $user['username'] === $username)) !== 0) {
                $errors['username'] = 'Username cannot be chosen';
            }
        }

        if (trim($email) === '') {
            $errors['email'] = 'Email required';
        }
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address';
        }

        if (trim($password) === '') {
            $errors['password'] = 'Password required';
        }
        else if (count(explode(' ', $password)) > 1) {
            $errors['password'] = 'Spaces are not accepted';
        }

        if (trim($confirm_password) === '') {
            $errors['confirm-password'] = 'Password required';
        }
        else if (count(explode(' ', $confirm_password)) > 1) {
            $errors['confirm-password'] = 'Spaces are not accepted';
        }
        else if (strcmp($password, $confirm_password) !== 0) {
            $errors['confirm-password'] = 'Passwords must match';
        }

        if (count($errors) == 0) {
            // form is OK :)
            
            $id = generateRandomString($users);
            $users[$id] = [
                'id' => $id,
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'isAdmin' => false
            ];
            file_put_contents('../json/users.json', json_encode($users, JSON_PRETTY_PRINT));
            header('location: login.php');
        }
    }








// ---------------------- FUNCTIONS -----------------------
    function generateRandomString($users, $length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if (count(array_filter($users, fn($user) => $user['id'] === $randomString)) !== 0) {
            while (count(array_filter($users, fn($user) => $user[$randomString])) !== 0) {
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
            }
        }
        return $randomString;
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

  <title>Register - User polls</title>
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

  <div class="register-container">
    <div class="logo"><img src="../img/poll_logo.png" alt="logo"></div>
    <div class="register-title">sign up</div>
    <form action="register.php" method="post" novalidate>
      <input class="register-username" type="text" name="username" value="<?= $username ?>" placeholder=" "></input>
      <span class="floating-register-username">Username</span>
      <span class="error error-register-username"><?= $errors['username'] ?? '' ?></span>

      <input class="register-email" type="text" name="email" value="<?= $email ?>" placeholder=" ">
      <span class="floating-register-email">Email</span>
      <span class="error error-register-email"><?= $errors['email'] ?? '' ?></span>

      <input class="register-password" type="password" name="password" value="<?= $password ?>" placeholder=" ">
      <span class="floating-register-password">Password</span>
      <span class="error error-register-password"><?= $errors['password'] ?? '' ?></span>

      <input class="register-confirm-password" type="password" name="confirm-password" value="<?= $confirm_password ?>"
        placeholder=" ">
      <span class="floating-confirm-password">Confirm Password</span>
      <span class="error error-register-confirm-password"><?= $errors['confirm-password'] ?? '' ?></span>

      <button type="submit">sign up</button>
    </form>
    <hr>
    <p>Already have an account?</p>
    <div class="go-to-login">
      <form action="login.php" method='post' novalidate>
        <input type='submit' name='go-to-login' value='Sign in'>
      </form>
    </div>

    <a href="index.php">homepage</a>
  </div>

</body>

</html>