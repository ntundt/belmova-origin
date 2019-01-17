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
		switch(response.error.error_code) {
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
	var identificator = document.getElementById("identificator").value;
	var password = document.getElementById("password").value;
	SendRequest("post", "http://localhost/work/method/auth.login", "login=" + identificator + "&password=" + password, responseHandler);
}