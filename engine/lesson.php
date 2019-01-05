<?php

class LessonsList {

	public static function setLesson($partition_id, $topic_id, $topic_level, $lesson_number, $lesson_object) {
		if (strcmp(gettype($lesson_object), 'mixed') === 0) {
			$lesson_object = json_encode($lesson_object, JSON_UNESCAPED_UNICODE);
		}

		$where = "`partition_id`={$partition_id} AND `topic_id`={$topic_id} AND `topic_level`={$topic_level} AND `lesson_number`={$lesson_number}";
		$lesson_isset = DatabaseQueriesProcessor::getLines('exercises', $where);
		if (isset($lesson_isset[0]['exercises'])) {
			DatabaseQueriesProcessor::replace('exercises', $lesson_object, $where);
		} else {
			DatabaseQueriesProcessor::append("DEFAULT, {$partition_id}, {$topic_id}, {$topic_level}, {$lesson_number}, {$lesson_object}");
		}
	}

	public static function getPartitonName($partition_id) {
		$last_table = DatabaseQueriesProcessor::$current_table;

		DatabaseQueriesProcessor::setCurrentTable('exercises_partitions_' . Lang::$lang);
		$name = DatabaseQueriesProcessor::getLines('name', "`id`={$partition_id}")[0]['name'];

		DatabaseQueriesProcessor::$current_table = $last_table;
		return $name;
	}

	public static function getTopicName($partition_id, $topic_id) {
		$last_table = DatabaseQueriesProcessor::$current_table;

		DatabaseQueriesProcessor::setCurrentTable('exercises_topics_' . Lang::$lang);
		$name = DatabaseQueriesProcessor::getLines('name', "`partition_id`={$partition_id} AND `id`={$topic_id}")[0]['name'];

		DatabaseQueriesProcessor::$current_table = $last_table;
		return $name;
	}

	public static function getTopicsFrom($partition_id) {
		$last_table = DatabaseQueriesProcessor::$current_table;

		DatabaseQueriesProcessor::setCurrentTable('exercises_basic_' . Lang::$lang);
		$list_data = DatabaseQueriesProcessor::getLines('topic_id', "`partition_id` = {$partition_id}");

		$topics = Arr::filterElementWithSameParameter('topic_id', $list_data);
		for ($i = 0; $i < count($topics); $i++) {
			$topics[$i]['topic_name'] = self::getTopicName($partition_id, $topics[$i]['topic_id']);
			$topics[$i]['topic_id'] = intval($topics[$i]['topic_id']);
		} 

		DatabaseQueriesProcessor::$current_table = $last_table;
		return $topics;
	}

	public static function lessonIsSet($partition_id, $topic_id, $topic_level, $lesson_number) {
		$last_table = DatabaseQueriesProcessor::$current_table;

		DatabaseQueriesProcessor::setCurrentTable('exercises_basic_' . Lang::$lang);
		$list_data = DatabaseQueriesProcessor::getLines('id', "`partition_id`={$partition_id} AND `topic_id`={$topic_id} AND `topic_level`={$topic_level} AND `lesson_number`={$lesson_number}");

		DatabaseQueriesProcessor::$current_table = $last_table;

		return isset($list_data[0]);
	}

	public static function toArray() {
		DatabaseQueriesProcessor::setCurrentTable('exercises_basic_' . Lang::$lang);
		$list_data = DatabaseQueriesProcessor::getLines('partition_id, topic_id');
		$list = ['partitions' => []];

		$partitions = [];
		if (isset($list_data[0])) { 
			for ($i = 0; $i < count($list_data); $i++) {
				if (!Arr::isInArray($list_data[$i]['partition_id'], $partitions)) {
					$partitions[] = $list_data[$i]['partition_id'];
					$list['partitions'][] = [
						'partition_id' => intval($list_data[$i]['partition_id']),
						'partition_name' => self::getPartitonName($list_data[$i]['partition_id']),
						'topics' => self::getTopicsFrom($list_data[$i]['partition_id'])
					];
				}
			}
		}

		return $list;
	}

	public static function getLesson($partition_id, $topic_id, $topic_level, $lesson_number) {
		DatabaseQueriesProcessor::setCurrentTable('exercises_basic_' . Lang::$lang);
		$lesson = DatabaseQueriesProcessor::getLines('exercises', "`partition_id`={$partition_id} AND `topic_id`={$topic_id} AND `topic_level`={$topic_level} AND `lesson_number`={$lesson_number}");

		if (isset($lesson[0]['exercises'])) {
			$exercises = json_decode($lesson[0]['exercises'], true);
			$lesson_object = ['exercises' => $exercises, 'exercises_count' => count($exercises), 'topic_name' => self::getTopicName($partition_id, $topic_id)];
			return $lesson_object;
		} else {
			return false;
		}
	}

}
