<?php
    session_start();
    $polls = json_decode(file_get_contents('../json/polls.json'), true);
    uasort($polls, fn($a, $b) => $b['createdAt'] <=> $a['createdAt']); 
    
    if (isset($_SESSION['user-id'])) {
        $users = json_decode(file_get_contents('../json/users.json'), true);
        $user = $users[$_SESSION['user-id']]['username'];
    }

    $groups = json_decode(file_get_contents('../json/groups.json'), true);



  function getPercentage($polls, $option, $i) {
    $totalVotes = getTotalVotes($polls, $i);
    if ($totalVotes === 0) {
        return 0; // Return 0% if there are no votes
    }
    return round((count($polls[$i]['answers'][$option]) * 100) / $totalVotes);
  }

    function getTotalVotes($polls, $i) {
        $sum = 0;
        foreach($polls[$i]['answers'] as $answer) {
            $sum += count($answer);
        }
        return $sum;
    }

    function inAnyGroup($user, $groups) {
        foreach ($groups as $group) {
            if (in_array($user, $group['members'])) {
                return true;
            }
        }
        return false;
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

  <title>Main - User polls</title>
</head>

<body>
  <div class="image-container">
    <img class="img" src="../img/dark-background.jpg" alt="">
  </div>


  <div class="header">
    <div class="header-title">
      <img src="../img/poll_logo_white.png" alt="logo">
    </div>
    <?php if(!isset($_SESSION['user-id'])): ?>
    <div class="header-auth">
      <a href='login.php'>sign in</a>
      &nbsp|&nbsp
      <a href='register.php'>sign up</a>
    </div>
    <?php else: ?>
    <div class="header-auth">
      <a href='logout.php'><?= $user ?></a>
    </div>
    <?php endif; ?>
  </div>

  <div class="description">
    Free your vote about any topic you are interested in <br>or just stick around to see what's on other people mind.
    <br>
    <?php if(!isset($user)): ?>
    <a href='login.php'>Sign in</a> / <a href='register.php'>Sign up</a> for a better experience, <br>letting you vote
    at any poll you are interested in.
    <?php endif; ?>
  </div>

  <?php if(isset($user) && $user === 'admin'): ?>
  <div class="admin-new">
    <div class="admin-new-poll">
      <a href='admin_create.php'>new poll</a>
    </div>
    <div class="admin-new-group">
      <a href='admin_groups.php'>new group</a>
    </div>
  </div>
  <?php endif; ?>

  <div class="polls-container">
    <?php if(isset($user) && $user !== 'admin'): ?>
    <details>
      <summary>My groups</summary>
      <?php if(!inAnyGroup($user, $groups)): ?>
      <p>You are not in any group</p>
      <?php else: ?>
      <?php foreach($groups as $i => $group): ?>
      <?php if(in_array($user, $group['members'])): ?>
      <div class="polls-title">
        <?= $group['group'] ?>
      </div>
      <?php foreach($group['polls'] as $poll): ?>
      <div class="poll">
        <div class="poll-id"><?= $polls[$poll]['id'] ?></div>
        <div class="poll-genre"><?= $polls[$poll]['genre'] ?></div>
        <div class="poll-question"><?= $polls[$poll]['question'] ?></div>
        <div class="poll-times">
          <div class="poll-createdAt"><?= date('j F Y', strtotime($polls[$poll]['createdAt'])) ?></div>
          <div class="poll-deadline"><?= date('j F Y', strtotime($polls[$poll]['deadline'])) ?></div>
          <?php if(in_array($user, $polls[$poll]['voted'])): ?>
          <a href='voting.php?poll=<?= $polls[$poll]['id'] ?>'>edit vote</a>
          <?php else: ?>
          <a href='voting.php?poll=<?= $polls[$poll]['id'] ?>'>vote</a>
          <?php endif; ?>
        </div>
        <div class="poll-votes"><?= count($polls[$poll]['voted']) ?> votes</div>
      </div>
      <?php endforeach; ?>
      <?php endif;?>
      <?php endforeach; ?>
      <?php endif; ?>
    </details>
    <?php endif; ?>

    <div class="polls-title">
      Polls
    </div>

    <?php foreach($polls as $i => $poll): ?>
    <?php if(isset($user) && $user === 'admin'): ?>
    <div class="poll">
      <div class="poll-id"><?= $poll['id'] ?></div>
      <div class="poll-genre"><?= $poll['genre'] ?></div>
      <div class="poll-question"><?= $poll['question'] ?></div>
      <div class="poll-times">
        <div class="poll-createdAt"><?= date('j F Y', strtotime($poll['createdAt'])) ?></div>
        <div class="poll-deadline"><?= date('j F Y', strtotime($poll['deadline'])) ?></div>
      </div>
      <div class="poll-votes"><?= count($poll['voted']) ?> votes</div>
      <a class="poll-admin-edit" href='admin_edit.php?poll=<?= $poll['id'] ?>'>edit</a>
      <a class="poll-admin-delete" href='admin_delete.php?poll=<?= $poll['id'] ?>'
        onclick="return  confirm('Are you sure you want to delete this poll?')">delete</a>
    </div>

    <?php elseif(date('Y-m-d') < $poll['deadline'] && $poll['group'] === ''): ?>
    <div class="poll">
      <div class="poll-id"><?= $poll['id'] ?></div>
      <div class="poll-genre"><?= $poll['genre'] ?></div>
      <div class="poll-question"><?= $poll['question'] ?></div>
      <div class="poll-times">
        <div class="poll-createdAt"><?= date('j F Y', strtotime($poll['createdAt'])) ?></div>
        <div class="poll-deadline"><?= date('j F Y', strtotime($poll['deadline'])) ?></div>
        <?php if(isset($_SESSION['user-id']) && in_array($user, $poll['voted']) && $user !== 'admin'): ?>
        <a href='voting.php?poll=<?= $poll['id'] ?>'>edit vote</a>
        <?php elseif(isset($_SESSION['user-id']) && $user !== 'admin'): ?>
        <a href='voting.php?poll=<?= $poll['id'] ?>'>vote</a>
        <?php elseif(!isset($_SESSION['user-id']) || $user !== 'admin'): ?>
        <a href='login.php?poll=<?= $poll['id'] ?>'>vote</a>
        <?php endif; ?>
      </div>
      <div class="poll-votes"><?= count($poll['voted']) ?> votes</div>
    </div>
    <?php endif;?>
    <?php endforeach; ?>

    <?php if(count(array_filter($polls, fn($poll) => date('Y-m-d') > $poll['deadline'])) <= 0): ?>
    <div class="polls-title">
      No expired polls
    </div>
    <?php else: ?>
    <div class="polls-title">
      Expired polls
    </div>
    <?php endif; ?>

    <?php foreach($polls as $i => $poll): ?>
    <?php if(date('Y-m-d') > $poll['deadline']):?>
    <div class="poll">
      <div class="poll-id"><?= $poll['id'] ?></div>
      <div class="poll-genre"><?= $poll['genre'] ?></div>
      <div class="poll-question"><?= $poll['question'] ?></div>
      <div class="poll-times">
        <div class="poll-deadline">expired on <?= date('j F Y', strtotime($poll['deadline'])) ?></div>
      </div>
      <div class="poll-votes expired"><?= count($poll['voted']) ?> votes</div>
      <hr>
      <div class="results-container">
        <?php foreach($poll['answers'] as $option => $answer): ?>
        <div class="answer-container"
          style="background: <?= (count($answer) == count(max($poll['answers']))) ? 'rgba(39, 179, 24, 0.3)' : ''  ?>;">
          <p>votes</p>
          <p class="vote"><?= count($answer) ?></p>
          <hr>
          <div class="answer-name"><?= $option ?></div>
          <div class="percentage"><?= getPercentage($polls, $option, $i) ?>%</div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif;?>
    <?php endforeach; ?>

  </div>

</body>

</html>