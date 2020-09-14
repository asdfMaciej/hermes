Vue.component('newsfeed-item', {
	props: {
		workout: undefined
	},
	template: '#newsfeed-item-template',
	methods: {
		time: function(t) {
			return moment(t).fromNow();
		},

		duration: function (seconds) {
			return showAsDuration(seconds);
		}
	}
});

var t = new Vue({
	el: "#newsfeed",
	data: {
		api: null
	},

	mounted: function() {
		this.api = new API();
	}
});