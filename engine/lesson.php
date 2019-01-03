<?php

class LessonsList {

	function __construct() {
		$this->db = new DB();
		$this->db->setTable(DB_TABLE_PREFIX . 'exercises_basic_' . Lang::$lang);
	}

	function setLesson($partition_id, $topic_id, $topic_level, $lesson_number, $lesson_object) {
		if (strcmp(gettype($lesson_object), 'mixed') === 0) {
			$lesson_object = json_encode($lesson_object, JSON_UNESCAPED_UNICODE);
		}

		$where = "`partition_id`={$partition_id} AND `topic_id`={$topic_id} AND `topic_level`={$topic_level} AND `lesson_number`={$lesson_number}";
		$lesson_isset = $this->db->getLines('exercises', $where);
		if (isset($lesson_isset[0]['exercises'])) {
			$this->db->replace('exercises', $lesson_object, $where);
		} else {
			$this->db->append("DEFAULT, {$partition_id}, {$topic_id}, {$topic_level}, {$lesson_number}, {$lesson_object}");
		}
	}

	function getPartitonName($partition_id) {
		$last_table = $this->db->table;

		$this->db->setTable(DB_TABLE_PREFIX . 'exercises_partitions_' . Lang::$lang);
		$name = $this->db->getLines('name', "`id`={$partition_id}")[0]['name'];

		$this->db->table = $last_table;
		return $name;
	}

	function getTopicName($partition_id, $topic_id) {
		$last_table = $this->db->table;

		$this->db->setTable(DB_TABLE_PREFIX . 'exercises_topics_' . Lang::$lang);
		$name = $this->db->getLines('name', "`partition_id`={$partition_id} AND `id`={$topic_id}")[0]['name'];

		$this->db->table = $last_table;
		return $name;
	}

	function getTopicsFrom($partition_id) {
		$last_table = $this->db->table;

		$this->db->setTable(DB_TABLE_PREFIX . 'exercises_basic_' . Lang::$lang);
		$list_data = $this->db->getLines('partition_id, topic_id');

		$topics = Arr::getAllElements('partition_id', $partition_id, $list_data);
		for ($i = 0; $i < count($topics); $i++) {
			$topics[$i]['topic_name'] = $this->getTopicName($topics[$i]['partition_id'], $topics[$i]['topic_id']);
			$topics[$i]['topic_id'] = intval($topics[$i]['topic_id']);
			unset($topics[$i]['partition_id']);
		} 

		$this->db->table = $last_table;
		return $topics;
	}

	function toArray() {
		$this->db->setTable(DB_TABLE_PREFIX . 'exercises_basic_' . Lang::$lang);
		$list_data = $this->db->getLines('partition_id, topic_id');
		$list = ['partitions' => []];

		$partitions = [];
		if (isset($list_data[0])) { 
			for ($i = 0; $i < count($list_data); $i++) {
				if (!Arr::isInArray($list_data[$i]['partition_id'], $partitions)) {
					$partitions[] = $list_data[$i]['partition_id'];
					$list['partitions'][] = [
						'partition_id' => $list_data[$i]['partition_id'],
						'partition_name' => $this->getPartitonName($list_data[$i]['partition_id']),
						'topics' => $this->getTopicsFrom($list_data[$i]['partition_id'])
					];
				}
			}
		}

		return $list;
	}

	function getLesson($partition_id, $topic_id, $topic_level, $lesson_number) {
		$this->db->setTable(DB_TABLE_PREFIX . 'exercises_basic_' . Lang::$lang);
		$list_data = $this->db->getLines('partition_id, topic_id');
	}

}