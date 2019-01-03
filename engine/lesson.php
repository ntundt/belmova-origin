<?php

class Lesson {

	public $topic_id;
	public $topic_level;
	public $lesson_id;

	function __construct($topic_id, $topic_level, $lesson_id) {
		$this->topic_id = $topic_id;
		$this->topic_level = $topic_level;
		$this->lesson_id = $lesson_id;
		$this->db = new DB();
		$this->db->setTable(DB_TABLE_PREFIX . 'exercises_basic_' . Lang::$lang);
	}

	function getExercises() {
		$exercises = $this->db->getLines('topic_name, exercises', "`topic_id` = {$this->topic_id} AND `topic_level` = {$this->topic_level} AND `lesson_id` = {$this->lesson_id}");

		return $exercises;
	}

}