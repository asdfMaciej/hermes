Vue.component("reaction-button", {
	props: {
		workout: undefined
	},
	template: "#reaction-button-template",
	data: function() {return {
		reacted: null,
		reactions: null,
		api: null
	}},

	watch: {
		workout: function(current, old) {
			this.reacted = current.reacted;
			this.reactions = current.reactions;
		}
	},

	created: function() {
		this.reacted = this.workout.reacted;
		this.reactions = this.workout.reactions;
		this.api = new API();
	},

	methods: {
		react: function() {
			let data = {workout_id: this.workout.workout_id};
			if (this.reacted == 1) {
				data.action = 'unreact';
				this.api.post('reactions', data, (response, data) => {
					if (response.code < 300) {
						this.reacted = 0;
						this.reactions = parseInt(this.reactions) - 1;
					}
				});
			} else {
				data.action = 'react';
				this.api.post('reactions', data, (response, data) => {
					if (response.code < 300) {
						this.reacted = 1;
						this.reactions = parseInt(this.workout.reactions) + 1;
					}
				});
			}
		}
	}
});

// todo: Vue components including and reusing in the framework