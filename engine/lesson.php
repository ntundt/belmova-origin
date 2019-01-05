<?php

class LessonsList {

	public static function setLesson($partitionId, $topicId, $topicLevel, $lessonNumber, $lessonObject) {
		if (strcmp(gettype($lessonObject), 'array') === 0) {
			$lessonObject = json_encode($lessonObject, JSON_UNESCAPED_UNICODE);
		}

		DatabaseQueriesProcessor::setCurrentTable('exercises_basic_' . Lang::$lang);
		$where = "`partition_id`={$partitionId} AND `topic_id`={$topicId} AND `topic_level`={$topicLevel} AND `lesson_number`={$lessonNumber}";
		$lesson_isset = DatabaseQueriesProcessor::getLines('exercises', $where);
		if (isset($lesson_isset[0]['exercises'])) {
			return DatabaseQueriesProcessor::replace('exercises', '\''.$lessonObject.'\'', $where);
		} else {
			return DatabaseQueriesProcessor::append("DEFAULT, {$partitionId}, {$topicId}, {$topicLevel}, {$lessonNumber}, '{$lessonObject}'");
		}
	}

	public static function getPartitonName($partitionId) {
		$last_table = DatabaseQueriesProcessor::$current_table;

		DatabaseQueriesProcessor::setCurrentTable('exercises_partitions_' . Lang::$lang);
		$name = DatabaseQueriesProcessor::getLines('name', "`id`={$partitionId}")[0]['name'];

		DatabaseQueriesProcessor::$current_table = $last_table;
		return $name;
	}

	public static function getTopicName($partitionId, $topicId) {
		$last_table = DatabaseQueriesProcessor::$current_table;

		DatabaseQueriesProcessor::setCurrentTable('exercises_topics_' . Lang::$lang);
		$name = DatabaseQueriesProcessor::getLines('name', "`partition_id`={$partitionId} AND `id`={$topicId}")[0]['name'];

		DatabaseQueriesProcessor::$current_table = $last_table;
		return $name;
	}

	public static function getTopicsFrom($partitionId) {
		$last_table = DatabaseQueriesProcessor::$current_table;

		DatabaseQueriesProcessor::setCurrentTable('exercises_basic_' . Lang::$lang);
		$list_data = DatabaseQueriesProcessor::getLines('topic_id', "`partition_id` = {$partitionId}");

		$topics = Arr::filterElementWithSameParameter('topic_id', $list_data);
		for ($i = 0; $i < count($topics); $i++) {
			$topics[$i]['topic_name'] = self::getTopicName($partitionId, $topics[$i]['topic_id']);
			$topics[$i]['topic_id'] = intval($topics[$i]['topic_id']);
		} 

		DatabaseQueriesProcessor::$current_table = $last_table;
		return $topics;
	}

	public static function lessonIsSet($partitionId, $topicId, $topicLevel, $lessonNumber) {
		$last_table = DatabaseQueriesProcessor::$current_table;

		DatabaseQueriesProcessor::setCurrentTable('exercises_basic_' . Lang::$lang);
		$list_data = DatabaseQueriesProcessor::getLines('id', "`partition_id`={$partitionId} AND `topic_id`={$topicId} AND `topic_level`={$topicLevel} AND `lesson_number`={$lessonNumber}");

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

	public static function getLesson($partitionId, $topicId, $topicLevel, $lessonNumber) {
		DatabaseQueriesProcessor::setCurrentTable('exercises_basic_' . Lang::$lang);
		$lesson = DatabaseQueriesProcessor::getLines('exercises', "`partition_id`={$partitionId} AND `topic_id`={$topicId} AND `topic_level`={$topicLevel} AND `lesson_number`={$lessonNumber}");

		if (isset($lesson[0]['exercises'])) {
			$exercises = json_decode($lesson[0]['exercises'], true);
			$lesson_object = ['exercises' => $exercises, 'exercises_count' => count($exercises), 'topic_name' => self::getTopicName($partition_id, $topic_id)];
			return $lesson_object;
		} else {
			return false;
		}
	}

}
