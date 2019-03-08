class APIRequest {
	constructor(sid) {
		this.sid = sid;
		this.parameters = {sid: sid};
		this.parametersString = "";
		this.error = {};
	}
	setMethod(method) {
		this.method = method;
	}
	addParameter(key, value) {
		this.parameters[key] = value;
	}
	onSuccess(response) {
		console.log(response);
		alert(l("api_request_successfully_performed", [JSON.stringify(response)]));
	}
	onError(error) {
		alert(l("error_during_api_request", [error.code, error.description]));
	}
	buildQuery(object, numberPrefix, temporaryKey) {
		var output_string = [];
		Object.keys(object).forEach(function(val) {
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
	standardCallback(response) {
		this.fullResponseString = response.response;
		this.fullResponse = JSON.parse(this.fullResponseString);
		if (this.fullResponse.error !== undefined) {
			this.error.code = this.fullResponse.error_code;
			this.error.description = this.fullResponse.error_description;
			this.onError(this.error);
		}
		if (this.fullResponse.response !== undefined) {
			this.response = this.fullResponse.response;
			this.onSuccess(this.response);
		}
	}
	perform(callback) {
		this.parametersString += (this.parameters.length > 0) ? this.buildQuery(this.parameters) : "";
		SendRequest("post", API_URL + this.method, this.parametersString, (callback !== undefined) ? callback : this.standardCallback);
	}
}
