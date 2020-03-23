<b>Tutaj będą wstawiane treningi!</b><br>
<a href="{{PATH_PREFIX}}/workout/add">Dodaj trening!</a><br>

<div class="newsfeed">
	<?php foreach ($workouts as $workout): ?>
		<div class="feed-workout">
			<div class="feed-workout__author">
				<a href="{{PATH_PREFIX}}/profile/{{$workout['user_id']}}">
					{{$workout["user_name"]}}
				</a>
			</div>
			<div class="feed-workout__date">
				{{$workout["date"]}}
				-
				<a href="{{PATH_PREFIX}}/gym/{{$workout['gym_id']}}">
					{{$workout["gym_name"]}}
				</a>
			</div>
			<a href="{{PATH_PREFIX}}/workout/{{$workout['workout_id']}}">
				<div class="feed-workout__title">
					{{$workout["name"]}}
				</div>
			</a>
		</div>
	<?php endforeach ?>
</div>
