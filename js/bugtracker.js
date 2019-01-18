function handlePost(response) {
	var content = document.getElementById("content");
	content.classList.toggle("no-padding", true);
	var response = JSON.parse(response.response).response;
	var new_html = "";

	new_html += "<div class=\"paper-head\">" + response.title + "<span class=\"right-hand-side gray\">" + getStatus(response.status) + "</span></div>";
	new_html += 
		"<div class=\"p12\">" + 
		"<span class=\"gray block\">Описание проблемы:</span>" + 
		response.description + 
		"<span class=\"gray block\">Фактический результат:</span>" + 
		response.fact_result +
		"<span class=\"gray block\">Ожидаемый результат:</span>" +
		response.needed_result + 
		"</div>";
	new_html += 
		"<div class=\"paper-foot\">Время публикации: " +
		response.date +
		"</div>"

	content.innerHTML = new_html;
}

function getPost(elem) {
	var id = elem.id;
	SendRequest("post", "http://localhost/work/method/bugtracker.getReport", 
		"sid=" + getCookie("sid") + 
		"&post_id=" + id.replace( /^\D+/g, ''), handlePost);
}

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
	HTMLContent += "<div class=\"paper-head\">Все отчёты</div>";
	for (i = 0; i < response.length; i++) {
		HTMLContent += "<div id=\"post" + response[i].post_id + "\" onclick=\"getPost(this)\" class=\"post" + (i == response.length - 1 ? " no-border-bottom" : "") + "\"><div class=\"post-title\">" + response[i].title + "</div>";
		HTMLContent += "<div class=\"post-content\">" + response[i].description + "</div>";
		HTMLContent += "<div class=\"bottom\"><a class=\"user-link\" href=\"/user" + response[i].from_id + "\">" + response[i].from_name + "</a> " + response[i].date + " <span class=\"right-hand-side dark-text\">" + getStatus(response[i].status) + "</span></div></div>";
	}
	content.innerHTML = HTMLContent;
}
