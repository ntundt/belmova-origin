<?php

class LessonsList {

	public static function setLesson($partitionId, $topicId, $topicLevel, $lessonNumber, $exerciseNumber, $lessonObject) {
		if (0 === strcmp(gettype($lessonObject), 'array')) {
			$lessonObject = json_encode($lessonObject, JSON_UNESCAPED_UNICODE);
		}

		Database::setCurrentTable('exercises_basic_' . Lang::$lang);
		$where = "
			`partition_id`={$partitionId} 
			AND `topic_id`={$topicId} 
			AND `topic_level`={$topicLevel} 
			AND `lesson_number`={$lessonNumber}
		";
		$lesson = Database::getLines('*', $where);
		if (isset($lesson[0])) {
			$lessonArray = json_decode($lesson[0]['exercises'], true);
			$lessonArray[$exerciseNumber] = json_decode($lessonObject, true);
			return Database::replace('exercises', '\'' . json_encode($lessonArray, JSON_UNESCAPED_UNICODE) . '\'', $where);
		} else {
			return Database::append("DEFAULT, {$partitionId}, {$topicId}, {$topicLevel}, {$lessonNumber}, '[{$lessonObject}]'");
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

	// public static function makeTree($array, $priority = [], $level = 0) {
	// 	$unique_values = [];
	// 	$result = [];
	// 	$result_tmp = [];
	// 	for ($i = 0; $i < count($array); $i++) {
	// 		if (!Arr::isInArray($array[$i][$priority[$level]], $unique_values)) {
	// 			$unique_values[] = $array[$i][$priority[$level]];
	// 		}
	// 	}
	// 	for ($i = 0; $i < count($unique_values); $i++) {
	// 		$result_tmp[] = isset($priority[$level + 1]) ? Arr::getAllElements($priority[$level], $unique_values[$i], self::arraySplit($array, $priority[$level + 1])) : $array;
	// 	}
	// 	var_dump($result_tmp);

	// 	//$array = arraySplit($array, $priority[$level]);
	// 	return $result;
	// }

	// private static function arraySplit($array, $property) {
	// 	$unique_values = [];
	// 	$splt = [];
	// 	for ($i = 0; $i < count($array); $i++) {
	// 		if (!Arr::isInArray($array[$i][$property], $unique_values)) {
	// 			$unique_values[] = $array[$i][$property];
	// 		}
	// 		$splt[$array[$i][$property]][] = $array[$i];
	// 	}
	// 	return $splt;
	// }

	// private static function bubbleSort(&$array, $property) {
	// 	for ($i = 0; $i < count($array); $i++) {
	// 		for ($j = 0; $j < count($array) - $i - 1; $j++) {
	// 			if ($array[$j][$property] > $array[$j + 1][$property]) {
	// 				self::arraySwap($array, $j, $j + 1);
	// 			}
	// 		}
	// 	}
	// }

	// private static function arraySwap(&$array, $a, $b){
	// 	list($array[$a],$array[$b]) = array($array[$b], $array[$a]);
	// }

	public static function getTree() {
		$tree = Database::query("SELECT * from `bm_exercises_basic_ru` order by `partition_id` asc, `topic_id` asc, `topic_level` asc, `lesson_number` asc");
		$result = [];
		while ($line = $tree->fetch_assoc()) {
			$result['partitions'][$line['partition_id'] - 1]['topics'][$line['topic_id'] - 1]['levels'][$line['topic_level'] - 1]['lessons'][$line['lesson_number'] - 1]['exercises'] = $line['exercises'];
		}
		for ($i = 0; $i < count($result['partitions']); $i++) {
			$part = &$result['partitions'][$i];
			$part['partition_id'] = $i + 1;
			$part['partition_name'] = self::getPartitonName($part['partition_id']);
			for ($j = 0; $j < count($part['topics']); $j++) {
				$topic = &$part['topics'][$j];
				$topic['topic_id'] = $j + 1;
				$topic['topic_name'] = self::getTopicName($part['partition_id'], $topic['topic_id']);
				for ($k = 0; $k < count($topic['levels']); $k++) {
					$level = &$topic['levels'][$k];
					for ($l = 0; $l < count($level['lessons']); $l++) {
						$lesson = &$level['lessons'][$l];
						$lesson['exercises'] = json_decode($lesson['exercises'], true);
					}
				}
			}
		}
		return $result;
	}

	public static function getLessonsCount($partitionId, $topicId, $topicLevel) {
		$resp = Database::query("SELECT SUM(partition_id = {$partitionId} and topic_id = {$topicId} and topic_level = {$topicLevel}) AS count FROM bm_exercises_basic_ru");
		return intval($resp->fetch_assoc()['count']);
	}

}
