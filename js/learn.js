var whatIsSelected = [];

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
				(current_topic.topic_passed?current_topic.total_lessons_count:current_topic.passed_lessons_count) + "/" + current_topic.total_lessons_count +
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
function goUpperPage() {
	if (getGET("act") !== undefined) {
		window.open("http://localhost/learn", "_self");
	} else {
		window.open("http://localhost/", "_self");
	}
}
function init_constructor() {
	//document.getElementById("content").innerHTML = "";
}
function isAlreadySelected(sid) {
	elementWithNeededValue = false;
	whatIsSelected.forEach(function (current_elem, i) {
		if (current_elem.selector_id == sid) {
			elementWithNeededValue = i;
		}
	});
	return elementWithNeededValue;
}
function setSelected(what) {
	splitted = what.split("_");
	selector_id = splitted[0];
	selected_element = splitted[1];
	selected = isAlreadySelected(selector_id);
	if (selected === false) {
		whatIsSelected.push({selector_id: selector_id, selected_element: selected_element});
	} else {
		whatIsSelected[selected].selected_element = selected_element;
	}
	console.log(whatIsSelected);
}
function onDropdownSelect(elem) {
	var selector = document.getElementById("typeSelector");
	var final = "";
	selector.innerText = elem.innerText;
	var nums = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
	for (i = 0; i < elem.id.length; i++) {
		if (inArray(elem.id.charAt(i), nums)) {
			final += elem.id.charAt(i);
		} else {
			if (final.length > 0) {
				if (final.charAt(final.length - 1) != "_") {
					final += "_";
				}
			}
		}
	}
	setSelected(final);
}
function inArray(elem, arr) {
	for (j = 0; j < arr.length; j++) {
		if (arr[j] == elem) {
			return true;
		}
	}
}
function goToConstructor() {
	window.open("http://localhost/learn?act=constructor", "_self");
}