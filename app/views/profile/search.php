<h2>Wyniki wyszukiwania:</h2>
<div class="profile-list">
	<?php foreach ($users as $user): ?>
		<a href="``PATH_PREFIX``/profile/``$user['user_id']``">
			<div class="profile-list-item">
                <img class="profile-list-item__avatar avatar" src="``PATH_PREFIX``/``$user['avatar']``">
				<div class="profile-list-item__user">
                    <h4>``$user["name"]``</h4>
                    <?php if ($user['following']): ?>
                        <div style="margin-top: -4px; margin-bottom: 4px">• Obserwujesz go</div>
                    <?php endif ?>
                    <div style="font-size: 0.9em">Dołączył <span class="date">``$user['register_date']``</span></div>

                </div>
			</div>
		</a>	
	<?php endforeach ?>
</div>

<?php if (!$users): ?>
	<h4> Nie znaleziono żadnych użytkowników. </h4>
<?php endif ?>