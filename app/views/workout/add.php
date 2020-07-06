<div id="add-workout" class='add-workout'>
	<div class="add-workout__settings">
		<div class="add-workout__settings-title">
			<h1>Dodaj trening:</h1>
			<input type="text" v-model='current.workout.workout.title' placeholder='Podaj nazwę treningu'>
		</div>

		<div class="add-workout__settings-gym">	
			<h3>Wybierz miejsce ćwiczeń:</h3>
			<div v-for="gym in cache.gyms" :class='{"exercise-selected": gym.gym_id == current.workout.workout.gym_id}'>
				<a href='#' @click.prevent='current.workout.workout.gym_id = gym.gym_id'>
					{{gym.name}}
				</a>
			</div>
		</div>
	</div>

	<div class="add-workout__list">
		<h2>Wybierz:</h2>
		<exercise-category :category='exerciseCategory' v-for="exerciseCategory in cache.exerciseCategories">
		</exercise-category>
	</div>

	<div v-if='selected.exerciseType.type_id' class='add-workout__add'>
		<h2>Dodaj:</h2>
		<exercise edit-only :value='selected.exerciseType' @input='addExercise($event)'>
		</exercise>
	</div>
	<div v-else>
		<h2>Dodaj:</h2>
		<h4>Nie wybrałeś żadnego ćwiczenia.</h4>
	</div>
	
	<div class="add-workout__preview">
		<h2>Dodane ćwiczenia:</h2>
		<exercise v-for="(exercise, i) in current.workout.exercises" 
			v-model='current.workout.exercises[i]'
			@delete='current.workout.exercises.splice(i, 1)'
			:hide-title='i == 0 ? false : current.workout.exercises[i-1].type_id == exercise.type_id'></exercise>
		<h4 v-if="current.workout.exercises.length == 0">Dodaj ćwiczenia do treningu.</h4>
	</div>
	
	<div class="add-workout__submit">
		<div class="add-workout__error" v-for='error in validateWorkoutErrors'>
			{{error}}
		</div>
		<button :disabled='validateWorkoutErrors.length > 0' @click="submit">
			Dodaj trening
		</button>
	</div>
	
</div>

<script type="text/x-template" id="exercise-category-template">
	<div class="exercise-category">
		<span class="exercise-category__title" @click='show = !show'>{{show ? '▲' : '▼'}} {{category.name}}</span>

		<ul v-if='show' class="exercise-category__exercises">
			<li v-for='exerciseType in category.exercises'>
				<a href='#' @click.prevent='$root.selected.exerciseType = exerciseType' :class='{"exercise-selected": exerciseType == $root.selected.exerciseType}'>
					{{exerciseType.exercise_type}}
				</a>
			</li>
		</ul>

		<exercise-category :category='exerciseCategory' v-for="exerciseCategory in category.categories" v-if='show'>
		</exercise-category>
	</div>
</script>

<script type="text/x-template" id="exercise-template">
	<div class="exercise">
		<h3 v-if="!hideTitle">{{exercise.exercise_type}}</h3>
		<div v-if='exercise.show_reps == 1'>
			<span>Ilość powtórzeń:</span>
			<input v-if='edit' type="number" v-model="exercise.reps">
			<span v-else>{{exercise.reps}}</span>
		</div>
		<div v-if='exercise.show_weight == 1'>
			<span>Obciążenie:{{edit ? ' [kg]' : ''}}</span>
			<input v-if='edit' type="number" v-model="exercise.weight">
			<span v-else>{{exercise.weight}} kg</span>
		</div>
		<div v-if='exercise.show_duration == 1'>
			<span>Czas trwania:{{edit ? ' [s]' : ''}}</span>
			<input v-if='edit' type="number" v-model="exercise.duration">
			<span v-else>{{exercise.duration}} s</span>
		</div>
		<button @click='remove' v-if='!viewOnly && !editOnly'>Usuń</button>
		<button @click='showEdit = true && !viewOnly' v-if='!edit'>Edytuj</button>
		<button @click='finishEdit' v-if='edit' :disabled='!valid'>Zapisz</button>
	</div>
</script>