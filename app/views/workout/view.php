<div class="workout">
	<div class="workout-details">
		<div class="workout-details__photo">
			<a href="``PATH_PREFIX``/profile/``$workout['user_id']``">
				<img src="``PATH_PREFIX``/``$workout['avatar']``">
			</a>
		</div>

		<div class="workout-details__row">
			<h2 class="workout-details__title">
				``$workout["title"]``
			</h2>
			<a class="workout-details__username" href="``PATH_PREFIX``/profile/``$workout['user_id']``">
				``$workout["user_name"]``
			</a>
			<div class="workout-details__date">
				``$workout["date"]``
			</div>
			
		</div>
	</div>

	<div class="workout-gym">	
		<a href="``PATH_PREFIX``/gym/``$gym['gym_id']``" class="workout-gym__picture">
			<img src="``PATH_PREFIX``/``$gym_album[0]['path']``" alt="">
		</a>
		<div class="workout-gym__name">
			<a href="``PATH_PREFIX``/gym/``$gym['gym_id']``">
				``$gym["name"]``
			</a>
		</div>
	</div>
	<div class="workout-exercises">
		<h3 class="workout-exercises__header">
			Przebieg treningu:
		</h3>
		<div class="workout-exercises__list exercises-list">
			<?php foreach ($exercises as $n => $exercise): ?>
				<?php
				$display_title = $n == 0 ? true : $exercises[$n - 1]["type_id"] != $exercise["type_id"];
				if ($display_title):
				?>
					<h4 class="exercises-list__name">
						``$exercise["exercise_type"]``
					</h4>
				<?php endif ?>

				<div class="exercises-list__item ``$exercise['failure'] ? 'failed' : ''``">
					<?php if ($exercise["show_duration"]): ?>
						<span>
							``$exercise["duration"]`` sekund
						</span>
					<?php endif ?>
					<?php if ($exercise["show_reps"]): ?>
						<span>
							``$exercise["reps"]`` powtórzeń
						</span>
					<?php endif ?>
					<?php if ($exercise["show_weight"]): ?>
						<span>
							po ``$exercise["weight"]`` kg
						</span>
					<?php endif ?>
					<?php if ($exercise["failure"]): ?>
						<span>
							- nieudane
						</span>
					<?php endif ?>
				</div>
			<?php endforeach ?>
		</div>
	</div>
</div>
