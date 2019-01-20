<?php

class LessonsList {

	public static function setLesson($partitionId, $topicId, $topicLevel, $lessonNumber, $lessonObject) {
		if (strcmp(gettype($lessonObject), 'array') === 0) {
			$lessonObject = json_encode($lessonObject, JSON_UNESCAPED_UNICODE);
		}

		Database::setCurrentTable('exercises_basic_' . Lang::$lang);
		$where = "`partition_id`={$partitionId} AND `topic_id`={$topicId} AND `topic_level`={$topicLevel} AND `lesson_number`={$lessonNumber}";
		$lesson_isset = Database::getLines('exercises', $where);
		if (isset($lesson_isset[0]['exercises'])) {
			return Database::replace('exercises', '\''.$lessonObject.'\'', $where);
		} else {
			return Database::append("DEFAULT, {$partitionId}, {$topicId}, {$topicLevel}, {$lessonNumber}, '{$lessonObject}'");
		}
	}

	public static function getPartitonName($partitionId) {
		$last_table = Database::$current_table;

		Database::setCurrentTable('exercises_partitions_' . Lang::$lang);
		$name = Database::getLines('name', "`id`={$partitionId}")[0]['name'];

		Database::$current_table = $last_table;
		return $name;
	}

	public static function getTopicName($partitionId, $topicId) {
		$last_table = Database::$current_table;

		Database::setCurrentTable('exercises_topics_' . Lang::$lang);
		$name = Database::getLines('name', "`partition_id`={$partitionId} AND `id`={$topicId}")[0]['name'];

		Database::$current_table = $last_table;
		return $name;
	}

	public static function getTopicsFrom($partitionId) {
		$last_table = Database::$current_table;

		Database::setCurrentTable('exercises_basic_' . Lang::$lang);
		$list_data = Database::getLines('topic_id', "`partition_id` = {$partitionId}");

		$topics = Arr::filterElementWithSameParameter('topic_id', $list_data);
		for ($i = 0; $i < count($topics); $i++) {
			$topics[$i]['topic_name'] = self::getTopicName($partitionId, $topics[$i]['topic_id']);
			$topics[$i]['topic_id'] = intval($topics[$i]['topic_id']);
		} 

		Database::$current_table = $last_table;
		return $topics;
	}

	public static function lessonIsSet($partitionId, $topicId, $topicLevel, $lessonNumber) {
		$last_table = Database::$current_table;

		Database::setCurrentTable('exercises_basic_' . Lang::$lang);
		$list_data = Database::getLines('id', "`partition_id`={$partitionId} AND `topic_id`={$topicId} AND `topic_level`={$topicLevel} AND `lesson_number`={$lessonNumber}");

		Database::$current_table = $last_table;

		return isset($list_data[0]);
	}

	public static function toArray() {
		Database::setCurrentTable('exercises_basic_' . Lang::$lang);
		$list_data = Database::getLines('partition_id, topic_id');
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
		Database::setCurrentTable('exercises_basic_' . Lang::$lang);
		$lesson = Database::getLines('exercises', "`partition_id`={$partitionId} AND `topic_id`={$topicId} AND `topic_level`={$topicLevel} AND `lesson_number`={$lessonNumber}");

		if (isset($lesson[0])) {
			$exercises = json_decode($lesson[0]['exercises'], true);
			$lesson_object = ['exercises' => $exercises, 'exercises_count' => count($exercises), 'topic_name' => self::getTopicName($partitionId, $topicId)];
			return $lesson_object;
		} else {
			ErrorList::addError(202);
			return false;
		}
	}

	public static function getLessonsCount($partitionId, $topicId, $topicLevel) {
		$resp = Database::query("SELECT SUM(partition_id = {$partitionId} and topic_id = {$topicId} and topic_level = {$topicLevel}) AS count FROM bm_exercises_basic_ru");
		return intval($resp->fetch_assoc()['count']);
	}

}
