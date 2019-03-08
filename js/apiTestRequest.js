var request = new APIRequest(getCookie("sid"));

function onSendButtonClick() {
	var method = document.getElementById("apiMethod").value;
	var parameters = document.getElementById("apiRequestParameters").innerText;
	//var request = new APIRequest(getCookie("sid"));
	request.setMethod(method);
	request.parametersString = parameters;
	request.addParameter("v", "5.92");
	request.perform(function(response) {
		request.standardCallback(response);
	});
}