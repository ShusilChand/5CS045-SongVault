<?php require __DIR__.'/includes/bootstrap.php';redirect(empty($_SESSION['uid'])?'login.php':'list-songs.php');
