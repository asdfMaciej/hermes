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
		hideTitle: Boolean,
		showAddRep: Boolean,
		order: Number,
		index: Number
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
			this.$nextTick(this.$root.scrollToExercisesBottom);
		}
	},

	computed: {
		exercise: function() {
			return this.value;
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
					title: "Trening szefa"
				},
				exercises: [],
				startMoment: null
			}
		},
		timeElapsed: '00:00',
		api: null,
		editTitle: false,
		showAddExercise: false,
		blockSubmit: false,
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
				this.blockSubmitButton(3000);
				this.snackbar(400, "Uzupełnij wszystkie pola!");
				return;
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