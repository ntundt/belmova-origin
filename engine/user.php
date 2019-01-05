<?php

class User {
	
	public $id;

	function __construct($id) {
		$this->id = $id;
	}

	function getAchievements() {
		
	}

	function addAchievement() {
		
	}

	function hasRightTo($what) {
		DatabaseQueriesProcessor::setCurrentTable('users_rights');
		$right = DatabaseQueriesProcessor::getLines('has', "`uid` = {$this->id} AND `type` = '{$what}'");

		if (isset($right[0])) {
			return ($right[0]['has'] == 1) ? true : false;
		} else {
			return false;
		}
	}

	function finishLesson($partitionId, $topicId, $topicLevel, $lessonNumber) {
		DatabaseQueriesProcessor::setCurrentTable('users_progress');
		$progress = DatabaseQueriesProcessor::getLines('topic_level, lessons_count', "`uid` = {$this->id} AND `partition_id` = {$partitionId} AND `topic_id` = {$topicId}");
		$mb_lesson_object = LessonsList::getLesson($partitionId, $topicId, $topicLevel, $lessonNumber);
		if (0 !== strcmp(gettype($mb_lesson_object), 'array')) {
			return new OutputError(202);
		}
		$xp = count($mb_lesson_object['exercises']);

		if (isset($progress[0])) {
			if ($progress[0]['lessons_count'] === $lessonNumber - 1 and $topicLevel === $progress[0]['topic_level']) {
				DatabaseQueriesProcessor::replace('lessons_count', $lessonNumber, '`uid` = {$this->id} AND `partition_id` = {$partitionId} AND `topic_id` = {$topicId}');
			} else if (1 === $lessonNumber and $topicLevel === $progress[0]['topic_level'] + 1) {
				DatabaseQueriesProcessor::replace('lessons_count', $lessonNumber, '`uid` = {$this->id} AND `partition_id` = {$partitionId} AND `topic_id` = {$topicId}');
				DatabaseQueriesProcessor::replace('topic_level', $topicLevel, '`uid` = {$this->id} AND `partition_id` = {$partitionId} AND `topic_id` = {$topicId}');
			}
 		} else if (1 === $topicLevel and 1 === $lessonNumber) {
 			DatabaseQueriesProcessor::append("DEFAULT, {$this->id}, {$partitionId}, {$topicId}, {$topicLevel}, {$lessonNumber}");
 		}
 		return ['new_xp' => $this->addXp($xp)];
	}

	function addXp($count) {
		DatabaseQueriesProcessor::setCurrentTable('users_xp');
		$uxp = DatabaseQueriesProcessor::getLines('xp', "`uid` = {$this->id}");
		$new_value = 0;
		if (isset($uxp[0])) {
			$new_value = $uxp[0]['xp'] + $count;
			DatabaseQueriesProcessor::replace('xp', $new_value, "`uid` = {$this->id}");
		} else {
			$new_value = $count;
			DatabaseQueriesProcessor::append("DEFAULT, {$this->id}, {$new_value}");
		}
		return $new_value;
	}

	function getLessonsList() {
		$list = LessonsList::toArray();
		DatabaseQueriesProcessor::setCurrentTable('users_progress');

		for ($i = 0; $i < count($list['partitions']); $i++) {
			for ($j = 0; $j < count($list['partitions'][$i]['topics']); $j++) {
				$pn = $list['partitions'][$i]['partition_id'];
				$tc = $list['partitions'][$i]['topics'][$j]['topic_id'];
				$progress = DatabaseQueriesProcessor::getLines('topic_level, lessons_count', "`uid` = {$this->id} AND `partition_id` = {$pn} AND `topic_id` = {$tc}");

				$list['partitions'][$i]['topics'][$j]['topic_passed'] = false;
				if (isset($progress[0])) {
					if (LessonsList::lessonIsSet($pn, $tc, intval($progress[0]['topic_level']), intval($progress[0]['lessons_count']) + 1)) {
						$list['partitions'][$i]['topics'][$j]['topic_level'] = intval($progress[0]['topic_level']);
						$list['partitions'][$i]['topics'][$j]['lesson_number'] = intval($progress[0]['lessons_count']) + 1;
					} else if (LessonsList::lessonIsSet($pn, $tc, intval($progress[0]['topic_level']) + 1, 1)) {
						$list['partitions'][$i]['topics'][$j]['topic_level'] = intval($progress[0]['topic_level']) + 1;
						$list['partitions'][$i]['topics'][$j]['lessons_count'] = 1;
					} else {
						$list['partitions'][$i]['topics'][$j]['topic_passed'] = true;
					}
				} else {
					$list['partitions'][$i]['topics'][$j]['topic_level'] = 1;
					$list['partitions'][$i]['topics'][$j]['lessons_count'] = 1;
				}
			} 
		}

		return $list;
	}

	function getLesson($partitionId, $topicId, $topicLevel, $lessonNumber) {
		DatabaseQueriesProcessor::setCurrentTable('users_progress');
		$progress = DatabaseQueriesProcessor::getLines('topic_level, lessons_count', "`partition_id`={$partitionId} AND `topic_id`={$topicId} AND `uid`={$this->id}");

		$lesson_object = LessonsList::getLesson($partitionId, $topicId, $topicLevel, $lessonNumber);

		if ($lesson_object !== false) {
			if (isset($progress[0]['topic_level'])) {
				if ($progress[0]['topic_level'] > $topicLevel) {
					$lesson_object['already_completed'] = true;
				} else if ($progress[0]['topic_level'] < $topicLevel) {
					$lesson_object['have_not_achieved'] = true;
				} else {
					if ($progress[0]['lessons_count'] > $lessonNumber) {
						$lesson_object['already_completed'] = true;
					} else if ($progress[0]['lessons_count'] < $lessonNumber) {
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
