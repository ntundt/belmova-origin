class APIRequest {
	APIRequest(sid) {
		this.sid = sid;
	}
	setMethod(method) {
		this.method = method;
	}
	perform() {
		SendRequest("post", URL + "/")
	}
}