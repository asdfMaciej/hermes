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

		clickWorkout: function(event) {
			if (this.showMenu && !this.toggledMenu) {
				this.showMenu = false;
				event.stopPropagation();
            	event.preventDefault();
			}
			this.toggledMenu = false;
		}
	}
});

var t = new Vue({
	el: "#newsfeed",
	data: {
		api: null
	},

	methods: {
	},

	mounted: function() {
		this.api = new API();
	}
});