class Lesson {
	constructor(lesson_id, elements) {
		this.lesson_id = lesson_id;
		Lesson.elements = elements;
		Lesson.instance = this;
		this.exercises = [];
		this.getExercises();
	}
	static getInstance() {
		if (Lesson.instance !== undefined) {
			return Lesson.instance;
		}
	}
	updateProgressBar() {
		var percentage = ~~(this.solved / this.exercisesCount * 100);
		console.log("Progress bar's width is now " + percentage + "%");
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
		for (var i = this.nowSolving; i < this.exercisesCount; i++) {
			if (this.exercises[i].solved === undefined) {
				this.draw(this.exercises[i]);
				this.nowSolving = i;
				return;
			}
		}
	}
	draw(exercise) {
		switch(exercise.type) {
		case "writeTranslation":
			Lesson.elements.taskTitle.innerText = l("writeTranslation", [l("lang_russian_nom")]);
			var textarea = document.createElement("textarea");
			Lesson.elements.activeZone.appendChild(textarea);
			this.translationElement = textarea;
			break;
		case "makeTranslation":
			Lesson.elements.taskTitle.innerText = l("makeTranslation", [l("lang_russian_nom")]);
			break;
		case "readTheRule":
			Lesson.elements.taskTitle.innerText = l("readTheRule");
			break;
		}
	}
}