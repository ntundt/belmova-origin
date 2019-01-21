<?php

class User {
	
	public $id;

	public function __construct($id) {
		$this->id = $id;
		if (!$this->id) {
			return false;
		}

		Database::setCurrentTable('users');
		if (!isset(Database::getLines('id', "`id`={$this->id}")[0])) {
			unset($this->id);
			return false;
		}
	}

	public function getProfilePicture() {
		if (!$this->id) {
			return false;
		}
		
		Database::setCurrentTable('users');
		$picture = Database::getLines('profile_picture', "`id`={$this->id}");
		return $picture[0]['profile_picture'];
		Database::setPreviousTable();
	}

	public function getAchievements() {
		
	}

	public function addAchievement() {
		
	}

	public function getName() {
		if (!$this->id) {
			return false;
		}

		Database::setCurrentTable('users');
		$name_data = Database::getLines('fname, lname', "`id`={$this->id}");
		Database::setPreviousTable();
		
		return $name_data[0]['fname'] . ' ' . $name_data[0]['lname'];
	}

	public function hasRightTo($what) {
		if (!$this->id) {
			return false;
		}

		Database::setCurrentTable('users_rights');
		$right = Database::getLines('has', "`uid` = {$this->id} AND `type` = '{$what}'");

		if (isset($right[0])) {
			return ($right[0]['has'] == 1) ? true : false;
		} else {
			ErrorList::addError(109);
			return false;
		}
	}

	public function finishLesson($partitionId, $topicId, $topicLevel, $lessonNumber) {
		if (!$this->id) {
			return false;
		}

		Database::setCurrentTable('users_progress');
		$progress = Database::getLines('topic_level, lessons_count', "`uid` = {$this->id} AND `partition_id` = {$partitionId} AND `topic_id` = {$topicId}");
		$mb_lesson_object = LessonsList::getLesson($partitionId, $topicId, $topicLevel, $lessonNumber);
		if (0 !== strcmp(gettype($mb_lesson_object), 'array')) {
			ErrorList::addError(202);
			return false;
		}
		$xp = count($mb_lesson_object['exercises']);

		Database::setCurrentTable('users_progress');
		if (isset($progress[0])) {
			if ($progress[0]['lessons_count'] === $lessonNumber - 1 and $topicLevel === $progress[0]['topic_level']) {
				Database::replace('lessons_count', $lessonNumber, '`uid` = {$this->id} AND `partition_id` = {$partitionId} AND `topic_id` = {$topicId}');
			} else if (1 === $lessonNumber and $topicLevel === $progress[0]['topic_level'] + 1) {
				Database::replace('lessons_count', $lessonNumber, '`uid` = {$this->id} AND `partition_id` = {$partitionId} AND `topic_id` = {$topicId}');
				Database::replace('topic_level', $topicLevel, '`uid` = {$this->id} AND `partition_id` = {$partitionId} AND `topic_id` = {$topicId}');
			}
 		} else if (1 === $topicLevel and 1 === $lessonNumber) {
 			Database::append("DEFAULT, {$this->id}, {$partitionId}, {$topicId}, {$topicLevel}, {$lessonNumber}");
 		}
 		return ['new_xp' => $this->addXp($xp)];
	}

	public function addXp($count) {
		if (!$this->id) {
			return false;
		}

		Database::setCurrentTable('users_xp');
		$uxp = Database::getLines('xp', "`uid` = {$this->id}");
		$new_value = 0;
		if (isset($uxp[0])) {
			$new_value = $uxp[0]['xp'] + $count;
			Database::replace('xp', $new_value, "`uid` = {$this->id}");
		} else {
			$new_value = $count;
			Database::append("DEFAULT, {$this->id}, {$new_value}");
		}
		return $new_value;
	}

	public function getLessonsList() {
		if (!$this->id) {
			return false;
		}

		$list = LessonsList::toArray();
		Database::setCurrentTable('users_progress');

		for ($i = 0; $i < count($list['partitions']); $i++) {
			for ($j = 0; $j < count($list['partitions'][$i]['topics']); $j++) {
				$pn = $list['partitions'][$i]['partition_id'];
				$tc = $list['partitions'][$i]['topics'][$j]['topic_id'];
				$progress = Database::getLines('topic_level, lessons_count', "`uid` = {$this->id} AND `partition_id` = {$pn} AND `topic_id` = {$tc}");
				$list['partitions'][$i]['topics'][$j]['lessons_total_count'] = LessonsList::getLessonsCount($pn, $tc, $progress[0]['topic_level']);

				$list['partitions'][$i]['topics'][$j]['topic_passed'] = false;
				if (isset($progress[0])) {
					if (LessonsList::lessonIsSet($pn, $tc, intval($progress[0]['topic_level']), intval($progress[0]['lessons_count']) + 1)) {
						$list['partitions'][$i]['topics'][$j]['topic_level'] = intval($progress[0]['topic_level']);
						$list['partitions'][$i]['topics'][$j]['lessons_count'] = intval($progress[0]['lessons_count']) + 1;
					} else if (LessonsList::lessonIsSet($pn, $tc, intval($progress[0]['topic_level']) + 1, 1)) {
						$list['partitions'][$i]['topics'][$j]['topic_level'] = intval($progress[0]['topic_level']) + 1;
						$list['partitions'][$i]['topics'][$j]['lessons_count'] = 1;
					} else {
						$list['partitions'][$i]['topics'][$j]['topic_passed'] = true;
						$list['partitions'][$i]['topics'][$j]['topic_level'] = 1;
						$list['partitions'][$i]['topics'][$j]['lessons_count'] = 1;
					}
				} else {
					$list['partitions'][$i]['topics'][$j]['topic_level'] = 1;
					$list['partitions'][$i]['topics'][$j]['lessons_count'] = 1;
				}
			} 
		}

		return $list;
	}

	public function getLesson($partitionId, $topicId, $topicLevel, $lessonNumber) {
		if (is_null($this->id)) {
			return false;
		}

		Database::setCurrentTable('users_progress');
		$progress = Database::getLines('topic_level, lessons_count', "`partition_id`={$partitionId} AND `topic_id`={$topicId} AND `uid`={$this->id}");

		$lesson_object = LessonsList::getLesson($partitionId, $topicId, $topicLevel, $lessonNumber);
		if ($lesson_object === false) {
			return false;
		}
		$lesson_object['completed'] = false;
		$lesson_object['not_reached'] = false;

		if ($lesson_object !== false) {
			if (isset($progress[0]['topic_level'])) {
				if ($topicLevel < $progress[0]['topic_level']) {
					$lesson_object['completed'] = true;
				} else if ($topicLevel > $progress[0]['topic_level']) {
					$lesson_object['not_reached'] = true;
				} else {
					if ($lessonNumber <= $progress[0]['lessons_count']) {
						$lesson_object['completed'] = true;
					} else if ($lessonNumber > $progress[0]['lessons_count'] + 1) {
						$lesson_object['not_reached'] = true;
					}
				}
			} else {
				$lesson_object['not_reached'] = true;
			}
		} else {
			ErrorList::addError(202);
			return false;
		}

		return $lesson_object;
	}

	public function getSettings() {
		
	}

	public function setSettings($settings) {
		
	}

}
