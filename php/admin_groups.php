<?php
    session_start();
    
    $users = json_decode(file_get_contents('../json/users.json'), true);
    $user = $users[$_SESSION['user-id']]['username'];

    $polls = json_decode(file_get_contents('../json/polls.json'), true);
    $groups = json_decode(file_get_contents('../json/groups.json'), true);
    
    $group = $_POST['group'] ?? '';
    $user_select = $_POST['user'] ?? [];
    
    $errors = [];
    if ($_POST) {
        if (trim($group) === '')
            $errors['group'] = 'The group name is required.';
        else if (count(explode(' ', $group)) < 2)
            $errors['group'] = 'Please, insert at least two words.';
        else if (in_array(strtolower($group), groupName($groups)))
            $errors['group'] = 'Name already selected.';
        
        if (empty($user_select))
            $errors['user'] = 'Please, select at least one user.';

        
        if (count($errors) === 0) {
            $next = !empty($groups) ? (max(array_keys($groups)))+1 : 1;

            $groups[$next] = [
                'id' => strval($next),
                'group' => $group,
                'members' => $user_select,
                'polls' => []
            ];
            file_put_contents('../json/groups.json', json_encode($groups, JSON_PRETTY_PRINT));
            header('location: admin_groups.php');
        }

    }


    function groupName($groups) {
        $results = [];
        foreach($groups as $group) {
            array_push($results, strtolower($group['group']));
        }
        return $results;
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
  <script src="../js/admin_create.js" defer></script>

  <title>Admin - Create groups</title>
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
    <div class="admin-title">
      Create a group
    </div>

    <div class="admin-create-container">
      <form action="" method="post" novalidate>
        <div class="create-question">
          <p>Group name</p>
          <input class="group-input" name="group" placeholder="Eg. The power rangers" value="<?= $group ?>"></input>
          <span><?= $errors['group'] ?? '' ?></span>
        </div>
        <div class="multiple-select">
          <p>User names</p>
          <select name="user[]" multiple>
            <?php foreach($users as $u): ?>
            <?php if($u['username'] !== 'admin'): ?>
            <option value="<?= $u['username'] ?>" <?= $user_select === $u['username'] ? 'selected' : '' ?>>
              <?= $u['username'] ?></option>
            <?php endif; ?>
            <?php endforeach; ?>
          </select>
          <span><?= $errors['user'] ?? '' ?></span>
        </div>
        <button class="submit-poll" type="submit" name='submit-poll'>create group</button>
      </form>
      <a href="index.php">homepage</a>
    </div>

    <div class="admin-title groups">
      Groups
    </div>

    <div class="admin-groups">
      <?php if(isset($groups)): ?>
      <?php foreach($groups as $i => $group): ?>
      <div class="group">
        <p class="group-title"><?= $group['group'] ?></p>
        <hr>
        <?php foreach($group['members'] as $member): ?>
        <p class="group-member"><?= $member ?></p>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>




</body>

</html>