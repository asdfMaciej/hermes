Vue.component("reaction-button", {
	props: {
		workout: undefined
	},
	template: "#reaction-button-template",
	data: function() {return {
		api: null
	}},

	created: function() {
		this.api = new API();
	},

	methods: {
		react: function() {
			let data = {workout_id: this.workout.workout_id};
			if (this.workout.reacted == 1) {
				data.action = 'unreact';
				this.api.post('reactions', data, (response, data) => {
					if (response.code < 300) {
						this.$emit('unreacted');
					}
				});
			} else {
				data.action = 'react';
				this.api.post('reactions', data, (response, data) => {
					if (response.code < 300) {
						this.$emit('reacted');
					}
				});
			}
		}
	}
});

// todo: Vue components including and reusing in the framework