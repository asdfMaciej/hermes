Vue.component('newsfeed-item', {
	props: {
		workout: undefined
	},
	template: '#newsfeed-item-template',
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