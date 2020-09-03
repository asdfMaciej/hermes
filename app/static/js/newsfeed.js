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
			// moment.js doesn't support this
			let minutes = Math.floor(parseInt(seconds) / 60);
			let hours = Math.floor(minutes / 60);
			minutes = minutes % 60;

			let duration = '', postfix = '';
			if (hours) {
				postfix = hours == 1 ? 'godzina' : (hours < 5 ? 'godziny' : 'godzin');
				duration = `${hours} ${postfix} `;
			}

			if (minutes) {
				duration += minutes == 1 ? `${minutes} minuta` : (minutes < 5 ? `${minutes} minuty` : `${minutes} minut`);
			}

			return duration;

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