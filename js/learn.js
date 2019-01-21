function goToLesson(elem) {
	window.open("http://localhost/learn?act=lesson&lid=" + elem.getAttribute("partition") + "-" + elem.getAttribute("topic") + "-" + elem.getAttribute("topiclevel") + "-" + elem.getAttribute("lessonnumber"), "_self");
}
function getGET(name) {
   if (name = (new RegExp('[?&]' + encodeURIComponent(name) + '=([^&]*)')).exec(location.search)) {
      return decodeURIComponent(name[1]);
   }
}
function handleLessonsList(response) {
	response = JSON.parse(response.response).response;
	console.log(response);
	var content = document.getElementById("content");

	var html = "";

	for (i = 0; i < response.partitions.length; i++) {
		html += "<div class=\"partition\">" + response.partitions[i].partition_name + "</div>";
		for (j = 0; j < response.partitions[i].topics.length; j++) {
			current_topic = response.partitions[i].topics[j];
			crowns = (current_topic.topic_level === undefined ? 0 : current_topic.topic_level);
			html += "<div class=\"topic piece-of-paper m12b\" partition=\"" + response.partitions[i].partition_id + "\" topic=\"" + current_topic.topic_id + "\" topiclevel=\"" + current_topic.topic_level + "\" lessonnumber=\"" + current_topic.lessons_count + "\"onclick=\"goToLesson(this)\">" + 
				"<div class=\"topic-title-container\">" +
				current_topic.topic_name + 
				" <span class=\"light-gray\">" + crowns + "</span>" +
				"<span class=\"right-hand-side\">" +
				(current_topic.topic_passed?current_topic.lessons_total_count:current_topic.lessons_count) + "/" + current_topic.lessons_total_count +
				"</span>" +
				"</div>" +
				"<div class=\"progress-bar-container\"><div class=\"progress-bar\"></div></div>" +
				"</div>";
		}
	}
	content.innerHTML = html;
}
function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}
function init() {
	SendRequest("post", "http://localhost/work/method/user.getLessonsList", "sid=" + getCookie("sid"), handleLessonsList);
}
function goMainPage() {
	window.open("http://localhost/", "_self");
}