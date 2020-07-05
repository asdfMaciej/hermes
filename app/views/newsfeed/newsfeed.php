<div class="newsfeed">
	<?php foreach ($workouts as $workout): ?>
		<div class="feed-workout">
			<div class="feed-workout__avatar">
				<img src="``PATH_PREFIX``/``$workout['avatar']``">
			</div>
			<div class="feed-workout__container">
				<div class="feed-workout__author">
					<a href="``PATH_PREFIX``/profile/``$workout['user_id']``">
						``$workout["user_name"]``
					</a>
				</div>
				<div class="feed-workout__date">
					<span class="date">``$workout["date"]``</span>
					-
					<a href="``PATH_PREFIX``/gym/``$workout['gym_id']``">
						``$workout["gym_name"]``
					</a>
				</div>
				<a href="``PATH_PREFIX``/workout/``$workout['workout_id']``">
					<div class="feed-workout__title">
						``$workout["title"]``
					</div>
				</a>
				<div class="feed-workout__reactions">
					<a href="``PATH_PREFIX``/workout/``$workout['workout_id']``/``$workout['reacted'] ? 'un' : ''``react" class='reaction-button ``$workout["reacted"] ? "liked" : ""``'>
					💪
						<span class="reaction-count">``$workout["reactions"] ?? 0``</span>
					</a>

					<span>``$workout["comments"]`` ``($workout["comments"] == 0 || $workout["comments"] >= 5) ? 'komentarzy' : ($workout["comments"] == 1 ? 'komentarz' : 'komentarze')``</span>
				</div>
			</div>
		</div>
		<?php if (!is_null($workout['comment'])): ?>
		<div class="comment comment--newsfeed">
			<a href="``PATH_PREFIX``/profile/``$workout['comment_user_id']``">
				<img class="comment__avatar" src="``PATH_PREFIX``/``$workout['comment_avatar']``">
			</a>
			<div class="comment__main">
				<a href="``PATH_PREFIX``/profile/``$workout['comment_user_id']``" class="comment__name">
					``$workout['comment_user_name']``
				</a>
				<span class="comment__created date">
					``$workout['comment_created']``
				</span>
				<div class="comment__content">
					``$workout['comment']``
				</div>
				
			</div>
		</div>
		<?php endif ?>
	<?php endforeach ?>
</div>
