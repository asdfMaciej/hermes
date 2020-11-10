<div id="add-workout">
    <div id="snackbar" class="snackbar" ref="snackbar"></div>
    <div class="add-workout" v-cloak v-if="viewGroup == 'workout'">
        <div class="add-workout__settings" v-if="view == 'main'">
            <div class="add-workout__settings-title">
                <h1 v-if="!editTitle" @click="openTitleEdition">
                    {{current.workout.workout.title}}&nbsp;<ion-icon name="create-outline"></ion-icon>
                </h1>
                <input type="text" v-if='editTitle' v-model='current.workout.workout.title'
                       placeholder='Podaj nazwę treningu' @keyup.enter="editTitle = false" ref="edittitle">
                <h4>{{timeElapsed}}</h4>
            </div>
            <a href="#" @click.prevent="showRoutinePicker()">Wybierz plan treningowy</a>
        </div>

        <div class="add-workout__list" v-if="view == 'routines'">
            <a href="#" @click.prevent="view = 'main'">Wróć do widoku głównego</a>
            <h2>Wybierz plan:</h2>
            <div @click.prevent='selectRoutine(routine)' class="routine-mini" v-for="routine in cache.routines">
                <img class='avatar' :src="'``PATH_PREFIX``/' + routine.avatar">
                <span>{{routine.name}}</span>
            </div>
        </div>
        <div class="add-workout__list" v-if="view == 'add-exercise'">
            <a href="#" @click.prevent="view = 'main'">Wróć do widoku głównego</a>
            <h2>Wybierz:</h2>
            <a href="#" @click.prevent="exerciseLanguage = 'pl'" v-if="exerciseLanguage == 'en'">🇵🇱 Pokaż nazwy po polsku</a>
            <a href="#" @click.prevent="exerciseLanguage = 'en'" v-if="exerciseLanguage == 'pl'">🇬🇧 Pokaż nazwy po angielsku</a>
            <br>
            <exercise-category :exercise-language="exerciseLanguage" :category='exerciseCategory' v-for="exerciseCategory in cache.exerciseCategories">
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

        <div class="add-workout__presubmit" v-if="view == 'presubmit'">
            <div class="add-workout__settings-title">
                <h1 v-if="!editTitle" @click="openTitleEdition">
                    {{current.workout.workout.title}}&nbsp;<ion-icon name="create-outline"></ion-icon>
                </h1>
                <input type="text" v-if='editTitle' v-model='current.workout.workout.title'
                       placeholder='Podaj nazwę treningu' @keyup.enter="editTitle = false" ref="edittitle">
                <h4>{{timeElapsed}}</h4>
            </div>
            <textarea placeholder="Dodaj opis do swojego treningu." v-model="current.workout.workout.description"></textarea>
            <h4>Dodaj zdjęcie:</h4>
            <input type="file" accept="image/*" @change="uploadImage($event)" id="workout-images-upload">
            <div class="add-workout__photos">
                <img v-for="(base64, filename) in current.workout.images" :src="base64">
            </div>
            <label><input type="checkbox" v-model='current.workout.routine.add'>
            Stwórz nowy plan treningowy</label>
            <input type="text" v-model='current.workout.routine.name' v-if='current.workout.routine.add' placeholder="Podaj nazwę planu">
        </div>

        <div class="add-workout__settings" v-if="view == 'presubmit'">
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

        <div class="add-workout__submit" v-if="view == 'main' || view == 'presubmit'">
            <button @click="submit" :disabled="blockSubmit">
                {{editedWorkoutId == null ? (view == 'presubmit' ? 'Dodaj trening' : 'Zakończ trening') : 'Edytuj trening'}}
                {{view == 'presubmit' && progress > 0 ? ` (${progress} %)` : ''}}
            </button>
        </div>
    </div>
    <div class="add-workout-timer" v-cloak v-if="viewGroup == 'timer'">
        <base-timer :time="timer.time"></base-timer>
        <label><input type="number" v-model="timer.time"> sekund</label>
    </div>
    <div class="add-workout-calculator" v-cloak v-if="viewGroup == 'calculator'">
        <label>
            Obciążenie [kg]:
            <input type="float" v-model="calculator.weight">
        </label>
        <label>
            Powtórzenia:
            <input type="number" v-model="calculator.reps">
        </label>
        <button @click="calculateRM">Oblicz</button>
        <pre>{{calculator.result}}</pre>
    </div>
    <div class="add-workout-switch" v-if="view != 'presubmit'">
        <a href="#" @click.prevent="switchViewGroup('workout')" :class="{selected: viewGroup == 'workout'}">
            <ion-icon name="barbell"></ion-icon>
            Trening
        </a>
        <a href="#" @click.prevent="switchViewGroup('timer')" :class="{selected: viewGroup == 'timer'}">
            <ion-icon name="timer"></ion-icon>
            Stoper
        </a>
        <a href="#" @click.prevent="switchViewGroup('calculator')" :class="{selected: viewGroup == 'calculator'}">
            <ion-icon name="calculator"></ion-icon>
            Kalkulator
        </a>
    </div>
</div>

<script type="text/x-template" id="exercise-category-template">
	<div class="exercise-category">
		<span class="exercise-category__title" @click='show = !show'>{{show ? '▲' : '▼'}} {{category.name}}</span>

        <ul v-if='show'>
            <li v-for='exerciseType in category.exercises'>
                <a href='#' @click.prevent='$root.selectExerciseType(exerciseType)'>
                    {{exerciseLanguage == "pl" ? exerciseType.exercise_type : exerciseType.exercise_type_en}}
                </a>
            </li>
        </ul>

		<exercise-category :exercise-language="exerciseLanguage" :category='exerciseCategory' v-for="exerciseCategory in category.categories" v-if='show'>
		</exercise-category>
	</div>
</script>

<script type="text/x-template" id="exercise-template">
	<div class="exercise" :class="{'group-end': !isFirst && !hideTitle}">
		<div class="exercise__name" v-if="!hideTitle">{{exercise.exercise_type}}</div>
        <a class="exercise__toggle-graphs" v-if="!hideTitle && exercise.type_id in $root.cache.exerciseHistory" @click.prevent="toggleGraphs">
            {{showGraphs ? 'Ukryj wykresy' : 'Pokaż wykresy'}}
        </a>
        <div class="exercise__graphs" v-if="showGraphs">
            <div style="max-width: 500px">
                <canvas :id="'canvas-weight-'+index"></canvas>
            </div>
            <div style="max-width: 500px">
                <canvas :id="'canvas-volume-'+index"></canvas>
            </div>
            <div style="max-width: 500px">
                <canvas :id="'canvas-rm-'+index"></canvas>
            </div>
        </div>
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
            <a href="#" @click.prevent="usePastSet()" class="exercise_attribute exercise_attribute--past-set">
                {{pastSet}}
            </a>
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
                <ion-icon name="close-outline"></ion-icon>
            </a>

        </div>

        <a href="#" @click.prevent="addRep" class="exercise__add-rep" v-if="showAddRep">
            Dodaj serię
        </a>
	</div>
</script>

<script type="text/x-template" id="base-timer-template">
    <div class="base-timer">
  <div class="base-timer__timer">
    <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
      <g class="base-timer__circle">
        <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
        <path
          :stroke-dasharray="circleDasharray"
          class="base-timer__path-remaining"
          :class="remainingPathColor"
          d="
            M 50, 50
            m -45, 0
            a 45,45 0 1,0 90,0
            a 45,45 0 1,0 -90,0
          "
        ></path>
      </g>
    </svg>
    <span class="base-timer__label">{{ formattedTimeLeft }}</span>
  </div>
  <button @click="restart">Start</button>
</div>
</script>
