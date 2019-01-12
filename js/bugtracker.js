function handleResponse(response) {
	var content = document.getElementById("content");
	var HTMLContent = '';
	var response = JSON.parse(response.response).response;
	for (i = 0; i < response.length; i++) {
		HTMLContent += "<div class=\"post\"><div class=\"post-title\">" + response[i].title + "</div>";
		HTMLContent += "<div class=\"bottom\"><a class=\"user-link\" href=\"/user" + response[i].from_id + "\">" + response[i].from_name + "</a></div></div>";
		HTMLContent += "<hr>";
	} 
	content.innerHTML = HTMLContent;
}

SendRequest("post", "http://localhost/work/method/bugtracker.getFeed", "sid=example", handleResponse);