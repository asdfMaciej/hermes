function copy(o) {
	return JSON.parse(JSON.stringify(o));
}

Vue.component('exercise-category', {
	props: {
		category: undefined
	},
	template: '#exercise-category-template',
	data: function() {return {
		show: true
	}}
});

Vue.component('exercise', {
	props: {
		value: undefined,
		isFirst: Boolean,
		editOnly: Boolean,
		viewOnly: Boolean,
		hideTitle: Boolean
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
		},

		addRep: function() {
			let exercise = clone(this.exercise);
			delete exercise.reps;
			delete exercise.weight;
			delete exercise.duration;
			this.$root.addExercise(exercise);
			this.$nextTick(this.$root.scrollToExercisesBottom);
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
	el: "#add-workout",
	data: {
		cache: {
			exerciseCategories: [],
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
					title: "Popołudniowy trening"
				},
				exercises: [],
				startMoment: null
			}
		},
		timeElapsed: '00:00',
		api: null,
		editTitle: false,
		showAddExercise: false,
		showModal: false,
	},

	mounted: function() {
		this.api = new API();
		this.api.get('exercise_categories', (response, data) => {
			this.cache.exerciseCategories = data.exercise_categories;
		});
		this.api.get('exercise_types', (response, data) => {
			this.cache.exerciseTypes = data.exercise_types;
		});

		this.api.get('gyms', (response, data) => {
			this.cache.gyms = data.gyms;
		});
		this.current.workout.startMoment = moment();
		setInterval(() => {
			this.$data.timeElapsed = moment(
				moment() - this.current.workout.startMoment
			).format('mm:ss');
		}, 1000);
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
		}
	},

	methods: {
		submit: function() {
			if (this.validateWorkoutErrors.length > 0) {
				this.showModal = true;
				setTimeout(() => {
					this.showModal = false;
				}, 5000);

				this.snackbar(400, this.validateWorkoutErrors.join(' '));
			} else {
				this.addWorkout(this.current.workout);
			}
		},

		openTitleEdition: function() {
			this.editTitle = true;
			this.$nextTick(() => {
				this.$refs.edittitle.focus();
			});
		},

		addExercise: function(exercise) {
			console.log(exercise);
			this.current.workout.exercises.push(copy(exercise));
		},

		isEmptyObject: function(obj) {
			return Object.keys(obj).length === 0; 
		},

		selectExerciseType: function(exerciseType) {
			this.showAddExercise = false;
			this.addExercise(exerciseType);
			this.$nextTick(this.scrollToExercisesBottom);
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
			this.$refs.snackbar.innerHTML = message;
			this.$refs.snackbar.classList.add('show');
			setTimeout(() => {
				this.$refs.snackbar.classList.remove('show');
			}, 3000);
		},

		redirect: function(page) {
			window.location.href = PATH_PREFIX + '/' + page;
		}
	}
});