function escapeRegExp(str) {
	return str.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

String.prototype.replaceAll = function(search, replacement) {
	search = escapeRegExp(search);
	var target = this;
	return target.replace(new RegExp(search, 'g'), replacement);
};

function l(name, replace=[]) {
	var result = LANG[name];
	if (replace.length > 0) {
		for (var i = 0; i < replace.length; i++) {
			result = result.replaceAll("%&" + i, replace[i]);
		}
	}
	return result;
}

const LANG = {
	// Languages (its cases)
	lang_russian_abl: "русском",
	lang_russian_nom: "русский",
	lang_belarusian_abl: "белорусском",
	lang_belarusian_nom: "белорусский",
	// Common
	learn: "Заниматься",
	error_during_api_request: "Сервер вернул ошибку №%&0. \"%&1\"",
	api_request_successfully_performed: "Запрос выполнен. Ответ: \"%&0\"",
	russians_forward: "Русские вперед! Русские вперед! Русские вперед! Русские вперед! Русские вперед! Русские вперед!",
	please_input: "Введите",
	please_select: "Выберите",
	// Bugtracker
	bugtracker: "Баг-трекер",
	bt_status_not_seen: "Не просмотрено модератором",
	bt_status_in_process: "В процессе",
	bt_status_closed: "Закрыт",
	bt_status_waiting: "Ожидает",
	bt_status_fixed: "Исправлен",
	bt_status_open: "Открыт",
	bt_replay_steps: "Шаги воспроизведения",
	bt_report_new_status: "Новый статус отчёта",
	bt_do_not_change_status: "Не изменять",
	bt_fact_result: "Фактический результат",
	bt_needed_result: "Ожидаемый результат",
	bt_report_new_status: "Статус отчёта",
	publication_time: "Время публикации",
	comments: "Комментарии",
	nobody_commented: "Никто еще не прокомментировал",
	your_comment: "Ваш комментарий..",
	send: "Отправить",
	// Learn activity
	allLessons: "Все уроки",
	constructor: "Конструктор",
	// Lesson
	readTheRule: "Прочтите правило",
	writeTranslation: "Напишите это на %&0",
	makeTranslation: "Переведите это на %&0",
	lesson_ended: "Урок окончен! +%&0 опыта",
	you_have__xp: "У Вас %&0 очков опыта"
}
