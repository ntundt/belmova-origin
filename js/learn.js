id = document.getElementById;

var constructorActivities = [
	{
		activityId: 1,
		sName: "readTheRule",
		requestParameters: [
			{
				parameterName: "title",
				parameterValue: ""
			},
			{
				parameterName: "text",
				parameterValue: ""
			}
		]
	},
	{
		activityId: 2,
		sName: "makeTranslation",
		requestParameters: [
			{
				parameterName: "sentence",
				parameterValue: ""
			},
			{
				parameterName: "words",
				parameterValue: ""
			},
			{
				parameterName: "variants",
				parameterValue: ""
			}
		]
	},
	{
		activityId: 3,
		sName: "writeTranslation",
		requestParameters: [
			{
				parameterName: "sentence",
				parameterValue: ""
			},
			{
				parameterName: "variants",
				parameterValue: ""
			}
		]
	},
	{
		activityId: 4,
		sName: "answerTheQuestion",
		requestParameters: [
			{
				parameterName: "question",
				parameterValue: ""
			},
			{
				parameterName: "answers",
				parameterValue: ""
			}
		]
	}
];

var whatIsSelected = [];
var currentActivity = 0;
var wordsInputContent = [];
var answer = [];
var notUsedYetWords = [];

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
	console.log(window.whatIsSelected);
	if (1 == selector_id) {
		setConstructorActivity(selected_element);
	}
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
			if (0 < final.length) {
				if ("_" != final.charAt(final.length - 1)) {
					final += "_";
				}
			}
		}
	}
	setSelected(final);
}

function inArray(elem, arr) {
	for (j = 0; j < arr.length; j++) {
		if (elem == arr[j]) {
			return true;
		}
	}
}

function goToConstructor() {
	window.open("http://localhost/learn?act=constructor", "_self");
}

function setConstructorActivity(activity_id) {
	console.log(constructorActivities[activity_id - 1].sName);
	document.getElementById(constructorActivities[currentActivity].sName).hidden = true;
	document.getElementById(constructorActivities[activity_id - 1].sName).hidden = false;
	currentActivity = activity_id - 1;
}

function makeString(array, delimiter) {
	var result = "";
	for (var i = 0; i < array.length; i++) {
		result += array[i] + (array[i] !== undefined?delimiter:"")
	}
	return result;
}

function drawAnswerInput() {
	var inputWithGivenWords = document.getElementById("ansh");
	var wordsHTML = "";
	for (var i = 0; i < answer.length; i++) {
		wordsHTML += "<span class=\"word\">" + answer[i] + "<span class=\"cross\" onclick=\"answer_removeWord(this.parentNode)\"></span></span>";
	}
	inputWithGivenWords.innerHTML = wordsHTML;
}

function answer_addWord(element) {
	var inputWithGivenWords = document.getElementById("ansh");
	answer.push(element.innerText);
	notUsedYetWords = removeElement(element.innerText, notUsedYetWords);
	element.remove();
	drawAnswerInput();
}

function answer_removeWord(element) {
	answer = removeElement(element.innerText, answer);
	notUsedYetWords.push(element.innerText);
	element.remove();
	drawAnswerInput();
	keyboardsUpdate();
}

function onSpacePressed() {
	var wordsInputText = document.getElementById("wordsInputTextInput");
	wordsInputContent.push(wordsInputText.innerText.replace(/^\s*(.*)\s*$/, '$1'));
	notUsedYetWords.push(wordsInputText.innerText.replace(/^\s*(.*)\s*$/, '$1'));
	drawWordsInput();
	keyboardsUpdate();
}

function drawWordsInput() {
	var wordsHTML = "";
	for (var i = 0; i < wordsInputContent.length; i++) {
		wordsHTML += "<span class=\"word\">" + wordsInputContent[i] + "<span class=\"cross\" onclick=\"removeWord(this.parentNode)\"></span></span>";
	}
	wordsHTML += "<div id=\"wordsInputTextInput\" contenteditable></div>";
	wordsInput.innerHTML = wordsHTML;

	var wordsInputText = document.getElementById("wordsInputTextInput");
	placeCaretAtEnd(wordsInputText);
}

function removeWord(element) {
	wordsInputContent = removeElement(element.innerText, wordsInputContent);
	notUsedYetWords = removeElement(element.innerText, notUsedYetWords);
	answer = removeElement(element.innerText, answer);
	element.remove();
	keyboardsUpdate();
	drawAnswerInput();
}

function removeElement(value, array) {
	return array.filter(function(element){
		return element != value;
	});
}

function setCPAtEnd() {
	var wordsInputText = document.getElementById("wordsInputTextInput");
	placeCaretAtEnd(wordsInputText);
}

function keyboardsUpdate() {
	keyboards = document.getElementById("keyboard");
	var wordsHTML = "";
	for (var i = 0; i < notUsedYetWords.length; i++) {
		wordsHTML += "<span class=\"word\" onclick=\"answer_addWord(this)\">" + notUsedYetWords[i] + "</span>";
	}
	keyboards.innerHTML = wordsHTML;
}

function onBackspacePressed() {
	var wordsInputText = document.getElementById("wordsInputTextInput");
	var wordsInput = document.getElementById("wordsInput");

	if (wordsInputText.innerText == "" && wordsInput.childNodes.length - 2 >= 0) {
		removeWord(wordsInput.childNodes[wordsInput.childNodes.length - 2]);
	}
	placeCaretAtEnd(wordsInputText);
}

function initWordsInput() {
	var wordsInput = document.getElementById("wordsInput");

	wordsInput.addEventListener('keydown', function(e) {
		if (e.keyCode == 32 || e.keyCode == 13) {
			onSpacePressed();
		} else if (e.keyCode == 8) {
			onBackspacePressed();
		}
	});
}

function copyToHead(element) {
	element.parentNode.firstChild.innerHTML += "<span class=\"word\">" + element.innerText + "<span class=\"cross\" onclick=\"this.parentNode.remove()\"></span></span>";
}

function debugResponseHandler(response) {
	alert(response.response);
}

function sendLesson() {
	var lid = document.getElementById("lessonId").value.split(" ");
	SendRequest(
		"post", 
		"http://localhost/work/method/lesson.set", 
		"sid=" + getCookie("sid")
		+ "&partition_id=" + lid[0]
		+ "&topic_id=" + lid[1]
		+ "&topic_level=" + lid[2]
		+ "&lesson_number=" + lid[3]
		+ "&json_object=" + makeLessonObject(), 
		debugResponseHandler
	);
}

function makeLessonObject() {
	var aid = whatIsSelected[0].selected_element - 1;
	var act = constructorActivities[aid];
	var exe = {type: act.sName};
	switch (act.sName) {
	case 'readTheRule':
		exe.title = document.getElementById("readTheRuleTitle").value;
		exe.text = document.getElementById("readTheRuleText").innerText;
		break;
	case 'makeTranslation':
		exe.sentence = document.getElementById("makeTranslationSentence").value;
		exe.words = wordsInputContent;
		exe.answer = answer;
		break;
	case 'writeTranslation':
		exe.sentence = document.getElementById("writeTranslationSentence").value;
		exe.translation = document.getElementById("writeTranslationTranslation").value;
		break;
	}
	var object = {
		exercises: exe
	};
	return JSON.stringify(object);
}

function init_lesson() {
	
}

function treeHandler(response) {
	response = JSON.parse(response.response).response;
}

function selectLessonDialog() {
	var win = document.getElementById("dialog-window");
	var hide = document.getElementById("dialog");
	win.hidden = false;
	hide.hidden = false;
	SendRequest("post", "http://localhost/work/method/lessons.getTree", "sid="+getCookie("sid"), treeHandler);
}