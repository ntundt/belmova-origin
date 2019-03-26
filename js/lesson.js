class Lesson {
	constructor(lesson_id, elements) {
		this.lesson_id = lesson_id;
		Lesson.elements = elements;
		Lesson.instance = this;
		Lesson.sounds = {
			wrong_answer: new Audio("/work/ui/wrong_answer.mp3"),
			right_answer: new Audio("/work/ui/right_answer.mp3"),
			lesson_complete: new Audio("/work/ui/lesson_complete.mp3")
		};
		Lesson.makeTranslationOpt = {
			selectedWords: [],
			availableWordsList: []
		};
		this.exercises = [];
		this.getExercises();

		Lesson.elements.checkButton.onclick = function() {
			Lesson.check();
		}
		Lesson.elements.onMistakeButton.onclick = function() {
			Lesson.elements.onMistakeNotifier.hidden = true;
			Lesson.getInstance().nextUnsolved();
		}
		Lesson.elements.onSuccessButton.onclick = function() {
			Lesson.elements.onSuccessNotifier.hidden = true;
			Lesson.getInstance().nextUnsolved();
		}
	}
	static win()  {
		var complete = new APIRequest(getCookie("sid"));
		complete.setMethod("user.finishLesson");
		complete.addParameter("lesson_id", Lesson.getInstance().lesson_id);
		complete.perform(function(r) {
			Lesson.winCallback(JSON.parse(r.response).response);
		});
	}
	static winCallback(response) {
		Lesson.elements.winScreen.hidden = false;
		Lesson.elements.progressNotification.innerText = l("lesson_ended", [response.added_xp]);
		Lesson.elements.newProgress.innerText = l("you_have__xp", [response.current_xp]);
		Lesson.sounds.lesson_complete.play();
	}
	static compareArrays(array1, array2) {
		if (array1.length !== array2.length) {
			return false;
		}
		for (var i = 0; i < array1.length; i++) {
			if (array1[i] !== array2[i]) {
				return false;
			}
		}
		return true;
	}
	static success() {
		Lesson.elements.onSuccessNotifier.hidden = false;
		Lesson.sounds.right_answer.play();
		Lesson.getInstance().nowSolving++;
		Lesson.getInstance().solved++;
		Lesson.getInstance().updateProgressBar();
		Lesson.getInstance().nowSolvingObject.solved = true;
	}
	static failure(text="") {
		Lesson.elements.onMistakeNotifier.hidden = false;
		if (text != "") Lesson.elements.onMistakeText.innerText = text;
		Lesson.sounds.wrong_answer.play();
		Lesson.getInstance().exercises.push(Lesson.getInstance().nowSolvingObject);
		Lesson.getInstance().exercises.splice(Lesson.getInstance().nowSolving, 1);
	}
	static processString(string) {
		var marks = [',', '.', '!', '?', ';', ':', '-', '(', ')', '[', ']', '{', '}', '\'', '"', '<', '>', '–', '—'];
		for (var i = 0; i < marks.length; i++) {
			string.replaceAll(marks[i], '');
		}
		return string.toLowerCase();
	}
	static check() {
		switch (Lesson.exerciseType) {
		case "writeTranslation":
			var userText = Lesson.elements.writeTranslation_textarea.value;
			var neededText = Lesson.getInstance().nowSolvingObject.translation;
			if (Lesson.processString(userText) == Lesson.processString(""+neededText)) {
				Lesson.success();
			} else {
				Lesson.failure(neededText);
			}
			break;
		case "makeTranslation":
			var answer = [];
			for (var i = 0;  i < Lesson.makeTranslationOpt.selectedWords.length; i++) {
				answer.push(Lesson.makeTranslationOpt.selectedWords[i].text);
			}
			if (Lesson.compareArrays(answer, Lesson.makeTranslationOpt.correctAnswer)) {
				Lesson.success();
			} else {
				Lesson.failure();
			}
			break;
		}
	}
	static isWordWithIdSelected(id) {
		for (var i = 0; i < Lesson.makeTranslationOpt.selectedWords.length; i++) {
			if (Lesson.makeTranslationOpt.selectedWords[i].id == id) {
				return i;
			}
		}
		return false;
	}
	static getInstance() {
		if (Lesson.instance !== undefined) {
			return Lesson.instance;
		}
	}
	static addSelectedWord(clickedElement) {
		if (Lesson.isWordWithIdSelected(clickedElement.attributes.word_id.value) === false) {
			clickedElement.classList.toggle("used");
			if (Lesson.makeTranslationOpt.selectedWords === undefined)
				Lesson.makeTranslationOpt.selectedWords = [];
			var word = document.createElement("span");
			word.classList.toggle("word");
			word.innerText = clickedElement.innerText;
			word.setAttribute("word_id", clickedElement.attributes.word_id.value);
			Lesson.makeTranslationOpt.selectedWords.push({
				id: clickedElement.attributes.word_id.value, 
				text: clickedElement.innerText, 
				element: word, 
				origin: clickedElement
			});
			word.onclick = function() {
				Lesson.removeSelectedWord(this);
			}
			Lesson.elements.wordsContainer.appendChild(word);
		} else {
			Lesson.removeSelectedWord(clickedElement);
		}
	}
	static removeSelectedWord(clickedElement) {
		var word = Lesson.makeTranslationOpt.selectedWords[Lesson.isWordWithIdSelected(clickedElement.attributes.word_id.value)];
		word.element.remove();
		word.origin.classList.toggle("used", false);
		Lesson.makeTranslationOpt.selectedWords.splice(Lesson.isWordWithIdSelected(clickedElement.attributes.word_id.value), 1);
	}
	static everythingIsSolved() {
		var isntIt = true;
		for (var i = 0; i < Lesson.getInstance().exercisesCount; i++) {
			if (Lesson.getInstance().exercises[i].solved === undefined) {
				isntIt = false;
			}
		}
		return isntIt;
	}
	updateProgressBar() {
		var percentage = ~~(this.solved / this.exercisesCount * 100);
		Lesson.elements.progressBar.style.width = percentage + "%";
	}
	getExercises() {
		var request = new APIRequest(getCookie("sid"));
		request.setMethod("user.getLesson");
		request.addParameter("lesson_id", this.lesson_id);
		request.perform(function(r) {
			r = JSON.parse(r.response).response;
			document.title = r.topic_name + " | " + l("learn");
			Lesson.getInstance().getExercisesCallback(r);
		});
	}
	getExercisesCallback(lessonObject) {
		this.exercises = lessonObject.exercises;
		this.nowSolving = 0;
		this.solved = 0;
		this.exercisesCount = lessonObject.exercises_count;
		this.nextUnsolved();
	}
	nextUnsolved() {
		if (Lesson.everythingIsSolved()) {
			Lesson.win();
		}
		for (var i = 0; i < this.exercisesCount; i++) {
			if (this.exercises[i].solved === undefined) {
				this.draw(this.exercises[i]);
				this.nowSolving = i;
				return;
			}
		}
	}
	draw(exercise) {
		Lesson.elements.loadingScreen.hidden = true;
		Lesson.elements.activeZone.innerHTML = "";
		this.nowSolvingObject = exercise;
		Lesson.makeTranslationOpt = {
			availableWordsList: [],
			selectedWords: [],
			correctAnswer: []
		};
		Lesson.exerciseType = exercise.type;
		switch (exercise.type) {
		case "writeTranslation":
			Lesson.elements.taskTitle.innerText = l("writeTranslation", [l("lang_russian_abl")]);
			Lesson.elements.taskText.innerText = exercise.sentence;
			var textarea = document.createElement("textarea");
			textarea.classList.toggle("write-translation-textarea");
			textarea.setAttribute("placeholder", l("please_input"));
			Lesson.elements.activeZone.appendChild(textarea);
			Lesson.elements.writeTranslation_textarea = textarea;
			break;
		case "makeTranslation":
			Lesson.elements.taskTitle.innerText = l("makeTranslation", [l("lang_russian_nom")]);

			var wordsInput = document.createElement("div");
			wordsInput.classList.toggle("words-input", true);
			
			for (var i = 0; i < 2; i++) {
				var line = document.createElement("div");
				line.classList.toggle("line");
				wordsInput.appendChild(line);
			}
			
			Lesson.makeTranslationOpt.correctAnswer = exercise.answer;

			var wordsContainer = document.createElement("div");
			wordsContainer.classList.toggle("words-container", true);
			wordsInput.appendChild(wordsContainer);
			Lesson.elements.wordsContainer = wordsContainer;
			
			var wordsKeyboard = document.createElement("div");
			wordsKeyboard.classList.toggle("words-keyboard", true);
			for (var i = 0; i < exercise.words.length; i++) {
				var word = document.createElement("span");
				word.classList.toggle("word");
				word.setAttribute("word_id", i);
				word.innerText = exercise.words[i];
				wordsKeyboard.appendChild(word);
				if (Lesson.makeTranslationOpt === undefined)
					Lesson.makeTranslationOpt = {};
				if (Lesson.makeTranslationOpt.availableWordsList === undefined)
					Lesson.makeTranslationOpt.availableWordsList = [];
				Lesson.makeTranslationOpt.availableWordsList.push({id: i, text: exercise.words[i]});
				word.onclick = function() {
					Lesson.addSelectedWord(this);
				}
			}
			Lesson.elements.activeZone.appendChild(wordsInput);
			Lesson.elements.activeZone.appendChild(wordsKeyboard);

			Lesson.elements.taskText.innerText = exercise.sentence;
			this.translationElement = textarea;
			break;
		case "readTheRule":
			Lesson.elements.taskTitle.innerText = l("readTheRule");
			break;
		}
	}
}