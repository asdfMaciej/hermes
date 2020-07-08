var t = new Vue({
	el: "#view-workout",
	data: {
		api: null
	},

	mounted: function() {
		this.api = new API();
	}
});