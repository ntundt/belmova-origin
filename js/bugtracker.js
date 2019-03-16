function getGET(name){
	if (name = (new RegExp('[?&]' + encodeURIComponent(name) + '=([^&]*)')).exec(location.search)) {
		return decodeURIComponent(name[1]);
	}
}

function drawBugtrackerMainPage() {
	SendRequest("post", API_URL + "bugtracker.getFeed", "sid=" + getCookie("sid"), handleFeed);
}

function goBugtrackerMainPage() {
	window.open(URL + "/bugtracker", "_self");
}

function handlePost(response) {
	var content = document.getElementById("content");
	content.classList.toggle("no-padding", true);
	var response = JSON.parse(response.response).response;
	var new_html = "";

	document.title = response.title + " | Баг-трекер";

	new_html += "<div class=\"paper-head\">" + response.title + "<span class=\"right-hand-side gray\">" + getStatus(response.status) + "</span></div>";
	new_html += 
		"<div class=\"p12\">" + 
		"<span class=\"gray block\">" + l("bt_replay_steps") + ":</span>" + 
		"<div class=\"m12b\">" + response.description + "</div>" +
		"<span class=\"gray block\">" + l("bt_fact_result") + ":</span>" + 
		"<div class=\"m12b\">" + response.fact_result + "</div>" +
		"<span class=\"gray block\">" + l("bt_needed_result") + ":</span>" +
		"<div>" + response.needed_result + "</div>" +
		"</div>";
	new_html += 
		"<div class=\"paper-foot\">" + l("publication_time") + ": " +
		response.date +
		"</div>"

	content.innerHTML = new_html;

	var comments_block = document.getElementById("comments");
	var comments_count_notifier = document.getElementById("commentsCount");
	var comments_html = "";
	var comments_count = 0;
	if (response.comments !== undefined) {
		comments_count = response.comments.length;
	}
	if (comments_count > 0) {
		for (var i = 0; i < comments_count; i++) {
			comments_html += "<div class=\"comment\"><div class=\"comment-profile-photo-container\"><img class=\"profile-photo\" src=\"" + response.comments[i].from_profile_picture + "\"></div>";
			comments_html += "<div class=\"comment-content\"><a href=\"/user" + response.comments[i].from_id + "\" class=\"comment-user-link\">" + response.comments[i].from_name + "</a>" + response.comments[i].text + (response.comments[i].new_status !== undefined?'<span class="report-new-status">' + l("bt_report_new_status") + ' — ' + getStatus(response.comments[i].new_status) + '</span>':'') + "</div></div>";
		}
		comments_block.innerHTML = comments_html;
		comments_count_notifier.innerText = comments_count;
	}
}

function getPost(post_id) {
	var id = post_id;
	SendRequest("post", API_URL + "bugtracker.getReport", 
		"sid=" + getCookie("sid") + 
		"&post_id=" + id.id, 
		handlePost
	);
}

function goToPost(elem) {
	window.open(URL + "bugtracker?act=view&post=" + elem.id.replace( /^\D+/g, ''), "_self")
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
	SendRequest("post", API_URL + "bugtracker.sendReport", 
		"sid=" + getCookie("sid") + 
		"&description=" + description + 
		"&title=" + title + 
		"&fact_result=" + fact_result + 
		"&needed_result=" + needed_result, 
		goToMainPage
	);
}

function getStatus(status) {
	return l("bt_status_" + status);
}

function handleFeed(response) {
	var content = document.getElementById("content");
	var HTMLContent = '';
	var response = JSON.parse(response.response).response;
	for (i = 0; i < response.length; i++) {
		HTMLContent += "<div id=\"post" + response[i].post_id + "\" onclick=\"goToPost(this)\" class=\"post" + (i == response.length - 1 ? " no-border-bottom" : "") + "\"><div class=\"post-title\">" + response[i].title + "</div>";
		HTMLContent += "<div class=\"post-content\">" + response[i].description + "</div>";
		HTMLContent += "<div class=\"bottom\"><a class=\"user-link\" href=\"/user" + response[i].from_id + "\">" + response[i].from_name + "</a> " + response[i].date + " <span class=\"right-hand-side dark-text\">" + getStatus(response[i].status) + "</span></div></div>";
	}
	content.innerHTML = HTMLContent;
	window.onbeforeunload = undefined;
}

function addNewComment(comment) {
	console.log(comment);
	var comments_block = document.getElementById("comments");
	comments_block.innerHTML += "<div class=\"comment\"><div class=\"comment-profile-photo-container\"><img class=\"profile-photo\" src=\"" + comment.from_profile_picture + "\"></div>"
		+ "<div class=\"comment-content\"><a href=\"/user" + comment.from_id + "\" class=\"comment-user-link\">" + comment.from_name + "</a>" + comment.text + (comment.new_status !== undefined?'<span class="report-new-status">' + l("bt_report_new_status") + ' — ' + getStatus(comment.new_status) + '</span>':'') + "</div></div>";
}

function commentSend() {
	var comment_text_input = document.getElementById("comment-text-input");
	var request = new APIRequest(getCookie("sid"));
	request.setMethod("bugtracker.addComment");
	request.addParameter("text", comment_text_input.value);
	request.addParameter("reply_to", getGET("post"));
	if (reportNewStatusSelector.getSelected() != null && reportNewStatusSelector.getSelected() != "do_not_change") request.addParameter("new_status", reportNewStatusSelector.getSelected());
	request.perform(function(r) {
		addNewComment(JSON.parse(r.response).response);
		comment_text_input.value = "";
		reportNewStatusSelector.reset();
	});
}
