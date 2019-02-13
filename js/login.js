function getGETFromText(name, text) {
	if (name = (new RegExp('[?#&]' + encodeURIComponent(name) + '=([^&]*)')).exec(text)) {
		return decodeURIComponent(name[1]);
	}
}

function responseHandler(response) {
	var identificator = document.getElementById("identificator");
	var password = document.getElementById("password");
	var linkToMainPage = document.getElementById("linkToMainPage");
	var incorrectIdentificatorNotification = document.getElementById("identificator-incorrect");
	var incorrectPasswordNotification = document.getElementById("password-incorrect");
	incorrectIdentificatorNotification.hidden = true;
	incorrectPasswordNotification.hidden = true;
	identificator.classList.toggle("red-border", false);
	password.classList.toggle("red-border", false);
	response = JSON.parse(response.response);
	if (response.error !== undefined) {
		switch (response.error.error_code) {
		case 101:
			incorrectIdentificatorNotification.hidden = false;
			identificator.classList.toggle("red-border", true);
			break;
		case 102:
			incorrectPasswordNotification.hidden = false;
			password.classList.toggle("red-border", true);
			break;
		}
	} else {
		var date = new Date;
		date.setDate(date.getDate() + 30);
		document.cookie = "sid=" + response.response.sid + "; expires=" + date.toUTCString();
		linkToMainPage.click();
	}
}

function handleButtonClick() {
	var identificator = document.getElementById("identificator");
	var password = document.getElementById("password");
	if (identificator.value != "" && password.value != "") {
		SendRequest("post", "http://localhost/work/method/auth.login", "login=" + identificator.value + "&password=" + password.value, responseHandler);
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
	window.open("https://oauth.vk.com/authorize?client_id=6843764&display=page&redirect_uri=blank.html&scope=5312512&response_type=token&v=5.92");
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
	window.open("http://localhost/", "_self");
}

function tokenInputProcess(elem) {
	var uri = document.getElementById("uriInput");

	SendRequest(
		"get", 
		"http://localhost/oauth", 
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
