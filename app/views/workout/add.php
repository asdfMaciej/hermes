<div id="add-workout">
    <div id="snackbar" class="snackbar" ref="snackbar"></div>
    <div class="add-workout" v-cloak>
        <div class="add-workout__settings" v-if="view == 'main'">
            <div class="add-workout__settings-title">
                <h1 v-if="!editTitle" @click="openTitleEdition">
                    {{current.workout.workout.title}}&nbsp;<ion-icon name="create-outline"></ion-icon>
                </h1>
                <input type="text" v-if='editTitle' v-model='current.workout.workout.title'
                       placeholder='Podaj nazwę treningu' @keyup.enter="editTitle = false" ref="edittitle">
                <h4>{{timeElapsed}}</h4>
            </div>
        </div>

        <div class="add-workout__list" v-if="view == 'add-exercise'">
            <h2>Wybierz:</h2>
            <exercise-category :category='exerciseCategory' v-for="exerciseCategory in cache.exerciseCategories">
            </exercise-category>
        </div>

        <div class="add-workout__preview" v-if="view == 'main'" ref="exercises">
            <h2>Dodane ćwiczenia:</h2>
            <exercise v-for="(exercise, i) in current.workout.exercises"
                v-model='current.workout.exercises[i]'
                @delete='current.workout.exercises.splice(i, 1)'
                      @toggle-failure="current.workout.exercises[i].failure = current.workout.exercises[i].failure ? 0 : 1"
                :hide-title='i == 0 ? false : current.workout.exercises[i-1].type_id == exercise.type_id'
                      :show-add-rep="i == (current.workout.exercises.length - 1) || current.workout.exercises[i+1].type_id != exercise.type_id"
                :is-first="i == 0"
                :order="exerciseOrderInType[i]"
                :index="i"
                :past="cache.pastExercises[exercise.type_id]"></exercise>
            <span v-if="current.workout.exercises.length == 0">Nie wybrałeś żadnych ćwiczeń.</span>
            <div class="add-workout__list-buttons">
                <a href="#" @click.prevent="showExercisePicker" class="add-workout__add-exercise">Dodaj nowe ćwiczenie</a>
            </div>

        </div>

        <div class="add-workout__settings" v-if="view == 'main'">
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

        <div class="add-workout__presubmit" v-if="view == 'presubmit'">
            <span>(niedopracowany widok XD)</span>
            <h3>Dodaj opis (opcjonalnie):</h3>
            <textarea placeholder="Lepszy opis = więcej polubień, ez math" v-model="current.workout.workout.description"></textarea>
        </div>

        <div class="add-workout__submit" v-if="view == 'main' || view == 'presubmit'">
            <button @click="submit" :disabled="blockSubmit">
                Dodaj trening
            </button>
        </div>
    </div>
</div>

<script type="text/x-template" id="exercise-category-template">
	<div class="exercise-category">
		<span class="exercise-category__title" @click='show = !show'>{{show ? '▲' : '▼'}} {{category.name}}</span>

        <ul v-if='show'>
            <li v-for='exerciseType in category.exercises'>
                <a href='#' @click.prevent='$root.selectExerciseType(exerciseType)'>
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
		<div class="exercise__name" v-if="!hideTitle">{{exercise.exercise_type}}</div>
        <div class="exercise__headers" v-if="!hideTitle">
            <span class="no">Seria</span>
            <span class="past">Poprzednio</span>
            <span class="reps" v-if='exercise.show_reps == 1'>Powtórzenia</span>
            <span class="weight" v-if='exercise.show_weight == 1'>Obciążenie [kg]</span>
            <span class="duration" v-if='exercise.show_duration == 1'>Czas [s]</span>
        </div>
        <div class="exercise__attributes" :class="{unfinished: exercise.failure, finished: !exercise.failure}">
            <div class="exercise__no">
                {{order}}.
            </div>
            <div class="exercise_attribute exercise_attribute--past-set">
                {{pastSet}}
            </div>
            <input v-if='exercise.show_reps == 1' class="exercise_attribute exercise_attribute__reps"
                   type="number" v-model="exercise.reps" placeholder="Ilość" @focus="exercise.reps = ''">

            <input v-if='exercise.show_weight == 1' class="exercise_attribute exercise_attribute__weight"
                   type="float" v-model="exercise.weight" placeholder="Waga" @focus="exercise.weight = ''">

            <input v-if='exercise.show_duration == 1' class="exercise_attribute exercise_attribute__duration"
                   type="number" v-model="exercise.duration" placeholder="Czas" @focus="exercise.duration = ''">

            <div class="exercise__checkmark" :class="{unchecked: exercise.failure, checked: !exercise.failure}" @click="toggleFailure">
                <ion-icon name="checkmark-circle-outline" v-if="exercise.failure"></ion-icon>
                <ion-icon name="checkmark-circle" v-if="!exercise.failure"></ion-icon>
            </div>
            <a href="#" @click.prevent="remove" class="exercise-remove">
                <ion-icon name="close-outline" @click="remove"></ion-icon>
            </a>

        </div>

        <a href="#" @click.prevent="addRep" class="exercise__add-rep" v-if="showAddRep">
            Dodaj serię
        </a>
	</div>
</script>