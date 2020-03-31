<div id="app">
	<button @click="testApi">Dodaj workout</button>
</div>

<script>
class APIResponse {
	constructor(axiosResponse) {
		this.data = axiosResponse.data;
		this.code = axiosResponse.status;
		this.codeText = axiosResponse.statusText;
		this.url = axiosResponse.config.url;
		this.method = axiosResponse.config.method;
	}

	preview(print=true) {
		let message = `${this.code} (${this.codeText}) returned for ${this.url}`;
		message += ` [${this.method}]`;
		message += "\n" + JSON.stringify(this.data);
		message += "\n---\n";
		if (print)
			console.log(message);

		return message;
	}
}

class API {
	getPath(method) {
		return PATH_PREFIX + '/api/' + method;
	}

	post(path, data, onResponse) {
		this._request(
			axios.post(this.getPath(path), data),
			onResponse
		)
	}

	get(path, onResponse) {
		this._request(
			axios.get(this.getPath(path)),
			onResponse
		)
	}

	_request(request, onResponse) {
		request.then((response) => {
			let r = new APIResponse(response);
			if (DEBUG)
				r.preview();

			onResponse(r, r.data);
		})
		.catch((error) => {
			let r = new APIResponse(error.response);
			if (DEBUG)
				r.preview();

			onResponse(r, r.data);
		});
	}
}

var t = new Vue({
	el: "#app",
	data: {
		cache: {
			exerciseTypes: []
		},
		selected: {
			exerciseType: {}
		},
		current: {
			workout: {
				workout: {},
				exercises: []
			}
		},
		exerciseTypes: [],
		api: null

	},

	mounted: function() {
		this.api = new API();
		this.api.get('exercise_types', (response, data) => {
			this.cache.exerciseTypes = data.exercise_types;
		});
	},

	methods: {
		testApi: function() {
			workout = {
				workout: {
					gym_id: 1,
					user_id: 16,
					name: "Trening dodany z frontendu!"
				},

				exercises: [
					{
						type_id: 1,
						reps: 5,
						weight: 80
					},
					{
						type_id: 1,
						reps: 5,
						weight: 85
					},
					{
						type_id: 1,
						reps: 4,
						weight: 100,
						failure: 1
					},
					{
						type_id: 2,
						reps: 8,
						weight: 12
					}
				]
			}

			this.addWorkout(workout)
		},

		addWorkout: function(workout) {
			this.api.post('workouts', workout, (response, data) => {
				if (response.code >= 400) {
					this.snackbar(response.code, 'Nie udało się dodać treningu.');
				} else {
					this.snackbar(response.code, 'Udało się dodać trening.');
					this.redirect('workout/'+data.workout_id);
				}
			});
		},

		snackbar: function(code, message) {
			let prefix = code >= 400 ? "[!]" : "[*]";
			console.log(prefix + ' ' + message);
		},

		redirect: function(page) {
			window.location.href = PATH_PREFIX + '/' + page;
		}
	}
});
</script>