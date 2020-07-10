<html>
	<head>	
		<meta charset="UTF-8">
		<link rel="apple-touch-icon" sizes="180x180" href="``PATH_PREFIX``/static/favicon/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="``PATH_PREFIX``/static/favicon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="``PATH_PREFIX``/static/favicon/favicon-16x16.png">
		<link rel="manifest" href="``PATH_PREFIX``/static/favicon/site.webmanifest">
		<?php foreach ($stylesheets as $style): ?>
		<link rel="stylesheet" type="text/css" href="``$style``">
		<?php endforeach ?>
			
		<script>var PATH_PREFIX = "``PATH_PREFIX``"; <?php echo DEBUG ? "var DEBUG = true;" : "var DEBUG = false"; ?></script>

		<?php foreach ($scripts as $script): ?>
			<script src="``$script``"></script>
		<?php endforeach ?>
		<title>``$title`` - Hermes</title>

	</head>
	<body>
		<div class='page-header' id="header">
			<a href="``PATH_PREFIX``/" class='page-header__logo'>
				<img src="``PATH_PREFIX``/static/img/logo.png">
			</a><br>
			<form action="``PATH_PREFIX``/search" method="get" class="page-header__filler">
				 <input id="user_search" name='q' type='text' placeholder='Znajdź użytkownika' value="``$_GET['q'] ?? ''``">
			</form>
			
			<?php if ($account->isLoggedIn()): ?>
				<button class='page-header__add' onclick="document.location = '``PATH_PREFIX``/workout/add'">Dodaj trening</button>
				<button class='page-header__settings' onclick="document.location = '``PATH_PREFIX``/settings'">Ustawienia</button>
				<a href="``PATH_PREFIX``/profile/``$account->user_id``">
					<img src="``PATH_PREFIX``/``$account->avatar``">
				</a><br>

				<form action="``PATH_PREFIX``/" method="post">
					<input type="hidden" name="action" value="logout">
					<input type="submit" value="Wyloguj się">
				</form>
			<?php else: ?>
				<form action="``PATH_PREFIX``/" method="post">
					<input type="hidden" name="action" value="login">
					<input type="text" name="login" placeholder="Login">
					<input type="password" name="password" placeholder="Hasło">
					<input type="submit" value="Zaloguj się">
				</form>
			<?php endif ?>
		</div>

		<div class='page-content' id="content">
