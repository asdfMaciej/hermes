Profil:
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