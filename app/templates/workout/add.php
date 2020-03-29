<form action="``PATH_PREFIX``/workout/add" method="post">
	<input type="hidden" name="action" value="add">
	<input type="text" name="name" placeholder="Nazwa"><br>
	<input type="number" name="reps" placeholder="Ilość powtórzeń"><br>
	<input type="number" name="weight" placeholder="Waga na powtórzeniu"><br>
	<input type="hidden" name="type_id" value="1">
	<input type="hidden" name="gym_id" value="1">
	<input type="hidden" name="failure" value="0">
	<input type="submit" value="Dodaj trening">
</form>


<div id="app">
	<pre>{{dataSent}}</pre>
	<button @click="testApi">Dodaj workout</button>
	<pre>{{response}}</pre>
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

var t = new Vue({
	el: "#app",
	data: {
		dataSent: {},
		response: {}
	},
	methods: {
		testApi: function() {
			axios.get(this.getPath('workout/add/api'))
				.then((response) => {
					let r = new APIResponse(response);
					r.preview();
				});
			console.log('hey');
		},

		getPath: function(method) {
			return PATH_PREFIX + '/' + method;
		}
	}
});
</script>