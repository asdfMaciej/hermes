function copy(o) {
	return JSON.parse(JSON.stringify(o));
}

Vue.component('exercise-category', {
	props: {
		category: undefined,
		exerciseLanguage: undefined
	},
	template: '#exercise-category-template',
	data: function() {return {
		show: true
	}}
});

const FULL_DASH_ARRAY = 283;
const WARNING_THRESHOLD = 20;
const ALERT_THRESHOLD = 10;

const COLOR_CODES = {
  info: {
    color: "green"
  },
  warning: {
    color: "orange",
    threshold: WARNING_THRESHOLD
  },
  alert: {
    color: "red",
    threshold: ALERT_THRESHOLD
  }
};

const TIME_LIMIT = 20;

/* component taken from:
https://medium.com/js-dojo/how-to-create-an-animated-countdown-timer-with-vue-89738903823f */
Vue.component('base-timer', {
	template: '#base-timer-template',
	props: {
		time: undefined
	},

  data: function() {
    return {
      
    };
  },

  computed: {
    circleDasharray() {
      return `${(this.timeFraction * FULL_DASH_ARRAY).toFixed(0)} 283`;
    },

    formattedTimeLeft() {
      const timeLeft = this.timeLeft;
      const minutes = Math.floor(timeLeft / 60);
      let seconds = timeLeft % 60;

      if (seconds < 10) {
        seconds = `0${seconds}`;
      }

      return `${minutes}:${seconds}`;
    },

    timeLeft() {
      return this.$root.timer.timeLimit - this.$root.timer.timePassed;
    },

    timeFraction() {
      const rawTimeFraction = this.timeLeft / this.$root.timer.timeLimit;
      return rawTimeFraction - (1 / this.$root.timer.timeLimit) * (1 - rawTimeFraction);
    },

    remainingPathColor() {
      const { alert, warning, info } = COLOR_CODES;

      if (this.timeLeft <= alert.threshold) {
        return alert.color;
      } else if (this.timeLeft <= warning.threshold) {
        return warning.color;
      } else {
        return info.color;
      }
    }
  },

  watch: {
    timeLeft(newValue) {
      if (newValue === 0) {
        this.onTimesUp();
      }
    }
  },

  mounted: function() {
  	if (this.timeLeft <= 0) {
  		this.$root.timer.timePassed = this.$root.timer.timeLimit;
  		this.onTimesUp();
  	}
  },

  methods: {
    onTimesUp() {
      clearInterval(this.$root.timer.timerInterval);
    },

    startTimer(timeLimit) {
    	this.$root.timer.timeLimit = timeLimit; 
      this.$root.timer.timerInterval = setInterval(() => (this.$root.timer.timePassed += 1), 1000);
    },

    restart() {
    	clearInterval(this.$root.timer.timerInterval);
    	this.$root.timer.timePassed = 0;
    	this.startTimer(this.$root.timer.time || TIME_LIMIT);
    }
  }
});

Vue.component('exercise', {
	props: {
		value: undefined,
		isFirst: Boolean,
		hideTitle: Boolean,
		showAddRep: Boolean,
		order: Number,
		index: Number,
		past: undefined
	},
	template: '#exercise-template',
	methods: {
		toggleFailure: function() {
			this.$emit('toggle-failure', this.value);
		},

		remove: function() {
			this.$emit('delete');
		},

		addRep: function() {
			this.$root.addExercise(this.exercise, this.index+1);
		},

		usePastSet: function() {
			let set = this.past[this.order - 1];
			if (!set)
				return;

			if (set.reps != null)
				this.$set(this.exercise, 'reps', set.reps);
			if (set.weight != null)
				this.$set(this.exercise, 'weight', set.weight);
			if (set.duration != null)
				this.$set(this.exercise, 'duration', set.duration);
		}
	},

	computed: {
		exercise: function() {
			return this.value;
		},

		pastSet: function() {
			if (!this.past)
				return "—";

			let set = this.past[this.order - 1];
			if (!set)
				return "—";

			let text = "";
			if (set.reps != null)
				text += set.reps;

			if (set.weight != null) {
				if (text)
					text += ' × ';

				text += `${set.weight} kg`;
			}
			if (set.duration != null) {
				if (text)
					text += ' × ';

				text += `${set.duration} s`;
			}

			return text;
		}
	}
});

var t = new Vue({
	el: "#add-workout",
	data: {
		cache: {
			exerciseCategories: [],
			exerciseTypes: [],
			gyms: [],
			routines: [],
			pastExercises: {}
		},
		selected: {
			exerciseType: {}
		},
		current: {
			workout: {
				workout: {
					gym_id: null,
					title: "Trening szefa",
					description: "",
					duration: 0
				},
				exercises: [],
				startMoment: null,
				routine: {
					add: false,
					name: ''
				},
				images: {}
			}
		},
		timer: {
			time: 30,
			timePassed: 0,
      		timerInterval: null,
      		timeLimit: 0
		},
		editedWorkoutId: null,
		timeElapsed: '00:00:00',
		api: null,
		editTitle: false,
		view: 'main',
		viewGroup: 'workout',
		blockSubmit: false,
		exerciseLanguage: "pl",
		progress: 0
	},

	mounted: function() {
		this.api = new API((e) => {this.showProgress(e);});
		this.api.get('exercise_categories', (response, data) => {
			this.cache.exerciseCategories = data.exercise_categories;
		});
		this.api.get('exercise_types', (response, data) => {
			this.cache.exerciseTypes = data.exercise_types;
		});
		this.api.get('gyms', (response, data) => {
			this.cache.gyms = data.gyms;
		});
		this.api.get('routines', (response, data) => {
			this.cache.routines = data.routines;
		});

		window.onpopstate = this.backButtonPressed;
		window.onbeforeunload = this.backButtonPressed;

		if (window.location.pathname.indexOf("edit") === -1) {
			// creating a new workout
			this.initAddWorkout();
		} else {
			// editing an existing workout
			this.initEditWorkout();
		}
	},

	computed: {
		validateWorkoutErrors: function() {
			let w = this.current.workout;
			let errors = [];
			if (w.workout.gym_id == null || !w.workout.title)
				errors.push("Nie ustawiono siłowni lub nazwy treningu!");

			if (w.exercises.length == 0)
				errors.push("Nie dodano żadnych ćwiczeń!");

			return errors;
		},

		/**
		 * For a given exercise index, it returns its position for its exercise type.
		 * For example:
		 * given [Plank, Plank, Squats, Plank, Deadlift]
		 * the expected return is [1, 2, 1, 3, 1]
		 * @returns {[]}
		 */
		exerciseOrderInType: function() {
			let typeIndexes = {};
			let index = [];
			for (let exercise of this.current.workout.exercises) {
				let typeId = exercise.type_id;
				if (!(typeId in typeIndexes))
					typeIndexes[typeId] = 1;

				index.push(typeIndexes[typeId]);
				typeIndexes[typeId] = typeIndexes[typeId] + 1;
			}

			return index;
		}
	},

	methods: {
		initEditWorkout: function() {
			// get the id from path
			const pathElements = window.location.pathname.split("workout/");
			this.editedWorkoutId = pathElements[1].split("/")[0];

			this.api.get(`workouts/${this.editedWorkoutId}`, (response, data) => {
				this.$set(this.current.workout, 'workout', data.workout);
				this.$set(this.current.workout, 'exercises', data.exercises);
				this.timeElapsed = moment.utc(data.workout.duration*1000).format('HH:mm:ss');

				// fetch past exercises?
				// this.getPastExercises(typeId); if add in future
			});
		},

		showProgress: function(event) {
			this.progress = Math.round((event.loaded * 100) / event.total);
		},


		initAddWorkout: function() {
			this.current.workout.startMoment = moment();
			setInterval(() => {
				let start = this.current.workout.startMoment;
				// moment.js doesn't support duration, so this is kinda a hack
				this.timeElapsed = moment.utc(moment().diff(start)).format("HH:mm:ss")
			}, 1000);
		},

		backButtonPressed: function(event) {
			if (this.viewGroup == 'timer') {
				this.viewGroup = 'workout';
				return;
			}

			if (this.view == 'add-exercise') {
				this.view = 'main';
			}
			if (this.view == 'presubmit') {
				this.view = 'main';
			}
			if (this.view == 'routines') {
				this.view = 'main';
			}
			return "";
		},

		selectRoutine: function(routine) {
			if (!confirm(`Czy chcesz wybrać plan '${routine.name}'? Usunie to twój obecny trening.`))
				return;

			this.view = 'main';
			this.api.get(`routines/${routine.routine_id}`, (response, data) => {
				this.$set(this.current.workout, 'exercises', []);
				for (let exercise of data.exercises) {
					for (let i=0; i<exercise.sets; i++) {
						exercise.failure = true;
						this.addExercise(exercise);
					}
					this.getPastExercises(exercise.type_id);
				}
			});
		},

		switchViewGroup: function(group) {
			this.viewGroup = group;
			history.pushState({page: group}, "Dodawanie treningu - Hermes", "#"+group);
			if (group == 'workout') {
				this.$nextTick(() => {this.scrollToExercisesBottom();});
			}
		},

		showRoutinePicker: function() {
			this.view = 'routines';
			history.pushState({page: 'routines'}, "Wybierz plan treningowy - Hermes", "#routines");
			this.$nextTick(() => {window.scrollTo({ top: 0, behavior: 'smooth' });});
		},

		showExercisePicker: function() {
			this.view = 'add-exercise';
			history.pushState({page: 'exercise-picker'}, "Wybierz ćwiczenie - Hermes", "#exercises");
			this.$nextTick(() => {window.scrollTo({ top: 0, behavior: 'smooth' });});
		},

		blockSubmitButton: function(durationMs) {
			this.blockSubmit = true;
			setTimeout(() => {
				this.blockSubmit = false;
			}, durationMs);
		},

		submit: function() {
			if (this.validateWorkoutErrors.length > 0) {
				this.blockSubmitButton(3000);
				this.snackbar(400, this.validateWorkoutErrors.join(' '));
				return;
			}

			// duration and reps have to be filled in
			let mandatoryInputs = document.querySelectorAll("input.exercise_attribute__duration, input.exercise_attribute__reps");
			let missingFields = false;
			mandatoryInputs.forEach((input) => {
				if (!input.value) {
					input.setCustomValidity("Uzupełnij pole!");
					missingFields = true;
				} else {
					input.setCustomValidity("");
				}
			});

			// weight can be empty, fill in zeros for clarity
			// using Vue instead of selectors due to reactivity
			for (let index in this.current.workout.exercises) {
				let exercise = this.current.workout.exercises[index];
				if (exercise.show_weight && !exercise.weight) {
					Vue.set(this.current.workout.exercises[index], 'weight', 0);
				}
			}

			if (missingFields) {
				this.blockSubmitButton(2000);
				this.snackbar(400, "Uzupełnij wszystkie pola!");
				return;
			}

			if (this.view == 'main') {
				this.view = 'presubmit';
				history.pushState({page: 'presubmit'}, "Potwierdź trening - Hermes", "#presubmit");
				this.$nextTick(() => {window.scrollTo({ top: 0, behavior: 'smooth' });});
				return;
			}

			if (this.view == 'presubmit') {
				if (this.current.workout.routine.add && !this.current.workout.routine.name) {
					this.blockSubmitButton(2000);
					this.snackbar(400, "Musisz dodać nazwę planu!");
					return;
				}
			}

			this.addWorkout(this.current.workout);
		},

		openTitleEdition: function() {
			this.editTitle = true;
			this.$nextTick(() => {
				this.$refs.edittitle.focus();
			});
		},

		addExercise: function(exercise, index) {
			exercise = copy(exercise);
			exercise.failure = 1;
			if (index) {
				this.current.workout.exercises.splice(index, 0, exercise);
			} else {
				this.current.workout.exercises.push(exercise);
			}

		},

		isEmptyObject: function(obj) {
			return Object.keys(obj).length === 0; 
		},

		selectExerciseType: function(exerciseType) {
			this.view = 'main';
			this.addExercise(exerciseType);
			this.$nextTick(this.scrollToExercisesBottom);

			let typeId = exerciseType.type_id;
			this.getPastExercises(typeId);
		},

		getPastExercises: function(typeId) {
			if (typeId in this.cache.pastExercises) {
				return;
			}

			this.api.get(`exercises/past?type_id=${typeId}`, (response, data) => {
				if (response.code >= 400)
					return;

				this.cache.pastExercises[typeId] = data.exercises;
			});
		},

		scrollToExercisesBottom: function() {
			// Vue refs dont work for me, even with $nextTick
			// they return undefined even though it theoretically should work
			// Google is unhelpful, so I used querySelector instead of Vue
			document.querySelector(".add-workout__preview").scrollIntoView({
				behavior: "smooth",
				block: "end"
			});
		},

		uploadImage: function(event) {
			// fires on image upload @change event
			let uploader = event.path[0];
			this.$set(this.current.workout, 'images', {});
			if (!(uploader.files && uploader.files[0])) {
				return console.log('uploader - exiting early');
			}

			let filename = uploader.files[0].name;

			var reader = new FileReader();
			reader.addEventListener("load", (e) => {
				let base64 = e.target.result;
				this.$set(this.current.workout.images, filename, base64);
				this.snackbar(200, 'Załadowano obrazek.');
			});

			reader.readAsDataURL(uploader.files[0]);
		},

		addWorkout: function(workout) {
			if (this.editedWorkoutId == null) {
				this.current.workout.workout.duration = Math.floor(moment().diff(this.current.workout.startMoment) / 1000);
			}

			this.blockSubmit = true;
			this.snackbar(200, "Zapisywanie treningu...");
			this.progress = 0;
			this.api.post('workouts', workout, (response, data) => {
				// leaving logging in, chrome devtools dont allow me to check past requests response [wtf]
				console.log(response);
				this.blockSubmit = false;
				this.progress = 0;

				if (response.code >= 400) {
					this.snackbar(response.code, 'Nie udało się dodać treningu.');
				} else {
					window.onbeforeunload = null;
					this.snackbar(response.code, 'Udało się dodać trening.');
					this.redirect('workout/'+data.workout_id);
				}

			});
		},

		snackbar: function(code, message) {
			let prefix = code >= 400 ? "[!]" : "[*]";
			console.log(prefix + ' ' + message);
			this.$refs.snackbar.innerHTML = message;
			this.$refs.snackbar.classList.add('show');
			setTimeout(() => {
				this.$refs.snackbar.classList.remove('show');
			}, 3000);
		},

		redirect: function(page) {
			//return console.log('preventing redirect');
			window.location.href = PATH_PREFIX + '/' + page;
		}
	}
});