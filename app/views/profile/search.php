<h2>Wyniki wyszukiwania dla "``$query``":</h2>
<div class="profile-list">
	<?php foreach ($users as $user): ?>
		<a href="``PATH_PREFIX``/profile/``$user['user_id']``">
			<div class="profile-list-item">
				<div class="profile-list-item__avatar">
					<img src="``PATH_PREFIX``/``$user['avatar']``">
				</div>
				<h4>
					``$user["name"]``
				</h4>
			</div>
		</a>	
	<?php endforeach ?>
</div>

<?php if (!$users): ?>
	<h4> Nie znaleziono żadnych użytkowników. </h4>
<?php endif ?>