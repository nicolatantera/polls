<?php
    session_start();

    $genres = ['School', 'Animals', 'Art', 'Book', 'Week', 'Web development', 'Web design', 'Week', 'Tv', 'Startup', 'Sport', 'Social', 'Psychology', 'Phylosophy', 'Science', 'Physics', 'Random', 'Politics', 'News', 'Music', 'Videos', 'Gaming', 'Youtube', 'Investment', 'History', 'Healthcare', 'Culture', 'World', 'Travel', 'Drink', 'Food', 'Crypto', 'Hobby'];
    sort($genres);
    
    $users = json_decode(file_get_contents('../json/users.json'), true);
    $user = $users[$_SESSION['user-id']]['username'];

    $groups = json_decode(file_get_contents('../json/groups.json'), true);
    $group_names = array_map(fn($g) => $g['group'], $groups);

    $_SESSION['num-options'] = $_SESSION['num-options'] ?? 3;
    $max = 10;

    
    $question = $_POST['question'] ?? '';
    $option = $_POST['option'] ?? [];
    $genre = $_POST['genre'] ?? '';
    $group_name = $_POST['group-name'] ?? '';
    $multiple = $_POST['multiple'] ?? false;
    $multiple = filter_var($multiple, FILTER_VALIDATE_BOOLEAN);
    $deadline = $_POST['deadline'] ?? date('Y-m-d');
    
    if (isset($_POST['add-option'])) {
        if ($_SESSION['num-options'] < $max)
            $_SESSION['num-options']++;
    }
    if (isset($_POST['remove-option'])) {
        if ($_SESSION['num-options'] > 1)
            $_SESSION['num-options']--;
    }
    
    $errors = [];
    if ($_POST && isset($_POST['submit-poll'])) {
        if (trim($question) === '')
            $errors['question'] = 'The question is required.';
        else if (count(explode(' ', $question)) < 5)
            $errors['question'] = 'Please, insert at least five words.';

        foreach ($option as $i => $o) {
            if (trim($o) === '')
                $errors['option-'.$i+1] = 'This option is required.';
        }

        if (trim($genre) === '')
            $errors['genre'] = 'Please, select a genre.';
        else if (!in_array($genre, $genres))
            $errors['genre'] = 'Please, select a valid genre.';

        if (count(explode('-', $deadline)) !== 3)
            $errors['deadline'] = 'Please, select a valid date.';
        else if ($deadline === date('Y-m-d')) 
            $errors['deadline'] = 'Please, select a future date.';

        if (count($errors) === 0) {
            $polls = json_decode(file_get_contents('../json/polls.json'), true);
            $next = !empty($polls) ? (max(array_keys($polls)))+1 : 1;

            $answers = [];
            foreach($option as $o) {
                $answers[$o] = [];
            }

            $polls[$next] = [
                'id' => strval($next),
                'genre' => $genre,
                'question' => $question,
                'options' => $option,
                'group' => $group_name,
                'isMultiple' => $multiple,
                'createdAt' => date('Y-m-d'),
                'deadline' => $deadline,
                'answers' => $answers,
                'voted' => []
            ];
            file_put_contents('../json/polls.json', json_encode($polls, JSON_PRETTY_PRINT));

            if (trim($group_name) !== '') {
                foreach($groups as $i => $group) {
                    if ($group['group'] === $group_name) {
                        array_push($groups[$i]['polls'], strval($next));
                        break;
                    }
                }
            }
            file_put_contents('../json/groups.json', json_encode($groups, JSON_PRETTY_PRINT));

            header('location: index.php');
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
  <script src="../js/admin_create.js" defer></script>

  <title>Admin - Create poll</title>
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
      Create a poll
    </div>

    <div class="admin-create-container">
      <form action="" method="post" novalidate>
        <div class="create-question">
          <p>Poll question</p>
          <input class="question-input" name="question" placeholder="Eg. What is your favourite colour?"
            value="<?= $question ?>"></input>
          <span><?= $errors['question'] ?? '' ?></span>
        </div>
        <div class="create-option-container">
          <?php for ($i = 1; $i <= $_SESSION['num-options'] && $i <= $max; $i++): ?>
          <div class="create-option">
            <p>Poll option</p>
            <input type="text" name="option[]" placeholder="Eg. Option <?= $i ?>"
              value="<?= !empty($option[$i - 1]) ? $option[$i - 1] : '' ?>">
            <span><?= $errors['option-'.$i] ?? '' ?></span>
          </div>
          <?php endfor; ?>
        </div>
        <div class="change-num-option">
          <button type="submit" name="add-option" class="increase-option">add option</button>
          <button type="submit" name="remove-option" class="decrease-option">remove option</button>
        </div>
        <hr>
        <div class="group-category">
          <p>Group &nbsp ( leave empty for a public poll )</p>
          <select name="group-name">
            <option value=""></option>
            <?php foreach($group_names as $g): ?>
            <option value="<?= $g ?>" <?= $group_name === $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <hr>
        <div class="create-poll-options">
          <div class="create-category">
            <p>Poll category</p>
            <select name="genre">
              <option value=""></option>
              <?php foreach($genres as $g): ?>
              <option value="<?= $g ?>" <?= $genre === $g ? 'selected' : '' ?>><?= $g ?></option>
              <?php endforeach; ?>
            </select>
            <span><?= $errors['genre'] ?? '' ?></span>
          </div>
          <div class="options-multiple">
            <p>Multiple options</p>
            <input class="checkbox" type="checkbox" name="multiple" <?= $multiple ? 'checked' : '' ?>>
            <span><?= $errors['multiple'] ?? '' ?></span>
          </div>
          <div class="options-end-date">
            <p>Poll deadline</p>
            <input type="date" name="deadline" value="<?= $deadline ?>">
            <span><?= $errors['deadline'] ?? '' ?></span>
          </div>
        </div>
        <button class="submit-poll" type="submit" name='submit-poll'>create your poll</button>
      </form>
      <a href="index.php">homepage</a>
    </div>
  </div>




</body>

</html>