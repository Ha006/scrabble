<?php

header('Content-Type: application/json');

session_start();

$arr['score'] = $_SESSION['score'];
$arr['users'][0] = array_values($_SESSION['users'][0]);
$arr['users'][1] = array_values($_SESSION['users'][1]);
$arr['current'] = $_SESSION['current'];
$arr['tiles'] = sizeof($_SESSION['bag']);
$arr['board'] = $_SESSION['board'];

echo json_encode($arr, JSON_PRETTY_PRINT);
