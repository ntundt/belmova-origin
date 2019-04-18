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
		let content = document.getElementById("content");
		content.classList.add("no-padding");
		response = JSON.parse(response.response).response;

		document.title = response.title + " | " + l("bugtracker");

		(block_title = document.createElement("div")).classList.add("paper-head");
		(link = document.createElement("a")).classList.add("textlink");
		link.setAttribute("href", "/bugtracker");
		link.innerText = l("bugtracker");
		let status = document.createElement("span");
		status.classList.add("right-hand-side", "gray");
		status.appendChild(document.createTextNode(Bugtracker.getReportStatusText(response.status)));
		
		(main_content = document.createElement("div")).classList.add("p12");
		
		(description_title = document.createElement("span")).classList.add("gray", "block");
		description_title.innerText = l("bt_replay_steps") + ":";
		(description = document.createElement("div")).classList.add("m12b");
		description.innerText = response.description;

		(fact_result_title = document.createElement("span")).classList.add("gray", "block");
		fact_result_title.innerText = l("bt_fact_result") + ":";
		(fact_result = document.createElement("div")).classList.add("m12b");
		fact_result.innerText = response.fact_result;

		(needed_result_title = document.createElement("span")).classList.add("gray", "block");
		needed_result_title.innerText = l("bt_needed_result") + ":";
		(needed_result = document.createElement("div"));
		needed_result.innerText = response.needed_result;

		(foot = document.createElement("div")).classList.add("paper-foot");
		foot.innerText = l("publication_time") + ": " + response.date;

		block_title.appendChild(link);
		block_title.appendChild(document.createTextNode(" > " + response.title));
		block_title.appendChild(status);

		main_content.appendChild(description_title);
		main_content.appendChild(description);
		main_content.appendChild(fact_result_title);
		main_content.appendChild(fact_result);
		main_content.appendChild(needed_result_title);
		main_content.appendChild(needed_result);

		content.innerHTML = "";
		content.appendChild(block_title);
		content.appendChild(main_content);
		content.appendChild(foot);
		// new_html += "<div class=\"paper-head\"><a class=\"textlink\" href=\"/bugtracker\">" 
		// + l("bugtracker") + "</a> > " + response.title + "<span class=\"right-hand-side gray\">" 
		// + Bugtracker.getReportStatusText(response.status) + "</span></div>";
		// new_html += 
		// 	"<div class=\"p12\">" + 
		// 	"<span class=\"gray block\">" + l("bt_replay_steps") + ":</span>" + 
		// 	"<div class=\"m12b\">" + response.description + "</div>" +
		// 	"<span class=\"gray block\">" + l("bt_fact_result") + ":</span>" + 
		// 	"<div class=\"m12b\">" + response.fact_result + "</div>" +
		// 	"<span class=\"gray block\">" + l("bt_needed_result") + ":</span>" +
		// 	"<div>" + response.needed_result + "</div>" +
		// 	"</div>";
		// new_html += 
		// 	"<div class=\"paper-foot\">" + l("publication_time") + ": " +
		// 	response.date +
		// 	"</div>"

		let comments_count = 0;
		if (response.comments !== undefined) {
			comments_count = response.comments.length;
		}
		if (comments_count > 0) {
			for (let i = 0; i < comments_count; i++) {
				Bugtracker.addNewComment(response.comments[i]);
			}
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
			(post_title = document.createElement("div")).classList.add("post_title");
			post_title.innerText = post.title;
			(post_bottom = document.createElement("div")).classList.add("bottom");
			(post_author_link = document.createElement("a")).setAttribute("href", "/user" + post.from_id);
			post_author_link.innerText = post.from_name;
			(post_date_container = document.createElement("span")).classList.add("post_date-container");
			post_date_container.innerText = " " + post.date;
			(post_status_container = document.createElement("span")).classList.add("dark-text", "right-hand-side");
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

	updateCommentsCount: (by) => {
		try {
			let comments_count_notifier = document.getElementById("commentsCount");
			comments_count_notifier.innerText = parseInt(comments_count_notifier.innerText) + by;
		} catch (e) {
			// Who cares?
		}
	},

	addNewComment: (comment) => {
		let comments_block = document.getElementById("comments");
		
		(comment_container = document.createElement("div")).classList.add("comment", "no-border-bottom");
		(profile_photo_container = document.createElement("div")).classList.add("comment-profile-photo-container");
		(photo = document.createElement("img")).classList.add("profile-photo");
		photo.setAttribute("src", comment.from_profile_picture);
		(comment_content = document.createElement("div")).classList.add("comment-content");
		(publisher_profile_link = document.createElement("a")).classList.add("comment-user-link");
		publisher_profile_link.setAttribute("href", "/user" + comment.from_id);
		publisher_profile_link.innerText = comment.from_name;

		comment_content.appendChild(publisher_profile_link);
		comment_content.appendChild(document.createTextNode(comment.text));
		if (comment.new_status !== undefined) {
			(report_new_status = document.createElement("span")).classList.add("report-new-status");
			report_new_status.innerText = l("bt_report_new_status") + ' — ' + Bugtracker.getReportStatusText(comment.new_status);
			comment_content.appendChild(report_new_status);
		}
		profile_photo_container.appendChild(photo);
		comment_container.appendChild(profile_photo_container);
		comment_container.appendChild(comment_content);

		try {
			if (!comments_block.firstChild.classList.contains("comment"))
				comments_block.innerHTML = "";
			comments_block.lastChild.classList.remove("no-border-bottom");
		} catch (e) {}

		comments_block.appendChild(comment_container);
		Bugtracker.updateCommentsCount(1);
	},

	commentSend: () => {
		var comment_text_input = document.getElementById("comment-text-input");
		var request = new APIRequest(getCookie("sid"));
		request.setMethod("bugtracker.addComment");
		request.addParameter("text", comment_text_input.value);
		request.addParameter("reply_to", getGET("post"));
		if (reportNewStatusSelector.getSelected() != null && reportNewStatusSelector.getSelected() != "do_not_change") 
			request.addParameter("new_status", reportNewStatusSelector.getSelected());
		request.perform(function(r) {
			Bugtracker.addNewComment(JSON.parse(r.response).response);
			comment_text_input.value = "";
			reportNewStatusSelector.reset();
		});
	}
}
