<h1>Ustawienia konta:</h1>

<h3>Zmień avatar:</h3>
<form enctype="multipart/form-data" action="``PATH_PREFIX``/upload/avatar" method="POST">
	<input type="hidden" name="action" value="upload">
	<input type="file" name="image"><br>
	<input type="submit">
</form>

<br>

<h3>Zaktualizuj informacje:</h3>
<form enctype="multipart/form-data" action="``PATH_PREFIX``/settings" method="POST">
	<input type="hidden" name="action" value="change">
	<label>
		Nazwa użytkownika:
		<input type="text" name="name" value="``$user['name']``">
	</label><br>
	<input type="submit" value="Zmień">
</form>