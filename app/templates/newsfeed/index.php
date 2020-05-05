<div class="frontpage">
	<div class='frontpage__statistics statistics-box'>
		<a href="``PATH_PREFIX``/profile/``$account->user_id``" class='statistics-box__avatar'>
			<img src="``PATH_PREFIX``/``$account->avatar``">
				<div class="statistics-box__name">
				``$account->name``
			</div>
		</a>
		<div class="statistics-box__statistics">
			<div>
				<h4>Na siłowni byłeś:</h4>
				``$statistics["workout_count"]`` razy
				<h4>Ostatni trening zrobiłeś:</h4>
				``$statistics["workout_last_date"]``
			</div>
		</div>
		<a class='statistics-box__add' href="``PATH_PREFIX``/workout/add">Dodaj trening!</a>
	</div>
	<div class="frontpage__newsfeed">
		<?php $this->nest("newsfeed/newsfeed.php", ["workouts" => $workouts]); ?>
	</div>
</div>