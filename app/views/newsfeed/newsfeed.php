<div class="newsfeed" id="newsfeed">
	<?php foreach ($workouts as $workout): ?>
		<newsfeed-item :workout='<?php echo str_replace("'", "&#39;", json_encode($workout, JSON_UNESCAPED_UNICODE)); ?>'></newsfeed-item>
	<?php endforeach ?>
</div>

<script type="text/x-template" id="newsfeed-item-template">
	<div class="newsfeed-item" v-if="!deleted">
		<div class="feed-workout__menu" v-if="showMenu">
			<button class="hermes" @click.prevent="easteregg">Idź ćwiczyć</button>
            <button class="hermes" @click.prevent="onEdit" v-if="userId == workout.user_id">Edytuj trening</button>
			<button class="hermes" @click.prevent="onDelete" v-if="userId == workout.user_id">Usuń trening</button>
		</div>
		<div class="feed-workout" :class="{activemenu: showMenu}" @click="clickWorkout">
			<div class="feed-workout__avatar">
                <a :href="'``PATH_PREFIX``/profile/' + workout.user_id">
				    <img loading="lazy" :src="'``PATH_PREFIX``/' + workout.avatar" class="avatar">
                </a>
			</div>
			<div class="feed-workout__container">
				<a href="#" class="feed-workout__menu-button" @click.prevent="showMenu = !showMenu; toggledMenu = true">
					<ion-icon name="ellipsis-vertical"></ion-icon>
				</a>
				<div class="feed-workout__author">
					<a :href="'``PATH_PREFIX``/profile/' + workout.user_id">
						{{workout.user_name}}
					</a>
				</div>
				<div class="feed-workout__date">
					<span :title="workout.date" class="date">{{time(workout.date)}}</span>
                    <span v-if="workout.duration != 0">- {{duration(workout.duration)}}</span>
					-
					<a :href="'``PATH_PREFIX``/gym/' + workout.gym_id">
						{{workout.gym_name}}
					</a>
				</div>
				<a :href="'``PATH_PREFIX``/workout/' + workout.workout_id">
					<div class="feed-workout__title">
						{{workout.title}}
					</div>
				</a>
                <div class="feed-workout__description" v-if="workout.description">{{workout.description}}</div>
				<div class="feed-workout__reactions">
					<reaction-button :workout='workout'></reaction-button>

					<a :href="'``PATH_PREFIX``/workout/' + workout.workout_id">{{workout.comments}} {{(workout.comments == 0 || workout.comments >= 5) ? 'komentarzy' : (workout.comments == 1 ? 'komentarz' : 'komentarze')}}</a>
				</div>
			</div>

            <div class="feed-workout__summary">
                <div v-for="exercise in workout.summary">
                    <a :href="'``PATH_PREFIX``/exercise/' + exercise.type_id"><strong>• {{exercise.exercise_type}}</strong></a>
                    <span class="desktop-only">-</span>
                    <br class="mobile-only">
                    {{exercise.sets}} {{exercise.sets == 1 ? 'seria' : (exercise.sets < 5 ? 'serie' : 'serii')}}
                    <span v-if="exercise.max_reps">
                        po {{exercise.max_reps != exercise.min_reps ?
                        `${exercise.min_reps}-${exercise.max_reps}` : exercise.max_reps}} powtórzeń
                    </span>

                    <span v-if="exercise.max_duration">
                        po {{exercise.max_duration != exercise.min_duration ?
                        `${exercise.min_duration}-${exercise.max_duration}` : exercise.max_duration}}
                        s
                    </span>

                    <span v-if="exercise.max_weight && exercise.max_weight != '0'">
                        z {{exercise.max_weight != exercise.min_weight ?
                        `${exercise.min_weight}-${exercise.max_weight}` : exercise.max_weight}}
                        kg
                    </span>


                </div>
            </div>
		</div>
		<div class="comment comment--newsfeed" v-if='workout.comment'>
			<div class="comment__main">
                <a :href="'``PATH_PREFIX``/profile/' + workout.comment_user_id">
                    <img loading="lazy" class="comment__avatar avatar" :src="'``PATH_PREFIX``/' + workout.comment_avatar">
                </a>
				<a :href="'``PATH_PREFIX``/profile/' + workout.comment_user_id" class="comment__name">
					{{workout.comment_user_name}}
				</a>
				<span :title="workout.comment_created" class="comment__created date">
					{{time(workout.comment_created)}}
				</span>
				<div class="comment__content">
					{{workout.comment}}
				</div>
			</div>
		</div>
	</div>
</script>

<?php $this->nest("vue/reaction-button.php"); ?>