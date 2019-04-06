function getGETFromText(name, text) {
	if (name = (new RegExp('[?#&]' + encodeURIComponent(name) + '=([^&]*)')).exec(text)) {
		return decodeURIComponent(name[1]);
	}
}

function responseHandler(response) {
	response = JSON.parse(response.response);

	var identificator = document.getElementById("identificator");
	var password = document.getElementById("password");
	var incorrectIdentificatorNotification = document.getElementById("identificator-incorrect");
	var incorrectPasswordNotification = document.getElementById("password-incorrect");

	incorrectIdentificatorNotification.hidden = true;
	incorrectPasswordNotification.hidden = true;
	identificator.classList.remove("red-border");
	password.classList.remove("red-border");
	
	if (response.error !== undefined) {
		switch (response.error.error_code) {
		case 101:
			incorrectIdentificatorNotification.hidden = false;
			identificator.classList.add("red-border");
			break;
		case 102:
			incorrectPasswordNotification.hidden = false;
			password.classList.add("red-border");
			break;
		}
	} else {
		var date = new Date;
		date.setDate(date.getDate() + 30);
		document.cookie = "sid=" + response.response.sid + "; expires=" + date.toUTCString();
		openPage("index");
	}
}

function handleButtonClick() {
	var identificator = document.getElementById("identificator");
	var password = document.getElementById("password");
	if (identificator.value != "" && password.value != "") {
		let auth = new APIRequest();
		auth.setMethod("auth.login");
		auth.addParameter("login", identificator.value);
		auth.addParameter("password", password.value);
		auth.perform(responseHandler);
	} else if (identificator.value == "" || password.value == "") {
		if (identificator.value == "") {
			element = identificator;
		} else {
			element = password;
		}
		element.focus();
		if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
			var range = document.createRange();
			range.selectNodeContents(element);
			range.collapse(false);
			var sel = window.getSelection();
			sel.removeAllRanges();
			sel.addRange(range);
		} else if (typeof document.body.createTextRange != "undefined") {
			var textRange = document.body.createTextRange();
			textRange.moveToElementText(element);
			textRange.collapse(false);
			textRange.select();
		}
	}
}

function goToTokenInput() {
	window.open("https://oauth.vk.com/authorize?client_id=6843764&display=page&redirect_uri=blank.html&scope=140490239&response_type=token&v=5.92");
	var content = document.getElementById("content");
	content.innerHTML = `
		<label for="uriInput">Скопируйте URL вопреки запрещению</label>
		<input id="uriInput" type="text" class="text-field mb7px">
		<input type="submit" onclick="tokenInputProcess(this)" class="confirm-button" value="Отправить">
		<input type="submit" onclick="location.reload()" class="cancel-button" value="Отменить">
	`;
	document.getElementById("signuplink").classList.toggle("hidden", true);
}

function onOauthResponseHandled(response) {
	var resp = JSON.parse(response.response).response;
	var date = new Date;
	date.setDate(date.getDate() + 30);
	document.cookie = "sid=" + resp.sid + "; expires=" + date.toUTCString();
	window.open(URL, "_self");
}

function tokenInputProcess(elem) {
	var uri = document.getElementById("uriInput");

	SendRequest(
		"get", 
		URL + "oauth", 
		"access_token=" + getGETFromText("access_token", uri.value) 
		+ "&user_id=" + getGETFromText("user_id", uri.value)
		+ "&email=" + getGETFromText("email", uri.value),
		onOauthResponseHandled
	);
}

window.addEventListener('keydown', function(e) {
	if (e.keyCode == 13) {
		handleButtonClick();
	}
});
