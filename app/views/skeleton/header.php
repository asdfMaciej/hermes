<?php
function auto_version($file)
{
    if(strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'] . $file))
        return $file;

    $mtime = filemtime($_SERVER['DOCUMENT_ROOT'] . $file);
    return $file."?v=".$mtime;
}
?><!DOCTYPE html>
<html lang="pl-PL">
	<head>	
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

        <?php if (DB_HOST != 'localhost'): ?>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-177328075-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-177328075-1');
        </script>
        <?php endif ?>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
		<link rel="apple-touch-icon" sizes="180x180" href="``PATH_PREFIX``/static/favicon/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="``PATH_PREFIX``/static/favicon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="``PATH_PREFIX``/static/favicon/favicon-16x16.png">
		<link rel="manifest" href="``PATH_PREFIX``/static/favicon/site.webmanifest">
		<?php foreach ($stylesheets as $style): ?>
		<link rel="stylesheet" type="text/css" href="``auto_version($style)``">
		<?php endforeach ?>
			
		<script>var PATH_PREFIX = "``PATH_PREFIX``"; <?php echo DEBUG ? "var DEBUG = true;" : "var DEBUG = false"; ?></script>

		<?php foreach ($scripts as $script): ?>
			<script src="``auto_version($script)``"></script>
		<?php endforeach ?>
		<title>``$title`` - Hermes</title>

	</head>
	<body>
		<div class='page-header' id="header">
			<a href="``PATH_PREFIX``/" class='page-header__logo'>
				<img src="``PATH_PREFIX``/static/img/logo.png">
			</a>
			<?php if ($account->isLoggedIn()): ?>
                <a href="#" class="page-header__search-button white">
                    <ion-icon name="search"></ion-icon>
                </a>
                <form action="``PATH_PREFIX``/search" method="get" class="page-header__filler">

                    <input id="user_search" name='q' type='text' placeholder='Znajdź użytkownika' value="``$_GET['q'] ?? ''``">
                </form>
				<a class='page-header__add white' href="``PATH_PREFIX``/workout/add">
                    <span>Dodaj trening</span>
                    <ion-icon name="add-outline"></ion-icon>
                </a>
				<a class='page-header__settings white' href="``PATH_PREFIX``/settings">
					<ion-icon name="settings-sharp"></ion-icon>
				</a>
				<a href="``PATH_PREFIX``/profile/``$account->user_id``">
					<img src="``PATH_PREFIX``/``$account->avatar``" class="avatar">
				</a><br>

				<form action="``PATH_PREFIX``/" method="post" id="logout-form">
					<input type="hidden" name="action" value="logout">
					<a href="#" class="white">
						<ion-icon name="log-out-outline"></ion-icon>
					</a>
				</form>
			<?php else: ?>
                <div class="page-header__filler"></div>
				<form action="``PATH_PREFIX``/" method="post" class="login-form">
					<input type="hidden" name="action" value="login">
					<input type="text" name="login" placeholder="Login">
					<input type="password" name="password" placeholder="Hasło">
					<input type="submit" value="Zaloguj się">
				</form>
			<?php endif ?>
		</div>

		<div class='page-content' id="content">
