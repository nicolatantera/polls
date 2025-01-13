<?php
    session_start();

    $polls = json_decode(file_get_contents('../json/polls.json'), true);
    unset($polls[$_GET['poll']]);
    file_put_contents('../json/polls.json', json_encode($polls, JSON_PRETTY_PRINT));

    header('location: index.php');
    exit();
?>