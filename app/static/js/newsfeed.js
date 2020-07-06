function copy(o) {
	return JSON.parse(JSON.stringify(o));
}

Vue.component('newsfeed-item', {
	props: {
		workout: undefined
	},
	template: '#newsfeed-item-template',
	data: function() {return {
	}},

	methods: {
		react: function() {}
	},

	computed: {
	}
});

var t = new Vue({
	el: "#newsfeed",
	data: {
		cache: {
		},
		selected: {
		},
		current: {

		},
		api: null
	},

	mounted: function() {
		this.api = new API();
	},

	computed: {
		validateWorkoutErrors: function() {
		}
	},

	methods: {
		submit: function() {
			
		}
	}
});