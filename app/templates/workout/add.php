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
		<div v-for="exerciseType in cache.exerciseTypes" :class='{"exercise-selected": exerciseType == selected.exerciseType}'>
			<a href='#' @click.prevent='selected.exerciseType = exerciseType'>
				{{exerciseType.exercise_type}}
			</a>
		</div>
	</div>

	<div v-if='selected.exerciseType.type_id' class='add-workout__add'>
		<h2>{{selected.exerciseType.exercise_type}}</h2>
		<div v-if='selected.exerciseType.show_reps == 1'>
			<span>Ilość powtórzeń:</span>
			<input type="number" v-model="selected.exerciseType.reps">
		</div>
		<div v-if='selected.exerciseType.show_weight == 1'>
			<span>Obciążenie [kg]:</span>
			<input type="number" v-model="selected.exerciseType.weight">
		</div>
		<div v-if='selected.exerciseType.show_duration == 1'>
			<span>Czas trwania [s]:</span>
			<input type="number" v-model="selected.exerciseType.duration">
		</div>
		<button :disabled='!validExercise' @click="addExercise">
			Dodaj ćwiczenie
		</button>
	</div>
	
	<div class="add-workout__preview">
		<div v-for="exercise in current.workout.exercises" class='exercise'>
			<h2>{{exercise.exercise_type}}</h2>
			<div v-if='exercise.show_reps == 1'>
				<span>Ilość powtórzeń:</span>
				<span>{{exercise.reps}}</span>
			</div>
			<div v-if='exercise.show_weight == 1'>
				<span>Obciążenie:</span>
				<span>{{exercise.weight}} kg</span>
			</div>
			<div v-if='exercise.show_duration == 1'>
				<span>Czas trwania:</span>
				<span>{{exercise.duration}} s</span>
			</div>
		</div>
	</div>
	
	<button :disabled='!validWorkout' @click="submit" class='add-workout__submit'>
		Dodaj trening
	</button>
</div>

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
		validExercise: function() {
			let e = this.selected.exerciseType;
			return this.validateExercise(e);
		},

		validWorkout: function() {
			let w = this.current.workout;
			if (w.workout.gym_id == null || !w.workout.name)
				return false;

			if (w.exercises.length == 0)
				return false;

			for (let e of w.exercises)
				if (!this.validateExercise(e))
					return false;

			return true;
		}
	},

	methods: {
		submit: function() {
			this.addWorkout(this.current.workout);
		},

		addExercise: function() {
			let exerciseType = this.selected.exerciseType;
			if (this.isEmptyObject(exerciseType))
				return this.snackbar(400, "Wybierz ćwiczenie!");

			this.current.workout.exercises.push(copy(exerciseType));
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

		validateExercise: function(e) {
			if (!e.type_id)
				return false;

			if ((e.show_reps == 1 && e.reps == null)
				|| (e.show_duration == 1 && e.duration == null)
				|| (e.show_weight == 1 && e.weight == null))
				return false;

			if (e.reps <= 0 || e.weight < 0 || e.duration <= 0)
				return false;

			return true;
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