<?php

header('Content-Type: application/json');

if (empty($_REQUEST['uid'])) {
	$errors[] = 'User ID id missing';
}
if (empty($_REQUEST['word'])) {
	$errors[] = 'Word is missing';
}
if (empty($_REQUEST['orientation'])) {
	$errors[] = 'Orientation is missing';
}
if (is_int($_REQUEST['x'])) {
	$errors[] = 'X coordinate is missing';
}
if (is_int($_REQUEST['y'])) {
	$errors[] = 'Y coordinate is missing';
}

if (empty($errors)) {
	session_start();
	
	/* Variable used for incrementation, no matter of orientation */
	$o = substr($_REQUEST['orientation'], 0, 1);
	$letters = str_split($_REQUEST['word']);
	$minLength = 2;
	
	if (sizeof($_SESSION['users'][$_REQUEST['uid']-1]) < $minLength) {
		if ($_SESSION['score'][0] > $_SESSION['score'][1]) {
			$winner = 'Player 1';
		} else if ($_SESSION['score'][0] < $_SESSION['score'][1]) {
			$winner = 'Player 2';
		} else {
			$winner = 'Both players';
		}
		$errors[] = 'We have a winner! 🥳 ' . $winner . ' wins';
	}
	if (strlen($_REQUEST['word']) < $minLength) {
		$errors[] = 'Too short word';
	}
	
	if (!empty($_SESSION['first'])) {
		foreach ($letters as $letter) {
			/* Only possible to place tile on bord where value is equal to one or the same as the value of the tile */
			if ($_SESSION['board'][$_REQUEST['y'] + $v][$_REQUEST['x'] + $h] == $letter) {
				$sameLetter[] = $letter;
			} else {
				if ($_SESSION['board'][$_REQUEST['y'] + $v][$_REQUEST['x'] + $h] != 1) {
					$letterChange[$letter] = $_SESSION['board'][$_REQUEST['y'] + $v][$_REQUEST['x'] + $h];
				}
			}
			$$o++;
		}
	
		/* Generating no error first time placing a word at the board */
		if (sizeof($letterChange) != 0) {
			$errors[] = 'Tiles on the board cannot be replaced';
		}
		
		if (sizeof($sameLetter) == 0) {
			$errors[] = 'Place word in connection to other words';
		}
	}
	
	if (
		($o == 'v' && $_REQUEST['y'] + strlen($_REQUEST['word']) > sizeof($_SESSION['board']) || $_REQUEST['x'] > sizeof($_SESSION['board'])) || 
		($o == 'h' && $_REQUEST['x'] + strlen($_REQUEST['word']) > sizeof($_SESSION['board']) || $_REQUEST['y'] > sizeof($_SESSION['board']))
	) {
		$errors[] = 'Cannot place word outside board';
	}

	/* If still no errors word is considered valid */
	if (empty($errors)) {
		unset($$o);
		foreach ($letters as $letter) {
			/* Updating the board */
			$_SESSION['board'][$_REQUEST['y'] + $v][$_REQUEST['x'] + $h] = $letter;
			
			/* Removing the tile from the users hand */
			unset($_SESSION['users'][$_REQUEST['uid']-1][array_search($letter, $_SESSION['users'][$_REQUEST['uid']-1])]);
			
			/* If bag with tiles is not empty, adding tile to the users hand */
			$replacement = array_shift($_SESSION['bag']);
			if ($replacement) {
				$_SESSION['users'][$_REQUEST['uid']-1][] = $replacement;
			}
			$$o++;
		}
		
		/* Indicating wether the first word was placed atvthe board */
		if (empty($_SESSION['first'])) {
			$_SESSION['first'] = 'true';
		}
		
		/* Incrementing the current users score */
		$_SESSION['score'][$_REQUEST['uid']-1] = $_SESSION['score'][$_REQUEST['uid']-1] + strlen($_REQUEST['word']);
		
		/* Changing current user */
		$_SESSION['current'] = $_REQUEST['uid'] == 1 ? 2 : 1;
	}
	if (!empty($errors)) {
		http_response_code(403);
	}
} else {
	http_response_code(400);
}

$arr['errors'] = $errors;

echo json_encode($arr, JSON_PRETTY_PRINT);
