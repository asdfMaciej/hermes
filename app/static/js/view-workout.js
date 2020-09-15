var t = new Vue({
	el: "#view-workout",
	data: {
		api: null,
		showMenu: false,
		userId: USER_ID
	},

	methods: {
		onDelete: function() {
			if (!confirm("Na pewno chcesz nieodwracalnie usunąć trening?"))
				return null;

			this.$root.api.delete("workouts/"+WORKOUT_ID, (response, data) => {
				if (response.code >= 400)
					return;

				document.location = `${PATH_PREFIX}/profile/${USER_ID}`;
			});
		},
	},

	mounted: function() {
		this.api = new API();
	}
});