<?php
    session_start();

    $polls = json_decode(file_get_contents('../json/polls.json'), true);
    $poll = $polls[$_GET['poll']];
    
    $users = json_decode(file_get_contents('../json/users.json'), true);
    $user = $users[$_SESSION['user-id']]['username'];
    
    
    $error = '';
    if ($_POST) {
        
        if ($poll['isMultiple'] === false) {
            $select = $_POST['select'] ?? '';
            if (trim($select) === '') {
                $error = 'Please, select one option';
            }
            else if (in_array($user, $polls[$_GET['poll']]['answers'][$select])) {
                $error = 'Choose a different option.';
            }
        }
        else {
            $selects = $_POST['selects'] ?? [];
            if (empty($selects)) {
                $error = 'Please, select at least one option';
            }
            else if (count(array_filter($polls[$_GET['poll']]['answers'], fn($a) => in_array($user, $a))) == count($selects) && getChosenOptions($polls, $selects)) {
                $error = 'Choose different options.';
            }
            else if (count($selects) == count($polls[$_GET['poll']]['answers']) && count(array_filter($polls[$_GET['poll']]['answers'], fn($a) => in_array($user, $a))) == count($polls[$_GET['poll']]['answers'])) {
                $error = 'All the options already been selected.';
            }
        }

        if ($error === '' && isset($_POST['submit'])) {
            // form is OK :)
            
            if (!in_array($user, $polls[$_GET['poll']]['voted'])) {
                if ($poll['isMultiple'] === false) {
                    array_push($polls[$_GET['poll']]['answers'][$select], $user);
                }
                else {
                    foreach ($selects as $s) {
                        array_push($polls[$_GET['poll']]['answers'][$s], $user);
                    }
                }

                array_push($polls[$_GET['poll']]['voted'], $user);

                $success = 'Your vote has been submitted correctly';
            }
            else {
                
                foreach ($polls[$_GET['poll']]['answers'] as $i => $answer) {
                    if ($poll['isMultiple'] === false) {
                        if (in_array($user, $answer)) {
                            if ($i !== $select) {
                                unset($polls[$_GET['poll']]['answers'][$i][array_search($user, $polls[$_GET['poll']]['answers'][$i])]);
                            }
                        }
                        else {
                            if ($i === $select) {
                                array_push($polls[$_GET['poll']]['answers'][$i], $user);
                            }
                        }
                    }
                    else {
                        if (in_array($user, $answer)) {
                            if (!in_array($i, $selects)) {
                                unset($polls[$_GET['poll']]['answers'][$i][array_search($user, $polls[$_GET['poll']]['answers'][$i])]);
                            }
                        }
                        else {
                            if (in_array($i, $selects)) {
                                array_push($polls[$_GET['poll']]['answers'][$i], $user);
                            }
                        }
                    }
                }

                $success = 'Your vote has been changed correctly';
            }
            
            file_put_contents('../json/polls.json', json_encode($polls, JSON_PRETTY_PRINT));
            header("refresh:3; url=index.php"); 
            
        }

    }


    function getChosenOptions($polls, $selects) {
        global $user;
        $count = 0;
        foreach ($selects as $sel) {
            if (in_array($user, $polls[$_GET['poll']]['answers'][$sel])) {
                $count++;
            }
        }

        return ($count === count($selects)) ? true : false;
    }

    function getColor() {
        $r = rand(100, 200);
        $g = rand(100, 200);
        $b = rand(100, 200);
        $rgb = 'rgb(' . $r . ',' . $g . ',' . $b . ')';
        return $rgb;
    }
    function getPercentage($polls, $option) {
        return (count($polls[$_GET['poll']]['answers'][$option]) !== 0) ? round((count($polls[$_GET['poll']]['answers'][$option]) * 100)/(getTotalVotes($polls))) : 0;
    }

    function getTotalVotes($polls) {
        $sum = 0;
        foreach($polls[$_GET['poll']]['answers'] as $answer) {
            $sum += count($answer);
        }
        return $sum;
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
  <script src="../js/voting.js" defer></script>

  <title>Voting - User polls</title>
</head>

<body onload="loaded()">

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

  <div class="poll-container">
    <div class="poll-genre-selection">
      <div class="poll-genre"><?= $poll['genre'] ?></div>
      <?php if($poll['isMultiple'] === false): ?>
      <div class="poll-selection">single choice</div>
      <?php else: ?>
      <div class="poll-selection">multiple &nbsp choices</div>
      <?php endif; ?>
    </div>
    <div class="poll-question"><?= $poll['question'] ?></div>
    <div class="poll-createdAt"><?= date('j F Y', strtotime($poll['createdAt'])) ?></div>
    <div class="poll-deadline"><?= date('j F Y', strtotime($poll['deadline'])) ?></div>
    <div class="poll-options-total">
      <form class="polls-form" action="" method="post" novalidate>
        <div class="poll-options">
          <?php foreach($poll['options'] as $i => $option): ?>
          <div class="poll-option">
            <?php if($poll['isMultiple'] === false): ?>
            <input type="radio" name="select" value="<?= $option ?>"
              <?= (in_array($user, $polls[$_GET['poll']]['answers'][$option])) ? 'checked' : '' ?>>
            <?php else: ?>
            <input type="checkbox" name="selects[]" value="<?= $option ?>"
              <?= (in_array($user, $polls[$_GET['poll']]['answers'][$option])) ? 'checked' : '' ?>>
            <?php endif; ?>
            <div class="poll-option-percentage">
              <div class="option-name"><?= $option ?></div>
              <div class='percentage'><?= getPercentage($polls, $option) ?>%</div>
            </div>
            <div class="slider-percentage">
              <div class="slider-value"
                style='width: <?= getPercentage($polls, $option) ?>%; background-color: <?= getColor() ?>;'></div>
            </div>
            <div class="num-votes"><?= count($polls[$_GET['poll']]['answers'][$option]) ?> Votes</div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="poll-total">
          <div class="total">
            <p>votes</p>
            <?= getTotalVotes($polls) ?>
            <hr>
            <span class='error-message'><?= $error ?? '' ?></span>
            <!-- ! IF THE USER ALREADY VOTED AND WANTED TO EDIT THE VOTE, CHANGE THE TEXT TO EDIT VOTE AND DISPLAY THE OLDER VOTE ! -->
            <?php if(count(array_filter($polls[$_GET['poll']]['voted'], fn($vote) => $vote === $user)) === 0): ?>
            <button class="submit-button" type="submit" name='submit'>submit vote</button>
            <?php else: ?>
            <button class="submit-button" type="submit" name='submit'>edit vote</button>
            <?php endif; ?>

          </div>
        </div>
      </form>
    </div>
    <span><?= $success ?? '' ?></span>
    <a class="a" href="index.php">homepage</a>
  </div>

</body>

</html>