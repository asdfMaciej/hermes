Vue.component("profile-list", {
	props: {
		users: undefined
	},
	methods: {
		time: function(t) {
			return moment(t).fromNow();
		},

		duration: function (seconds) {
			return showAsDuration(seconds);
		}
	},
	template: "#profile-list-template"
});
