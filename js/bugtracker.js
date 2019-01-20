function getGET(name){
   if (name = (new RegExp('[?&]' + encodeURIComponent(name) + '=([^&]*)')).exec(location.search)) {
      return decodeURIComponent(name[1]);
   }
}

function drawBugtrackerMainPage() {
	SendRequest("post", "http://localhost/work/method/bugtracker.getFeed", "sid=" + getCookie("sid"), handleFeed);
}

function goBugtrackerMainPage() {
	window.open("http://localhost/bugtracker", "_self");
}

function handlePost(response) {
	var content = document.getElementById("content");
	content.classList.toggle("no-padding", true);
	var response = JSON.parse(response.response).response;
	var new_html = "";

	new_html += "<div class=\"paper-head\">" + response.title + "<span class=\"right-hand-side gray\">" + getStatus(response.status) + "</span></div>";
	new_html += 
		"<div class=\"p12\">" + 
		"<span class=\"gray block m12b\">Описание проблемы:</span>" + 
		"<div class=\"m12b\">" + response.description + "</div>" +
		"<span class=\"gray block m12b\">Фактический результат:</span>" + 
		"<div class=\"m12b\">" + response.fact_result + "</div>" +
		"<span class=\"gray block m12b\">Ожидаемый результат:</span>" +
		"<div class=\"m12b\">" + response.needed_result + "</div>" +
		"</div>";
	new_html += 
		"<div class=\"paper-foot\">Время публикации: " +
		response.date +
		"</div>"

	content.innerHTML = new_html;
}

function getPost(post_id) {
	var id = post_id;
	SendRequest("post", "http://localhost/work/method/bugtracker.getReport", 
		"sid=" + getCookie("sid") + 
		"&post_id=" + id.id, 
		handlePost
	);
}

function goToPost(elem) {
	window.open("http://localhost/bugtracker?act=view&post=" + elem.id.replace( /^\D+/g, ''), "_self")
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
	HTMLContent += "<div class=\"paper-head\">Все отчёты<a href=\"/bugtracker?act=add\" class=\"button-red right-hand-side\">Отправить</a></div>";
	for (i = 0; i < response.length; i++) {
		HTMLContent += "<div id=\"post" + response[i].post_id + "\" onclick=\"goToPost(this)\" class=\"post" + (i == response.length - 1 ? " no-border-bottom" : "") + "\"><div class=\"post-title\">" + response[i].title + "</div>";
		HTMLContent += "<div class=\"post-content\">" + response[i].description + "</div>";
		HTMLContent += "<div class=\"bottom\"><a class=\"user-link\" href=\"/user" + response[i].from_id + "\">" + response[i].from_name + "</a> " + response[i].date + " <span class=\"right-hand-side dark-text\">" + getStatus(response[i].status) + "</span></div></div>";
	}
	content.innerHTML = HTMLContent;
	window.onbeforeunload = undefined;
}
