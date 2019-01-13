function responseHandler(response) {
	var linkToMainPage = document.getElementById("linkToMainPage");
	var incorrectIdentificatorNotification = document.getElementById("identificator-incorrect");
	var incorrectPasswordNotification = document.getElementById("password-incorrect");
	incorrectIdentificatorNotification.hidden = true;
	incorrectPasswordNotification.hidden = true;
	response = JSON.parse(response.response);
	if (response.error !== undefined) {
		switch(response.error.error_code) {
		case 101:
			incorrectIdentificatorNotification.hidden = false;
			break;
		case 102:
			incorrectPasswordNotification.hidden = false;
			break;
		}
	} else {
		document.cookie = "sid="+response.response.sid;
		linkToMainPage.click();
	}
}

function handleButtonClick() {
	var identificator = document.getElementById("identificator").value;
	var password = document.getElementById("password").value;
	SendRequest("post", "http://localhost/work/method/auth.login", "login="+identificator+"&password="+password, responseHandler);
}