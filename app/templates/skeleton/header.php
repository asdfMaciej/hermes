<html>
	<head>
		<title>{{$title}}</title>
		<meta charset="UTF-8"/>
		<?php foreach ($stylesheets as $style): ?>
		<link rel="stylesheet" type="text/css" href="{{$style}}">
		<?php endforeach ?>

		<?php foreach ($scripts as $script): ?>
			<script src="{{$script}}"></script>
		<?php endforeach ?>
	</head>
	<body>
		<div id="header">
			<?php if ($account->isLoggedIn()): ?>
				Cześć, {{$account->name}}!<br>
				<?php $this->nest("forms/logout.php", []); ?>
			<?php else: ?>
				<a href="{{PATH_PREFIX}}/login">Zaloguj się</a>
				<a href="{{PATH_PREFIX}}/register">Rejestracja</a>
			<?php endif ?>
		</div>

		<div id="content">
