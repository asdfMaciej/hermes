<h2>Profil:</h2>
<div class="profile-details">
	<div class="profile-details__name">
		{{$user["login"]}}
	</div>
	<div class="profile-details__login">
		{{$user["name"]}}
	</div>
	<div class="profile-details__register-date">
		{{$user["register_date"]}}
	</div>
	<div class="profile-details__avatar">
		<img src="{{PATH_PREFIX}}/{{$user['avatar']}}">
	</div>
</div>

<?php if ($account->user_id == $user["user_id"]): ?>
<h3>Zmień avatar:</h3>
<form enctype="multipart/form-data" action="<?php echo PATH_PREFIX; ?>/upload/avatar" method="POST">
	<input type="hidden" name="action" value="upload">
	<input type="file" name="image">
	<input type="submit">
</form>
<?php endif ?>