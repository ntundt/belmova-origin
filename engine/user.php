<?php

class User {
	
	public $id;

	private $db;

	function __construct($id) {
		$this->id = $id;
		$this->db = new DB();
	}

	function getAchievements() {
		
	}

	function addAchievement() {
		
	}

	function finishLesson($partition_id, $topic_id, $topic_level, $lesson_number) {
		require_once 'lesson.php';
		$lessons_list = new LessonsList();
		$this->db->setTable(DB_TABLE_PREFIX . 'users_progress');
		$progress = $this->db->getLines('topic_level, lessons_count', "`uid` = {$this->id} AND `partition_id` = {$partition_id} AND `topic_id` = {$topic_id}");
		$xp = count($lessons_list->getLesson($partition_id, $topic_id, $topic_level, $lesson_number)['exercises']);

		if (isset($progress[0])) {
			if ($progress[0]['lessons_count'] === $lesson_number - 1 and $progress[0]['topic_level'] === $topic_level) {
				$this->db->replace('lessons_count', $lesson_number, '`uid` = {$this->id} AND `partition_id` = {$partition_id} AND `topic_id` = {$topic_id}');
			} else if ($lesson_number === 1 and $topic_level === $progress[0]['topic_level'] + 1) {
				$this->db->replace('lessons_count', $lesson_number, '`uid` = {$this->id} AND `partition_id` = {$partition_id} AND `topic_id` = {$topic_id}');
				$this->db->replace('topic_level', $topic_level, '`uid` = {$this->id} AND `partition_id` = {$partition_id} AND `topic_id` = {$topic_id}');
			}
 		} else if ($topic_level === 1 and $lesson_number === 1) {
 			$this->db->append("DEFAULT, {$this->id}, {$partition_id}, {$topic_id}, {$topic_level}, {$lesson_number}");
 		}
 		return ['xp' => $this->addXP($xp)];
	}

	function addXP($value) {
		$this->db->setTable(DB_TABLE_PREFIX . 'users_xp');
		$uxp = $this->db->getLines('xp', "`uid` = {$this->id}");
		$new_value = 0;
		if (isset($uxp[0])) {
			$new_value = $uxp[0]['xp'] + $value;
			$this->db->replace('xp', $new_value, "`uid` = {$this->id}");
		} else {
			$new_value = $value;
			$this->db->append("DEFAULT, {$this->id}, {$new_value}");
		}
		return $new_value;
	}

	function getLessonsList() {
		require_once 'lesson.php';
		$lessons_list = new LessonsList();
		$this->db->setTable(DB_TABLE_PREFIX . 'users_progress');
		$list_additional_data = $this->db->getLines('partition_id, topic_id, topic_level, lessons_count', "`uid` = {$this->id}");

		$list = $lessons_list->toArray();

		for ($i = 0; $i < count($list['partitions']); $i++) {
			for ($j = 0; $j < count($list['partitions'][$i]['topics']); $j++) {
				$passed = Arr::findElementWith('topic_id', $list['partitions'][$i]['topics'][$j]['topic_id'], $list_additional_data);
				if (isset($passed['partition_id'])) {
					$list['partitions'][$i]['topics'][$j]['topic_knowledge_level'] = $passed['topic_level'];
					$list['partitions'][$i]['topics'][$j]['lessons_passed_count'] = $passed['lessons_count'];
				} else {
					$list['partitions'][$i]['topics'][$j]['topic_knowledge_level'] = 0;
					$list['partitions'][$i]['topics'][$j]['lessons_passed_count'] = 0;
				}
			} 
		}

		return $list;
	}

	function getLesson($partition_id, $topic_id, $topic_level, $lesson_number) {
		require_once 'lesson.php';
		$this->db->setTable(DB_TABLE_PREFIX . 'users_progress');
		$progress = $this->db->getLines('topic_level, lessons_count', "`partition_id`={$partition_id} AND `topic_id`={$topic_level} AND `uid`={$this->id}");

		$lessons_list = new LessonsList();
		$lesson_object = $lessons_list->getLesson($partition_id, $topic_id, $topic_level, $lesson_number);

		if ($lesson_object !== false) {
			if (isset($progress[0]['topic_level'])) {
				if ($progress[0]['topic_level'] > $topic_level) {
					$lesson_object['already_completed'] = true;
				} else if ($progress[0]['topic_level'] < $topic_level) {
					$lesson_object['have_not_achieved'] = true;
				} else {
					if ($progress[0]['lessons_count'] > $lesson_number) {
						$lesson_object['already_completed'] = true;
					} else if ($progress[0]['lessons_count'] < $lesson_number) {
						$lesson_object['have_not_achieved'] = true;
					}
				}
			} else {
				$lesson_object['already_completed'] = false;
				$lesson_object['have_not_achieved'] = true;
			}
		} else {
			return new OutputError(202);
		}

		return $lesson_object;
	}

	function getSettings() {
		
	}

	function setSettings($settings) {
		
	}

}
