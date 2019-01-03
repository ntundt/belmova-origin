<?php

class LessonsList {

	function __construct() {
		$this->db = new DB();
		$this->db->setTable(DB_TABLE_PREFIX . 'exercises_basic_' . Lang::$lang);
	}

	function setLesson($partiton_id, $topic_id, $topic_level, $lesson_id, $lesson_object) {
		if (strcmp(gettype($lesson_object), 'mixed') === 0) {
			$lesson_object = json_encode($lesson_object, JSON_UNESCAPED_UNICODE);
		}

		$where = "`partition_id`={$partition_id} AND `topic_id`={$topic_id} AND `topic_level`={$topic_level} AND `lesson_id`={$lesson_id}";
		$lesson_isset = $this->db->getLines('exercises', $where);
		if (isset($lesson_isset[0]['exercises'])) {
			$this->db->replace('exercises', $lesson_object, $where);
		} else {
			$this->db->append();
		}

		return $exercises;
	}

}