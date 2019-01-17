function goToMainPage() {
	document.getElementById("bugtracker-link").click();
}

function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

function sendReport() {
	var title = document.getElementById("title").value;
	var description = document.getElementById("description").value;
	var fact_result = document.getElementById("fact_result").value;
	var needed_result = document.getElementById("needed_result").value;
	SendRequest("post", "http://localhost/work/method/bugtracker.sendReport", 
		"sid=" + getCookie("sid") + 
		"&description=" + description + 
		"&title=" + title + 
		"&fact_result=" + fact_result + 
		"&needed_result=" + needed_result, 
		goToMainPage
	);
}

function getStatus(status) {
	statuses = {
		"not_seen": "не просмотрен", 
		"in_process": "в процессе", 
		"closed": "закрыт", 
		"waiting": "ожидает", 
		"fixed": "исправлен"
	};
	return statuses[status];
}

function handleFeed(response) {
	var content = document.getElementById("content");
	var HTMLContent = '';
	var response = JSON.parse(response.response).response;
	for (i = 0; i < response.length; i++) {
		HTMLContent += "<div class=\"post" + (i == response.length - 1 ? " no-border-bottom" : "") + "\"><div class=\"post-title\">" + response[i].title + "</div>";
		HTMLContent += "<div class=\"post-content\">" + response[i].description + "</div>";
		HTMLContent += "<div class=\"bottom\"><a class=\"user-link\" href=\"/user" + response[i].from_id + "\">" + response[i].from_name + "</a> " + response[i].date + " <span class=\"right-hand-side dark-text\">" + getStatus(response[i].status) + "</span></div></div>";
	}
	content.innerHTML = HTMLContent;
}
