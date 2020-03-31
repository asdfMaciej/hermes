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
		<div id="header">
			<a href="``PATH_PREFIX``/">Strona główna</a><br>
			<?php if ($account->isLoggedIn()): ?>
				Cześć,
				<a href="``PATH_PREFIX``/profile/``$account->user_id``">
					``$account->name``!
				</a><br>
				<?php $this->nest("forms/logout.php", []); ?>
			<?php else: ?>
				<a href="``PATH_PREFIX``/login">Zaloguj się</a>
				<a href="``PATH_PREFIX``/register">Rejestracja</a>
			<?php endif ?>
		</div>

		<div id="content">
