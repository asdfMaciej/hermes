Vue.component('newsfeed-item', {
	props: {
		workout: undefined
	},
	data: function() {return {
		showMenu: false,
		toggledMenu: false,
		deleted: false,
		userId: USER_ID
	}},
	template: '#newsfeed-item-template',
	methods: {
		time: function(t) {
			return moment(t).fromNow();
		},

		duration: function (seconds) {
			return showAsDuration(seconds);
		},

		easteregg: function() {
			alert("Idź na trening >:(");
			this.showMenu = false;
		},

		onDelete: function() {
			if (!confirm("Na pewno chcesz nieodwracalnie usunąć trening?"))
				return null;

			this.$root.api.delete("workouts/"+this.workout.workout_id, (response, data) => {
				if (response.code >= 400)
					return;

				this.deleted = true;
			});
		},

		onEdit: function() {
			window.location.href = `${PATH_PREFIX}/workout/${this.workout.workout_id}/edit`;
		},

		clickWorkout: function(event) {
			if (this.showMenu && !this.toggledMenu) {
				this.showMenu = false;
				event.stopPropagation();
            	event.preventDefault();
			}
			this.toggledMenu = false;
		},

		reacted: function() {
			this.$emit('reacted');
		},
		
		unreacted: function() {
			this.$emit('unreacted');
		}
	}
});


var t = new Vue({
	el: "#newsfeed",
	data: {
		api: null,
		workouts: [],
		loading: true,
		loadedAll: false,
		newsfeedPrefix: NEWSFEED_PREFIX
	},

	methods: {
		handleScroll: function(e) {
			let element = document.querySelector("#newsfeed");
			let rect = element.getBoundingClientRect();
			let loadMore = rect.bottom - window.innerHeight <= 1500;

			if (loadMore && !this.loading) {
				this.loadNextItems();
			}

		},

		unreacted: function(workout){
			for (let n in this.workouts) {
				if (this.workouts[n].workout_id == workout.workout_id) {
					this.workouts[n].reacted = 0;
					this.workouts[n].reactions = parseInt(this.workouts[n].reactions) - 1;
					let reactions_users = this.workouts[n].reactions_users.filter((v) => {v.user_id != USER_ID});
					this.$set(this.workouts[n], 'reactions_users', reactions_users);
					break;
				} 
			}
		},

		reacted: function(workout) {
			for (let n in this.workouts) {
				if (this.workouts[n].workout_id == workout.workout_id) {
					this.workouts[n].reacted = 1;
					this.workouts[n].reactions = parseInt(this.workouts[n].reactions) + 1;
					let reactions_users = this.workouts[n].reactions_users.concat({
						avatar: USER_AVATAR, user_id: USER_ID, workout_id: workout.workout_id});
					this.$set(this.workouts[n], 'reactions_users', reactions_users);
					break;
				} 
			}
		},

		loadNextItems: function() {
			if (this.workouts.length == 0)
				return;

			if (this.loadedAll)
				return;

			this.loading = true;
			let lastId = this.workouts[this.workouts.length-1].workout_id;

			this.api.get(`${this.newsfeedPrefix}newsfeed?before=${lastId}`, (response, data) => {
				if (response.code >= 400)
					return;

				this.loading = false;
				this.workouts = this.workouts.concat(data.workouts);

				if (data.workouts.length == 0) {
					this.loadedAll = true;
				}
			});
		}
	},

	mounted: function() {
		this.api = new API();
		this.api.get(`${this.newsfeedPrefix}newsfeed`, (response, data) => {
			if (response.code >= 400)
				return;

			this.loading = false;
			this.workouts = data.workouts;
		});
	},

	created() {
		window.addEventListener('scroll', this.handleScroll);
	},

	destroyed() {
		window.removeEventListener('scroll', this.handleScroll);
	}
});