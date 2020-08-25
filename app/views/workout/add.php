<div id="add-workout">
    <div id="snackbar" class="snackbar" ref="snackbar"></div>
    <div class="add-workout">
        <div class="add-workout__settings">
            <div class="add-workout__settings-title">
                <h1 v-if="!editTitle" @click="openTitleEdition">
                    {{current.workout.workout.title}}&nbsp;<ion-icon name="create-outline"></ion-icon>
                </h1>
                <input type="text" v-if='editTitle' v-model='current.workout.workout.title'
                       placeholder='Podaj nazwę treningu' @keyup.enter="editTitle = false" ref="edittitle">
                <h4>{{timeElapsed}}</h4>
            </div>
        </div>

        <div class="add-workout__list" v-if="showAddExercise">
            <h2>Wybierz:</h2>
            <ul>
                <li v-for='exerciseType in cache.exerciseTypes'>
                    <a href='#' @click.prevent='selectExerciseType(exerciseType)'>
                        {{exerciseType.exercise_type}}
                    </a>
                </li>
            </ul>
        </div>

        <div class="add-workout__preview" v-if="!showAddExercise" ref="exercises">
            <h2>Dodane ćwiczenia:</h2>
            <exercise v-for="(exercise, i) in current.workout.exercises"
                v-model='current.workout.exercises[i]'
                @delete='current.workout.exercises.splice(i, 1)'
                :hide-title='i == 0 ? false : current.workout.exercises[i-1].type_id == exercise.type_id'
                      :show-add-rep="i == (current.workout.exercises.length - 1) || current.workout.exercises[i+1].type_id != exercise.type_id"
                :is-first="i == 0"></exercise>
            <span v-if="current.workout.exercises.length == 0">Nie wybrałeś żadnych ćwiczeń.</span>
            <div class="add-workout__list-buttons">
                <a href="#" @click.prevent="showAddExercise = true" class="add-workout__add-exercise">Dodaj nowe ćwiczenie</a>
            </div>

        </div>

        <div class="add-workout__settings" v-if="!showAddExercise">
            <div class="add-workout__settings-gym">
                <h3>Wybierz miejsce ćwiczeń:</h3>
                <ul>
                    <li v-for="gym in cache.gyms" :class='{"exercise-selected": gym.gym_id == current.workout.workout.gym_id}'>
                        <a href='#' @click.prevent='current.workout.workout.gym_id = gym.gym_id'>
                            {{gym.name}}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="add-workout__submit" v-if="!showAddExercise">
            <button @click="submit" :disabled="blockSubmit">
                Dodaj trening
            </button>
        </div>
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
	<div class="exercise" :class="{'group-end': !isFirst && !hideTitle}">
		<span class="exercise__name" v-if="!hideTitle">{{exercise.exercise_type}}</span>

        <div class="exercise__attributes">
            <div class="exercise_attribute" v-if='exercise.show_reps == 1'>
                <input class="exercise_attribute__reps" type="number" v-model="exercise.reps" placeholder="Ilość" @focus="exercise.reps = ''">
                powtórzeń
            </div>
            <div class="exercise_attribute" v-if='exercise.show_weight == 1'>
                <input class="exercise_attribute__weight" type="number" v-model="exercise.weight" placeholder="Waga" @focus="exercise.weight = ''">
                kg
            </div>
            <div class="exercise_attribute" v-if='exercise.show_duration == 1'>
                <input class="exercise_attribute__duration" type="number" v-model="exercise.duration" placeholder="Czas" @focus="exercise.duration = ''">
                sekund
            </div>
        </div>
        <a href="#" @click.prevent="addRep" class="exercise__add-rep" v-if="showAddRep">
            Dodaj serię
        </a>
	</div>
</script>