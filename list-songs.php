<?php require __DIR__.'/includes/bootstrap.php';logged();render('songs.twig',['songs'=>search_songs([]),'heading'=>'Music library']);
