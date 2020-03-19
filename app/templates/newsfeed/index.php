<b>Tutaj będą wstawiane treningi!</b><br>
<a href="{{PATH_PREFIX}}/workout/add">Dodaj trening!</a><br>
<?php var_dump($workouts); ?>

<div class="newsfeed">
	<?php foreach ($workouts as $workout): ?>
		<div class="feed-workout">
			<div class="feed-workout__author">
				{{$workout["user_name"]}}
			</div>
			<div class="feed-workout__date">
				{{$workout["date"]}} - {{$workout["gym_name"]}}
			</div>
			<a href="{{PATH_PREFIX}}/workout/{{$workout['workout_id']}}">
				<div class="feed-workout__title">
					{{$workout["name"]}}
				</div>
			</a>
		</div>
	<?php endforeach ?>
</div>
