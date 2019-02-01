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
		document.cookie = "sid=" + response.response.sid;
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

window.addEventListener('keydown', function(e) {
	if (e.keyCode == 13) {
		handleButtonClick();
	}
})