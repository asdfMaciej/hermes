<div class="workout-details">
	<div class="workout-details__name">
		``$workout["name"]``
	</div>
	<div class="workout-details__date">
		``$workout["date"]``
	</div>
	<div class="workout-details__username">
		``$workout["user_name"]``
	</div>
	<div class="workout-details__photo">
		<img src="``PATH_PREFIX``/``$workout['avatar']``">
	</div>
</div>
<div class="workout-gym">
	<div class="workout-gym__name">
		<a href="``PATH_PREFIX``/gym/``$gym['gym_id']``">
			``$gym["name"]``
		</a>
	</div>
	<div class="workout-gym__map">
		Lokalizacja siłowni: ``$gym["lat"] . ", " . $gym["long"]``
	</div>
</div>
<div class="workout-exercises">
	<div class="workout-exercises__header">
		Wykonane ćwiczenia:
	</div>
	<div class="workout-exercises__list exercises-list">
		<?php foreach ($exercises as $exercise): ?>
			<div class="exercises-list__item exercise ``$exercise['failure'] ? 'failed' : ''``">
				<h3 class="exercises-list__name">
					``$exercise["exercise_type"]``
				</h3>
				<?php if ($exercise["show_duration"]): ?>
					<div>
						``$exercise["duration"]`` sekund
					</div>
				<?php endif ?>
				<?php if ($exercise["show_reps"]): ?>
					<div>
						``$exercise["reps"]`` powtórzeń
					</div>
				<?php endif ?>
				<?php if ($exercise["show_weight"]): ?>
					<div>
						``$exercise["weight"]`` kg
					</div>
				<?php endif ?>
			</div>
		<?php endforeach ?>
	</div>
</div>

<style>
	.workout-details__photo img {
		width: 100px;
	}
</style>
