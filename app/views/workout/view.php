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
			<div class="workout-details__reactions">
				<a href="``PATH_PREFIX``/workout/``$workout['workout_id']``/``$reactions['reacted'] ? 'un' : ''``react" class='reaction-button ``$reactions["reacted"] ? "liked" : ""``'>
					💪
					<span class="reaction-count">``$reactions["count"] ?? 0``</span>
				</a>				
			</div>
		</div>
	</div>

	<style>
		.reaction-button {
			box-shadow: -1px 2px 5px #0000002b;
			display: inline-block;
			padding: 4px 12px;
			font-weight: 600;
			text-decoration: none;
			background: white;
			color: black !important;
		}
		.reaction-button:hover {
			box-shadow: -1px 2px 8px #0000003d;
		}
		.reaction-button.liked {
			background: #92C52C;
		}
		.workout-details__reactions {
			margin-top: 8px;
		}
	</style>

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
		<h2 class="workout-exercises__header">
			Przebieg treningu:
		</h2>
		<div class="workout-exercises__list exercises-list">
			<?php foreach ($exercises as $n => $exercise): ?>
				<?php
				$display_title = $n == 0 ? true : $exercises[$n - 1]["type_id"] != $exercise["type_id"];
				if ($display_title):
				?>
					<h4 class="exercises-list__name">
						• ``$exercise["exercise_type"]``
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
							z ``$exercise["weight"]`` kg
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
	<div class="workout-comments">
		<div class="workout-comments__add">
			<h2>Dodaj komentarz:</h2>
			<form action="``PATH_PREFIX``/workout/``$workout['workout_id']``" method="post">
					<input type="hidden" name="action" value="comment">
					<textarea placeholder="Napisz komentarz..." name="comment"></textarea>
					<input type="submit" value="Dodaj">
				</form>
		</div>
		
		<?php if (!$comments): ?>
		<div class="comment-none">
			Nie ma jeszcze żadnych komentarzy. Bądź pierwszy!
		</div>
		<?php endif ?>
		
		<?php foreach ($comments as $comment): ?>
		<div class="comment">
			<a href="``PATH_PREFIX``/profile/``$comment['user_id']``">
				<img class="comment__avatar" src="``PATH_PREFIX``/``$comment['avatar']``">
			</a>
			<div class="comment__main">
				<a href="``PATH_PREFIX``/profile/``$comment['user_id']``" class="comment__name">
					``$comment['user_name']``
				</a>
				<span class="comment__created">
					``$comment['created']``
				</span>
				<div class="comment__content">
					``$comment['comment']``
				</div>
				
			</div>
		</div>
		<?php endforeach ?>
	</div>
</div>
