var request = new APIRequest(getCookie("sid"));

function onSendButtonClick() {
	var method = document.getElementById("apiMethod").value;
	var parameters = document.getElementById("apiRequestParameters").value;
	request.setMethod(method);
	request.parametersString = parameters;
	request.addParameter("v", "5.92");
	request.perform(function(response) {
		showResponse(JSON.parse(response.response));
	});
}

function showResponse(response) {
	var wrapper = document.getElementById("response-container");
	wrapper.setAttribute("style", "");
	wrapper.innerHTML = "";
	var tree = jsonTree.create(response, wrapper);
}