<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<meta name="apple-mobile-web-app-capable" content="yes">
		
		<title>Scrabble</title>
		
		<style>
			@font-face {
				font-family: "ocr a extended";
				src: url("//db.onlinewebfonts.com/t/fd6fa80f1e3345834599de891cca3f4c.eot");
				src: url("//db.onlinewebfonts.com/t/fd6fa80f1e3345834599de891cca3f4c.eot?#iefix") format("embedded-opentype"), url("//db.onlinewebfonts.com/t/fd6fa80f1e3345834599de891cca3f4c.woff2") format("woff2"), url("//db.onlinewebfonts.com/t/fd6fa80f1e3345834599de891cca3f4c.woff") format("woff"), url("//db.onlinewebfonts.com/t/fd6fa80f1e3345834599de891cca3f4c.ttf") format("truetype"), url("//db.onlinewebfonts.com/t/fd6fa80f1e3345834599de891cca3f4c.svg#OCR A Extended") format("svg");
			}
			* {
				font-family: "ocr a extended";
				font-size: 24px;
			}
			body {
				background: #222;
				color: #fff;
				text-align: center;
			}
			h1 {
				font-size: 3em;
			}
			table {
				background: rgba(238, 232, 170);
				border: 1px solid #000;
				border-collapse: collapse;
				margin: 0 auto;
			}
			table#board {
				pointer-events: none;
			}
			td {
				border: 1px solid #000;
				padding: 0;
			}
			table input {
				-webkit-appearance: none;
				background: transparent;
				border: 0;
				border-radius: 0;
				font-size: 25px;
				height: 30px;
				padding: 0;
				margin: 0;
				text-align: center;
				width: 30px;
			}
			table:last-of-type input {
				font-size: 50px;
				height: 60px;
				width: 60px;
			}
			tr:nth-child(2n),
			td:nth-child(2n) {
				background: rgba(255, 255, 255, 0.5);
			}
			table:last-of-type td,
			td.tile {
				background: #fff;
				color: #222;
			}
		</style>
	</head>
	<body>
		<h1>Scrabble</h1>
		
		<p><strong>Player 1: </strong> <span id="p1"></span> • <strong>Player 2: </strong> <span id="p2"></span></p>
		
		<table id="board">
		<?php
		for ($row = 0; $row < 15; $row++) {
			echo '<tr>';
			for ($col = 0; $col < 15; $col++) {
				echo '<td><input maxlength="1"></td>';
			}
			echo '</tr>';
		}
		?>
		</table>
		
		<p><small><strong>Current user: </strong>Player <span id="current">0</span> (<span id="tiles"></span> tiles)</small></p>
		
		<table><tr>
		<?php
		for ($col = 0; $col < 7; $col++) {
			echo '<td><input readonly></td>';
		}
		?>
		</tr></table>
		
		<input type="hidden" name="user" value="1">
		
		<p>
			<input name="word" readonly style="width: 53%">
		</p>
		
		<p>
		<select name="orientation">
			<option value="horizontal">Horizontal</option>
			<option value="vertical">Vertical</option>
		</select>
		
		<label for="x">X</label>
		<select name="x" id="x">
		<?php
		for ($i = 0; $i < 15; $i++) {
			echo '<option value="' . $i . '"' . ($i == 2 ? ' selected' : '') .  '>' . ($i+1) . '</option>';
		}
		?>
		</select>
		
		<label for="y">Y</label>
		<select name="y" id="y">
		<?php
		for ($i = 0; $i < 15; $i++) {
			echo '<option value="' . $i . '"' . ($i == 7 ? ' selected' : '') .  '>' . ($i+1) . '</option>';
		}
		?>
		</select>
		</p>
		
		<script>
			function updateUser(uid) {
				document.querySelector('#current').innerHTML = uid;
			 	document.querySelector('input[type="hidden"]').value = uid;
			}
			
			function updateBoard(board) {
				board.forEach(function(row, i) {
					board[i].forEach(function(cel, j) {
						el = [...document.querySelectorAll('table input')][i*15+j];
						if (cel != 1) {
							el.value = cel;
							el.parentNode.classList.add('tile');
						} else {
							el.value = '';
							el.parentNode.classList.remove('tile');
						}
					});
				});
			}
			
			function updateStatus() {
				xhr = new XMLHttpRequest();
				xhr.onloadend = function() {
					json = JSON.parse(xhr.response);
					updateUser(json.current);
					updateBoard(json.board);
					
					document.querySelector('[name=word]').value = '';
					document.querySelector('#tiles').innerHTML = json.tiles;
					
					document.querySelector('#p1').innerHTML = json.score[0];
					document.querySelector('#p2').innerHTML = json.score[1];
					
					json.users[json.current-1].forEach(function(row, i) {
						el = [...document.querySelectorAll('table:last-of-type input')][i];
						el.value = row;
					});
					
					document.querySelectorAll('table:last-of-type input').forEach(function(el) {
						el.removeAttribute('disabled');
					});
				}
				xhr.open('GET', 'status.php');
				xhr.send();
			}
			
			function nextPlayer() {
				formData = new FormData();
				formData.append('uid', document.querySelector('input[type="hidden"]').value);
				formData.append('word', document.querySelector('[name=word]').value.toLowerCase());
				formData.append('orientation', document.querySelector('[name=orientation]').value);
				formData.append('x', document.querySelector('[name=x]').value);
				formData.append('y', document.querySelector('[name=y]').value);
				
				xhr = new XMLHttpRequest();
				xhr.onloadend = function() {
					json = JSON.parse(xhr.response);
					if (json.errors) {
						alert(json.errors[0]);
					} else {
						updateStatus();
					}
				}
				xhr.open('POST', 'play.php');
			 	xhr.send(formData);
			}
			
			function startGame() {
				xhr = new XMLHttpRequest();
				xhr.onloadend = function() {
					updateUser(1);
					updateStatus();
				}
				xhr.open('DELETE', 'start.php');
				xhr.send();
			}
			
			window.onload = function() {
				updateStatus();
				document.querySelectorAll('table:last-of-type input').forEach(function(el) {
					el.addEventListener('click', function() {
						word = document.querySelector('[name=word]');
						word.value = word.value + this.value;
						this.setAttribute('disabled', 'true');
					});
				});
			}
		</script>
		
		<button onclick="nextPlayer()">Put</button>
		<button onclick="confirm('Are you sure? 😬') ? startGame() : ''">ReStart</button>
	</body>
</html>
