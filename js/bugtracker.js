var Bugtracker = {
	drawMainPage: () => {
		var mainPage = new APIRequest(getCookie("sid"));
		mainPage.setMethod("bugtracker.getFeed");
		mainPage.perform((response) => {
			Bugtracker.handleFeed(response);
		});
	},

	goBugtrackerMainPage: () => {
		window.open(URL + "/bugtracker", "_self");
	},

	handlePost: (response) => {
		var content = document.getElementById("content");
		content.classList.toggle("no-padding", true);
		var response = JSON.parse(response.response).response;
		var new_html = "";

		document.title = response.title + " | " + l("bugtracker");

		new_html += "<div class=\"paper-head\"><a class=\"textlink\" href=\"/bugtracker\">" 
		+ l("bugtracker") + "</a> > " + response.title + "<span class=\"right-hand-side gray\">" 
		+ Bugtracker.getReportStatusText(response.status) + "</span></div>";
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
	},

	getPost: (post_id) => {
		let postRequest = new APIRequest(getCookie("sid"));
		postRequest.setMethod("bugtracker.getReport");
		postRequest.addParameter("post_id", post_id);
		postRequest.perform((response) => {
			Bugtracker.handlePost(response);
		});
	},

	goToReport: (post_id) => {
		window.open(URL + "bugtracker?act=view&post=" + post_id, "_self");
	},

	getCookie: (name) => {
	  var matches = document.cookie.match(new RegExp(
	    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	  ));
	  return matches ? decodeURIComponent(matches[1]) : undefined;
	},

	sendReport: () => {
		var request = new APIRequest(getCookie("sid"));
		request.setMethod("bugtracker.addReport");
		request.addParameter("title", document.getElementById("title").value);
		request.addParameter("description", document.getElementById("description").value);
		request.addParameter("fact_result", document.getElementById("fact_result").value);
		request.addParameter("needed_result", document.getElementById("needed_result").value);
		request.perform(function(r) {
			r = JSON.parse(r.response).response;
			openPage('bugtracker');
		});
	},

	getReportStatusText: (status) => {
		return l("bt_status_" + status);
	},

	handleFeed: (response) => {
		var content = document.getElementById("content");
		content.innerHTML = '';
		var response = JSON.parse(response.response).response;
		response.forEach((post, index, response) => {
			var post_container = document.createElement("div");
			post_container.classList.add("post");
			post_container.onclick = (function() {
				Bugtracker.goToReport(this.attributes.post_id.value);
			}).bind(post_container);
			post_container.setAttribute("post_id", post.post_id);
			if (index + 1 == response.length)
				post_container.classList.add("no-border-bottom");
			var post_title = document.createElement("div");
			post_title.classList.add("post_title");
			post_title.innerText = post.title;
			var post_bottom = document.createElement("div");
			post_bottom.classList.add("bottom");
			var post_author_link = document.createElement("a");
			post_author_link.setAttribute("href", "/user" + post.from_id);
			post_author_link.innerText = post.from_name;
			var post_date_container = document.createElement("span");
			post_date_container.classList.add("post_date-container");
			var post_status_container = document.createElement("span")
			post_status_container.classList.add("dark-text", "right-hand-side");
			post_status_container.innerText = Bugtracker.getReportStatusText(post.status);
			
			post_container.appendChild(post_title);
			post_container.appendChild(post_bottom);
			post_bottom.appendChild(post_author_link);
			post_bottom.appendChild(post_date_container);
			post_bottom.appendChild(post_status_container);
			content.appendChild(post_container);
		});
		window.onbeforeunload = undefined;
	},

	addNewComment: (comment) => {
		console.log(comment);
		var comments_block = document.getElementById("comments");
		comments_block.innerHTML += "<div class=\"comment\"><div class=\"comment-profile-photo-container\"><img class=\"profile-photo\" src=\"" + comment.from_profile_picture + "\"></div>"
			+ "<div class=\"comment-content\"><a href=\"/user" + comment.from_id + "\" class=\"comment-user-link\">" + comment.from_name + "</a>" + comment.text + (comment.new_status !== undefined?'<span class="report-new-status">' + l("bt_report_new_status") + ' — ' + getStatus(comment.new_status) + '</span>':'') + "</div></div>";
	},

	commentSend: () => {
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
}
