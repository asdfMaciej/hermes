<div class="workout-details">
	<div class="workout-details__name">
		{{$workout["name"]}}
	</div>
	<div class="workout-details__date">
		{{$workout["date"]}}
	</div>
	<div class="workout-details__username">
		{{$workout["user_name"]}}
	</div>
</div>
<div class="workout-gym">
	<div class="workout-gym__name">
		{{$gym["name"]}}
	</div>
	<div class="workout-gym__map">
		Lokalizacja siłowni: {{$gym["lat"] . ", " . $gym["long"]}}
	</div>
</div>
<div class="workout-exercises">
	<div class="workout-exercises__header">
		Wykonane ćwiczenia:
	</div>
	<div class="workout-exercises__list exercises-list">
		<?php foreach ($exercises as $exercise): ?>
			<div class="exercises-list__item {{$exercise['failure'] ? 'failed' : ''}}">
				<span class="exercises-list__name">
					{{$exercise["exercise_type"]}}
				</span>
				<?php if ($exercise["show_duration"]): ?>
					<div class="exercises-list__duration">
						{{$exercise["duration"]}} sekund
					</div>
				<?php endif ?>
				<?php if ($exercise["show_reps"]): ?>
					<div class="exercises-list__reps">
						{{$exercise["reps"]}} powtórzeń
					</div>
				<?php endif ?>
				<?php if ($exercise["show_weight"]): ?>
					<div class="exercises-list__weight">
						{{$exercise["weight"]}} kg
					</div>
				<?php endif ?>
			</div>
		<?php endforeach ?>
	</div>
</div>