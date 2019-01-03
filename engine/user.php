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

	function onLessonIsOver() {
		
	}

	function statAdd() {
		
	}

	function getLessonsList() {
		$this->db->setTable(DB_TABLE_PREFIX . 'exercises_basic_' . Lang::$lang);
		$list_data = $this->db->getLines('partition_id, topic_id');
		$this->db->setTable(DB_TABLE_PREFIX . 'users_progress');
		$list_additional_data = $this->db->getLines('partition_id, topic_id, topic_level, lessons_count', "`uid` = {$this->id}");
		$this->db->setTable(DB_TABLE_PREFIX . 'exercises_partitions_' . Lang::$lang);
		$partition_names = $this->db->getLines('id, name');
		$this->db->setTable(DB_TABLE_PREFIX . 'exercises_topics_' . Lang::$lang);
		$topic_names = $this->db->getLines('partition_id, id, name');


		$list = ['partitions' => []];

		$partitions = [];
		if (isset($list_data[0])) { 
			for ($i = 0; $i < count($list_data); $i++) {
				if (!Arr::isInArray($list_data[$i]['partition_id'], $partitions)) {
					$partitions[] = $list_data[$i]['partition_id'];
					$list['partitions'][] = [
						'partiton_id' => $list_data[$i]['partition_id'],
						'partition_name' => Arr::getAllElements('id', $list_data[$i]['partition_id'], $partition_names)[0]['name'],
						'topics' => Arr::getAllElements('partition_id', $list_data[$i]['partition_id'], $list_data)
					];
				}
			}
		}

		unset($partitions);

		for ($i = 0; $i < count($list['partitions']); $i++) {
			for ($j = 0; $j < count($list['partitions'][$i]['topics']); $j++) {
				$passed = Arr::findElementWith('topic_id', $list['partitions'][$i]['topics'][$j]['topic_id'], $list_additional_data);
				if (isset($passed['partition_id'])) {
					$list['partitions'][$i]['topics'][$j]['topic_knowledge_level'] = $passed['topic_level'];
					$list['partitions'][$i]['topics'][$j]['lessons_passed_count'] = $passed['lessons_count'];
					$list['partitions'][$i]['topics'][$j]['topic_name'] = Arr::getAllElements('id', $j + 1, Arr::getAllElements('partition_id', $i + 1, $topic_names))[0]['name'];
				} else {
					$list['partitions'][$i]['topics'][$j]['topic_knowledge_level'] = 0;
					$list['partitions'][$i]['topics'][$j]['lessons_passed_count'] = 0;
					$list['partitions'][$i]['topics'][$j]['topic_name'] = Arr::getAllElements('id', $j + 1, Arr::getAllElements('partition_id', $i + 1, $topic_names))[0]['name'];
				}
				unset($list['partitions'][$i]['topics'][$j]['partition_name']);
			} 
		}

		return $list;
	}

	function getLesson($partition_id, $topic_id, $topic_level, $lesson_number) {
		$this->db->setTable(DB_TABLE_PREFIX . 'exercises_basic_' . Lang::$lang);
		$lesson = $this->db->getLines('topic_id, exercises', "`partition_id`={$partition_id} AND `topic_id`={$topic_id} AND `topic_level`={$topic_level} AND `lesson_number`={$lesson_number}");
		$this->db->setTable(DB_TABLE_PREFIX . 'users_progress');
		$progress = $this->db->getLines('topic_level, lessons_count', "`partition_id`={$partition_id} AND `topic_id`={$topic_level} AND `uid`={$this->id}");

		$exercises = json_decode($lesson[0]['exercises']);

		$lesson_object = ['exercises' => $exercises, 'exercises_count' => count($exercises), 'already_completed' => false, 'have_not_achieved' => false];

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

		return $lesson_object;
	}

	function getSettings() {
		
	}

	function setSettings($settings) {
		
	}

}
