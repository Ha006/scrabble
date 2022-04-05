<?php

session_start();

$_SESSION['score'] = [0, 0];
$_SESSION['current'] = 1;
unset($_SESSION['first']);

$letters = json_decode(file_get_contents('letters.json'), 1)['letters'];

/* Creating array with tiles of all letters in the bag */
foreach ($letters as $letter => $val) {
	for ($i = 0; $i < $val['tiles']; $i++) {
		$bag[] = $letter;
	}
}

/* Shaking the bag with tiles */
shuffle($bag);

/* Players are given tiles that gets removed from the bag */
for ($user = 0; $user < 2; $user++) {
	for ($i = 0; $i < 7; $i++) {
		$users[$user][] = $bag[$user * 7 + $i];
		unset($bag[$user * 7 + $i]);
	}
}

/* Creating the board */
for ($row = 0; $row < 15; $row++) {
	for ($col = 0; $col < 15; $col++) {
		$board[$row][$col]++;
	}
}

$_SESSION['board'] = $board;
$_SESSION['users'] = $users;
$_SESSION['bag'] = $bag;
