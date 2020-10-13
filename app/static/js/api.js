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
	constructor(showProgress) {
		this.config = {}
		if (showProgress) {
			this.config['onUploadProgress'] = showProgress;
		}
	}
	getPath(method) {
		return PATH_PREFIX + '/api/' + method;
	}

	post(path, data, onResponse) {
		this._request(
			axios.post(this.getPath(path), data, this.config),
			onResponse
		)
	}

	get(path, onResponse) {
		this._request(
			axios.get(this.getPath(path), this.config),
			onResponse
		)
	}

	delete(path, onResponse) {
		this._request(
			axios.delete(this.getPath(path), this.config),
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
			console.log(error);
			let r = new APIResponse(error.response);
			if (DEBUG)
				r.preview();

			onResponse(r, r.data);
		});
	}
}
