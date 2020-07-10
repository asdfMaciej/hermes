<div class="profile-details">
	<div class="profile-details__avatar">
		<img src="``PATH_PREFIX``/``$user['avatar']``">
	</div>
	<div class="profile-details__container">
		<h2 class="profile-details__name">
			``$user["name"]``
		</h2>
		<h4 class="profile-details__register-date">
			Założył konto: <span class="date">``$user["register_date"]``</span>
		</h4>
		
		<?php if ($user["user_id"] != $account["user_id"]): ?>
			<div class="profile-details__follow">
				<?php if ($user["following"]): ?>
					<a href="``PATH_PREFIX``/profile/``$user['user_id']``/unfollow">Odobserwuj</a>
				<?php else: ?>
					<a href="``PATH_PREFIX``/profile/``$user['user_id']``/follow" class="unfollowed">Zaobserwuj</a>
				<?php endif ?>
			</div>
		<?php endif ?>
	</div>
</div>
