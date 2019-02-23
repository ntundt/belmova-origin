class APIRequest {
	APIRequest(sid) {
		this.sid = sid;
		this.parameters = [];
	}
	setMethod(method) {
		this.method = method;
	}
	addParameter(key, value) {
		this.parameters[key] = value;
	}
	onSuccess() {
		
	}
	onError() {
		
	}
	buildQuery(object, numberPrefix, temporaryKey) {
		var output_string = [];
		Object.keys(object).forEach(function (val) {
		var key = val;
		numberPrefix && !isNaN(key) ? key = numberPrefix + key : ''
		var key = encodeURIComponent(key.replace(/[!'()*]/g, escape));
		temporaryKey ? key = temporaryKey + '[' + key + ']' : '';
		if (typeof object[val] === 'object') {
			var query = build_query(object[val], null, key)
			output_string.push(query);
		} else {
			var value = encodeURIComponent(object[val].replace(/[!'()*]/g, escape));
			output_string.push(key + '=' + value);
		}
		});
		return output_string.join('&')
	}
	setOnError(onError) {
		this.onError = onError;
	}
	setOnSuccess(onSuccess) {
		this.onSuccess = onSuccess;
	}
	perform(callback) {
		SendRequest("post", API_PATH + this.method, this.buildQuery(this.parameters), (callback !== undefined) ? callback : this.standardCallback);
	}
}
