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
		<a href="{{PATH_PREFIX}}/gym/{{$gym['gym_id']}}">
			{{$gym["name"]}}
		</a>
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
					<span class="exercises-list__duration">
						{{$exercise["duration"]}} sekund
					</span>
				<?php endif ?>
				<?php if ($exercise["show_reps"]): ?>
					<span class="exercises-list__reps">
						{{$exercise["reps"]}} powtórzeń
					</span>
				<?php endif ?>
				<?php if ($exercise["show_weight"]): ?>
					<span class="exercises-list__weight">
						{{$exercise["weight"]}} kg
					</span>
				<?php endif ?>
			</div>
		<?php endforeach ?>
	</div>
</div>