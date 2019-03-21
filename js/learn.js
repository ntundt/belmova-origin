const constructorActivities = [
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

var tree;

var lastSelected;

var selectedConstructorExercise = {
	partition_id: 0,
	topic_id: 0,
	topic_level: 0,
	lesson_number: 0,
	exercise_number: 0
}
var potentialSelectedConstructorExercise = {
	partition_id: 0,
	topic_id: 0,
	topic_level: 0,
	lesson_number: 0,
	exercise_number: 0
}

var whatIsSelected = [];
var currentActivity = 0;
var wordsInputContent = [];
var answer = [];
var notUsedYetWords = [];

function goToLesson(elem) {
	window.open(URL + "learn?act=lesson&lid=" + elem.getAttribute("lid"), "_self");
}

function getGET(name) {
	if (name = (new RegExp('[?&]' + encodeURIComponent(name) + '=([^&]*)')).exec(location.search)) {
		return decodeURIComponent(name[1]);
	}
}

function handleLessonsList(response) {
	response = JSON.parse(response.response).response;
	var content = document.getElementById("content");

	var html = "";

	for (i = 0; i < response.partitions.length; i++) {
		html += "<div class=\"partition\">" + response.partitions[i].partition_name + "</div>";
		for (j = 0; j < response.partitions[i].topics.length; j++) {
			current_topic = response.partitions[i].topics[j];
			crowns = (current_topic.topic_level === undefined ? 0 : current_topic.topic_level);
			html += "<div class=\"topic piece-of-paper m12b\" lid=\"" + current_topic.next_id + "\" onclick=\"goToLesson(this)\">" 
				+ "<div class=\"topic-title-container\">"
				+ current_topic.topic_name 
				+ " <span class=\"light-gray\">" + crowns + "</span><span class=\"right-hand-side\">"
				+ (current_topic.passed ? current_topic.total_count : current_topic.passed_count) + "/" + current_topic.total_count
				+ "</span></div><div class=\"progress-bar-container\"><div class=\"progress-bar\" style=\"width: " + ~~(current_topic.passed_count / current_topic.total_count * 100) + "%;\"></div></div></div>";
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
	SendRequest("post", API_URL + "user.getLessonsList", "sid=" + getCookie("sid"), handleLessonsList);
}
function goMainPage() {
	window.open(URL, "_self");
}

function goUpperPage() {
	if (getGET("act") !== undefined) {
		window.open(URL + "learn", "_self");
	} else {
		window.open(URL, "_self");
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
	window.open(URL + "learn?act=constructor", "_self");
}

function setConstructorActivity(activity_id) {
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
		wordsHTML += "<span class=\"word\"><span class=\"word-content\">" + answer[i] + "</span><span class=\"cross\" onclick=\"answer_removeWord(this.parentNode)\"></span></span>";
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
		wordsHTML += "<span class=\"word\"><span class=\"word-content\">" + wordsInputContent[i] + "</span><span class=\"cross\" onclick=\"removeWord(this.parentNode)\"></span></span>";
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
	element.parentNode.firstChild.innerHTML += "<span class=\"word\"><span class=\"word-content\">" + element.innerText + "</span><span class=\"cross\" onclick=\"this.parentNode.remove()\"></span></span>";
}

function debugResponseHandler(response) {
	if (response.response == true) {
		alert("Сохранено.");
	} else {
		alert("Ошибка!" + response.error);
	}
}

function sendLesson() {
	var lid = document.getElementById("lessonId").value.split(" ");
	SendRequest(
		"post", 
		API_URL + "lesson.set", 
		"sid=" + getCookie("sid")
		+ "&partition_id=" + selectedConstructorExercise.partition_id
		+ "&topic_id=" + selectedConstructorExercise.topic_id
		+ "&topic_level=" + selectedConstructorExercise.topic_level
		+ "&lesson_number=" + selectedConstructorExercise.lesson_number
		+ "&exercise_number=" + (selectedConstructorExercise.exercise_number - 1)
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
	return JSON.stringify(exe);
}

function init_lesson() {
	
}

function treeHandler(response) {
	response = JSON.parse(response.response).response;
	tree = response;
	var markup = "<div class=\"nested active\" id=\"tree\">";
	var dialog_content = document.getElementById("dialog-content");
	for (var partition = 0; partition < response.partitions.length; partition++) {
		this_partition = response.partitions[partition];
		markup += "<span class=\"block\"><span class=\"caret l1\">" + this_partition.partition_name + "</span><div class=\"nested\">";
		for (var topic = 0; topic < this_partition.topics.length; topic++) {
			this_topic = this_partition.topics[topic];
			markup += "<span class=\"block\"><span class=\"caret l2\">" + this_topic.topic_name + "</span><div class=\"nested\">";
			for (var level = 0; level < this_topic.levels.length; level++) {
				this_level = this_topic.levels[level];
				markup += "<span class=\"block\"><span class=\"caret l3\">Уровень " + (level + 1) + "</span><div class=\"nested\">";
				for (var lesson = 0; lesson < this_level.lessons.length; lesson++) {
					markup += "<span class=\"block\"><span class=\"caret l4\">Урок " + (lesson + 1) + "</span><div class=\"nested\">";
					this_lesson = this_level.lessons[lesson];
					for (var exercise = 0; exercise < this_lesson.exercises.length; exercise++) {
						markup += "<span class=\"block\"><span class=\"l5\" onclick=\"onExerciseSelect(this)\" id=\"p" + (partition+1) + "t" + (topic+1) + "l" + (level+1) + "l" + (lesson+1) + "e" + (exercise+1) + "\"> Задание " + (exercise+1) + "</span></span>";
						if (this_lesson.exercises[exercise + 1] == undefined) {
							markup += "<span class=\"block\"><span class=\"l5\" onclick=\"onExerciseSelect(this)\" id=\"p" + (partition+1) + "t" + (topic+1) + "l" + (level+1) + "l" + (lesson+1) + "e" + (exercise+2) + "\"> Задание " + (exercise+2) + " (новое)</span></span>";
						}
					} 
					markup += "</div></span>"
				}
				markup += "</div></span>";
			}
			markup += "</div></span>";
		}
		markup += "</div></span>";
	}
	markup += "</div>";
	dialog_content.innerHTML = markup;

	var toggler = document.getElementsByClassName("caret");
	var i;
	for (i = 0; i < toggler.length; i++) {
		toggler[i].addEventListener("click", function() {
			this.parentElement.querySelector(".nested").classList.toggle("active");
			this.classList.toggle("caret-down");
		});
	}
}

function selectLessonDialog() {
	var win = document.getElementById("dialog-window");
	var hide = document.getElementById("dialog");
	win.hidden = false;
	hide.hidden = false;
	SendRequest("post", API_URL + "lesson.getTree", "sid="+getCookie("sid"), treeHandler);
	document.getElementById("dialogSelectButton").classList.toggle("inactive", true);
}

function dialogHide() {
	document.getElementById("dialog").hidden = true;
	document.getElementById("dialog-window").hidden = true;
}

function getDigitsFrom(text) {
	var final = "";
	var nums = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
	for (i = 0; i < text.length; i++) {
		if (inArray(text.charAt(i), nums)) {
			final += text.charAt(i);
		} else {
			if (0 < final.length) {
				if ("_" != final.charAt(final.length - 1)) {
					final += "_";
				}
			}
		}
	}
	return final;
}

function exerciseHandler(response) {
	response = JSON.parse(response.response).response;

	console.log(response);

	switch(response.type) {
	case 'readTheRule':
		setConstructorActivity(1);
		if (response.title != undefined && response.text != undefined) {
			document.getElementById("readTheRuleTitle").value = response.title;
			document.getElementById("readTheRuleText").innerText = response.text;
		}
		break;
	case 'makeTranslation':
		setConstructorActivity(2);
		if (response.sentence != undefined && response.answer != undefined && response.words != undefined) {
			document.getElementById("makeTranslationSentence").value = response.sentence;
			wordsInputContent = response.words;
			answer = response.answer;
			notUsedYetWords = arrayDifference(response.words, response.answer);
			drawWordsInput();
			keyboardsUpdate();
			drawAnswerInput();
		}
		break;
	case 'writeTranslation':
		setConstructorActivity(3);
		if (response.sentence != undefined && response.translation != undefined) {
			document.getElementById("writeTranslationSentence").value = response.sentence;
			document.getElementById("writeTranslationTranslation").value = response.translation;
		}
		break;
	}
}

function arrayDifference(a1, a2) {

    var a = [], diff = [];

    for (var i = 0; i < a1.length; i++) {
        a[a1[i]] = true;
    }

    for (var i = 0; i < a2.length; i++) {
        if (a[a2[i]]) {
            delete a[a2[i]];
        } else {
            a[a2[i]] = true;
        }
    }

    for (var k in a) {
        diff.push(k);
    }

    return diff;
}

function selectionProcess() {
	selectedConstructorExercise = potentialSelectedConstructorExercise;
	var selected = selectedConstructorExercise;
	document.getElementById("lessonId").value = 
		tree["partitions"][selected.partition_id - 1]["partition_name"] 
		+ " > " + tree["partitions"][selected.partition_id - 1]["topics"][selected.topic_id - 1]["topic_name"]
		+ " > Уровень " + (selected.topic_level)
		+ " > Урок " + (selected.lesson_number)
		+ " > Задание " + selected.exercise_number;
	SendRequest("post", API_URL + "lesson.getExerciseById",
		"partition_id=" + selected.partition_id 
		+ "&topic_id=" + selected.topic_id
		+ "&topic_level=" + selected.topic_level
		+ "&lesson_number=" + selected.lesson_number
		+ "&exercise_number=" + (selected.exercise_number - 1),
		exerciseHandler
	)
	dialogHide();
}

function onExerciseSelect(elem) {
	var id = getDigitsFrom(elem.id).split("_");
	selectedConstructorExercise = {
		partition_id: id[0],
		topic_id: id[1],
		topic_level: id[2],
		lesson_number: id[3],
		exercise_number: id[4]
	}
	elem.classList.toggle("selected", true);
	if (lastSelected != undefined) {
		lastSelected.classList.toggle("selected", false);
	}
	lastSelected = elem;
	document.getElementById("dialogSelectButton").classList.toggle("inactive", false);
	document.getElementById("dialogSelectButton").setAttribute("onclick", "selectionProcess()");
	potentialSelectedConstructorExercise = {
		partition_id: id[0],
		topic_id: id[1],
		topic_level: id[2],
		lesson_number: id[3],
		exercise_number: id[4]
	}
}
