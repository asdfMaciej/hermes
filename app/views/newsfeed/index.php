<div class="frontpage">
	<div class='frontpage__statistics statistics-box'>
		<a href="``PATH_PREFIX``/profile/``$account->user_id``" class='statistics-box__avatar'>
			<img src="``PATH_PREFIX``/``$account->avatar``" class="avatar">
				<div class="statistics-box__name">
				    Cześć, ``$account->first_name``!
			    </div>
		    </a>

		<a class='statistics-box__add' href="``PATH_PREFIX``/workout/add">Dodaj trening!</a>

        <?php if (strpos($account->avatar, 'default') !== false): ?>
            <a class='statistics-box__add' href="``PATH_PREFIX``/settings">Zmień avatar</a>
        <?php endif ?>
		<div class="statistics-box__statistics">
			<h4>Na siłowni byłeś:</h4>
			<span>
				``$statistics["workout_count"]`` razy
			</span>
			
			<h4>Ostatni trening zrobiłeś:</h4>
			<span class='date'>
				``$statistics["workout_last_date"]``
			</span>
		</div>
		
	</div>
	<div class="frontpage__newsfeed">
		<?php $this->nest("newsfeed/newsfeed.php", []); ?>
	</div>
</div>