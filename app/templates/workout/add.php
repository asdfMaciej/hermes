<div id="app" class='add-workout'>
	<div class="add-workout__settings">
		<h1>Dodaj trening:</h1>
		<input type="text" v-model='current.workout.workout.name' placeholder='Podaj nazwę treningu'>
		<h3>Wybierz siłownię:</h3>
		<div v-for="gym in cache.gyms" :class='{"exercise-selected": gym.gym_id == current.workout.workout.gym_id}'>
			<a href='#' @click.prevent='current.workout.workout.gym_id = gym.gym_id'>
				{{gym.name}}
			</a>
		</div>
	</div>
	<div class="add-workout__list">
		<h2>Wszystkie ćwiczenia:</h2>
		<div v-for="exerciseType in cache.exerciseTypes" :class='{"exercise-selected": exerciseType == selected.exerciseType}'>
			<a href='#' @click.prevent='selected.exerciseType = exerciseType'>
				{{exerciseType.exercise_type}}
			</a>
		</div>
	</div>

	<div v-if='selected.exerciseType.type_id' class='add-workout__add'>
		<h2>Dodaj:</h2>
		<exercise edit-only :value='selected.exerciseType' @input='addExercise($event)'>
		</exercise>
	</div>
	
	<div class="add-workout__preview">
		<h2>Dodane ćwiczenia:</h2>
		<exercise v-for="(exercise, i) in current.workout.exercises" 
			v-model='current.workout.exercises[i]'
			@delete='current.workout.exercises.splice(i, 1)'></exercise>
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

<script type="text/x-template" id="exercise-template">
	<div class="exercise">
		<h3>{{exercise.exercise_type}}</h3>
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

<script>
class APIResponse {
	constructor(axiosResponse) {
		this.data = axiosResponse.data;
		this.code = axiosResponse.status;
		this.codeText = axiosResponse.statusText;
		this.url = axiosResponse.config.url;
		this.method = axiosResponse.config.method;
	}

	preview(print=true) {
		let message = `${this.code} (${this.codeText}) returned for ${this.url}`;
		message += ` [${this.method}]`;
		message += "\n" + JSON.stringify(this.data);
		message += "\n---\n";
		if (print)
			console.log(message);

		return message;
	}
}

class API {
	getPath(method) {
		return PATH_PREFIX + '/api/' + method;
	}

	post(path, data, onResponse) {
		this._request(
			axios.post(this.getPath(path), data),
			onResponse
		)
	}

	get(path, onResponse) {
		this._request(
			axios.get(this.getPath(path)),
			onResponse
		)
	}

	_request(request, onResponse) {
		request.then((response) => {
			let r = new APIResponse(response);
			if (DEBUG)
				r.preview();

			onResponse(r, r.data);
		})
		.catch((error) => {
			let r = new APIResponse(error.response);
			if (DEBUG)
				r.preview();

			onResponse(r, r.data);
		});
	}
}

function copy(o) {
	return JSON.parse(JSON.stringify(o));
}

Vue.component('exercise', {
	props: {
		value: undefined,
		editOnly: Boolean,
		viewOnly: Boolean
	},
	template: '#exercise-template',
	data: function() {return {
		showEdit: false
	}},

	methods: {
		finishEdit: function() {
			if (this.validateExercise(this.value)) {
				this.$emit('input', this.value);
				this.$emit('add', this.value);
				this.showEdit = false;
			}
		},

		validateExercise: function(e) {
			if (!e.type_id)
				return false;

			if ((e.show_reps == 1 && (e.reps == null || e.reps === ""))
				|| (e.show_duration == 1 && (e.duration == null || e.duration === ""))
				|| (e.show_weight == 1 && (e.weight == null || e.weight === "")))
				return false;

			if (e.reps <= 0 || e.weight < 0 || e.duration <= 0)
				return false;

			return true;
		},

		remove: function() {
			this.$emit('delete');
		}
	},

	computed: {
		exercise: function() {
			return this.value;
		},

		valid: function() {
			return this.validateExercise(this.value);
		},

		edit: function() {
			if (this.editOnly)
				return true;

			if (this.viewOnly)
				return false;

			return this.showEdit;
		}
	}
});

var t = new Vue({
	el: "#app",
	data: {
		cache: {
			exerciseTypes: [],
			gyms: []
		},
		selected: {
			exerciseType: {}
		},
		current: {
			workout: {
				workout: {
					gym_id: null,
					name: ""
				},
				exercises: []
			}
		},
		api: null
	},

	mounted: function() {
		this.api = new API();
		this.api.get('exercise_types', (response, data) => {
			this.cache.exerciseTypes = data.exercise_types;
		});
		this.api.get('gyms', (response, data) => {
			this.cache.gyms = data.gyms;
		});
	},

	computed: {
		validateWorkoutErrors: function() {
			let w = this.current.workout;
			let errors = [];
			if (w.workout.gym_id == null || !w.workout.name)
				errors.push("Nie ustawiono siłowni lub nazwy treningu!");

			if (w.exercises.length == 0)
				errors.push("Nie dodano żadnych ćwiczeń!");

			return errors;
		}
	},

	methods: {
		submit: function() {
			this.addWorkout(this.current.workout);
		},

		addExercise: function(exercise) {
			console.log(exercise);
			this.current.workout.exercises.push(copy(exercise));
		},

		isEmptyObject: function(obj) {
			return Object.keys(obj).length === 0; 
		},

		addWorkout: function(workout) {
			this.api.post('workouts', workout, (response, data) => {
				if (response.code >= 400) {
					this.snackbar(response.code, 'Nie udało się dodać treningu.');
				} else {
					this.snackbar(response.code, 'Udało się dodać trening.');
					this.redirect('workout/'+data.workout_id);
				}
			});
		},

		snackbar: function(code, message) {
			let prefix = code >= 400 ? "[!]" : "[*]";
			console.log(prefix + ' ' + message);
		},

		redirect: function(page) {
			window.location.href = PATH_PREFIX + '/' + page;
		}
	}
});
</script>