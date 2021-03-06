<?php

class User {
	
	public $id;

	public function __construct($id) {
		return $this->setUserId($id);
	}

	public function setUserId($id) {
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

	# TODO: Add this method's arguments processing.
	public function getName($onlyName = false, $case = 'nom') { 
		if (!$this->id) {
			return false;
		}

		Database::setCurrentTable('users');
		$name_data = Database::getLines('fname, lname', "`id`={$this->id}");
		Database::setPreviousTable();
		
		return (!$onlyName) ? ($name_data[0]['fname'] . ' ' . $name_data[0]['lname']) : $name_data[0]['fname'];
	}

	public function isLogged() {
		return (!is_null($this->id));
	}

	public function hasRightTo($what) {
		if (!$this->id) {
			return false;
		}

		Database::setCurrentTable('users_rights');
		$right = Database::getLines('has', "
			`uid` = {$this->id} AND 
			`type` = '{$what}'
		");

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
		$progress = Database::getLines('topic_level, lessons_count', "
			`uid` = {$this->id} AND 
			`partition_id` = {$partitionId} AND 
			`topic_id` = {$topicId}
		");
		$mb_lesson_object = LessonsList::getLesson($partitionId, $topicId, $topicLevel, $lessonNumber);
		if (0 !== strcmp(gettype($mb_lesson_object), 'array')) {
			ErrorList::addError(202);
			return false;
		}
		$xp = count($mb_lesson_object['exercises']);

		Database::setCurrentTable('users_progress');
		if (isset($progress[0])) {
			if ($progress[0]['lessons_count'] === $lessonNumber - 1 and $topicLevel === $progress[0]['topic_level']) {
				Database::replace('lessons_count', $lessonNumber, '
					`uid` = {$this->id} AND 
					`partition_id` = {$partitionId} AND 
					`topic_id` = {$topicId}
				');
			} else if (1 === $lessonNumber and $topicLevel === $progress[0]['topic_level'] + 1) {
				Database::replace('lessons_count', $lessonNumber, '
					`uid` = {$this->id} AND 
					`partition_id` = {$partitionId} AND 
					`topic_id` = {$topicId}
				');
				Database::replace('topic_level', $topicLevel, '
					`uid` = {$this->id} AND 
					`partition_id` = {$partitionId} AND 
					`topic_id` = {$topicId}
				');
			}
 		} else if (1 === $topicLevel and 1 === $lessonNumber) {
 			Database::append("DEFAULT, {$this->id}, {$partitionId}, {$topicId}, {$topicLevel}, {$lessonNumber}");
 		}
 		return [
 			'current_xp' => $this->addXp($xp),
 			'added_xp' => $xp
 		];
	}

	private function markLessonPassed($partitionId, $topicId, $topicLevel, $lessonNumber) {
		Database::setTable('users_progress');
		$where = "
			`uid` = {$this->id} AND 
			`partition_id` = {$partitionId} AND 
			`topic_id` = {$topicId} AND
			`topic_level` = {$topic_level} AND 
			`lesson_number` = {$lessonNumber}
		";
		$progress = Database::getLines('passed', $where);

		if (isset($progress[0]['passed'])) {
			if (1 != $progress[0]['passed']) {
				Database::replace('passed', 1, $where);
			}
		} else {
			Database::append("
				{$this->id}, 
				{$partitionId}, 
				{$topicId}, 
				{$topic_level}, 
				{$lessonNumber}, 
				1
			");
		}
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

		for ($part_index = 0; $part_index < count($list['partitions']); $part_index++) {
			$partition = &$list['partitions'][$part_index];
			for ($topic_index = 0; $topic_index < count($partition['topics']); $topic_index++) {
				$topic = &$partition['topics'][$topic_index];
				$progress = Database::getLines('topic_level, lesson_number', "
					`uid` = {$this->id} 
					AND `partition_id` = {$partition['partition_id']} 
					AND `topic_id` = {$topic['topic_id']}
				");
				$whatIsNext = [
					'partition_id' => $partition['partition_id'],
					'topic_id' => $topic['topic_id'],
					'topic_level' => 1,
					'lesson_number' => 1
				];

				$topic['passed'] = false;
				if (isset($progress[0])) {
					if (LessonsList::lessonIsSet(
						$partition['partition_id'], 
						$topic['topic_id'], 
						intval($progress[0]['topic_level']), 
						intval($progress[0]['lesson_number']) + 1
					)) {
						$whatIsNext['topic_level'] = intval($progress[0]['topic_level']);
						$whatIsNext['lesson_number'] = intval($progress[0]['lesson_number']) + 1;
					} else if (LessonsList::lessonIsSet(
						$partition['partition_id'], 
						$topic['topic_id'], 
						intval($progress[0]['topic_level']) + 1, 
						1
					)) {
						$whatIsNext['topic_level'] = intval($progress[0]['topic_level']) + 1;
						$whatIsNext['lesson_number'] = 1;
					} else {
						$whatIsNext['topic_level'] = intval($progress[0]['topic_level']);
						$whatIsNext['lesson_number'] = intval($progress[0]['lesson_number']) + 1;
						$topic['passed'] = true;
					}
					$topic['total_count'] = LessonsList::getLessonsCount(
						$partition['partition_id'], 
						$topic['topic_id'], 
						$progress[0]['topic_level']
					);
					$topic['passed_count'] = $progress[0]['lesson_number'] - 1;
				} else {
					$whatIsNext['topic_level'] = 1;
					$whatIsNext['lesson_number'] = 1;
					$topic['total_count'] = LessonsList::getLessonsCount(
						$partition['partition_id'], 
						$topic['topic_id'], 
						1
					);
					$topic['passed_count'] = 0;
				}
				$topic['next_id'] = LessonsList::getLessonId(
					$whatIsNext['partition_id'], 
					$whatIsNext['topic_id'], 
					$whatIsNext['topic_level'], 
					$whatIsNext['lesson_number']
				);
			} 
		}

		return $list;
	}


	public function getLesson($partitionId, $topicId, $topicLevel, $lessonNumber) {
		if (is_null($this->id)) {
			return false;
		}

		Database::setCurrentTable('users_progress');
		$progress = Database::getLines('topic_level, lessons_count', "
			`partition_id`={$partitionId} AND 
			`topic_id`={$topicId} AND 
			`uid`={$this->id}
		");

		$lesson_object = LessonsList::getLesson($partitionId, $topicId, $topicLevel, $lessonNumber);
		$lesson_object['completed'] = false;
		$lesson_object['not_reached'] = false;

		if (false !== $lesson_object) {
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

	/*
	 * Shortened methods
	 */

	public function finishLessonById($lessonId) {
		$address = LessonsList::getLessonAddress($lessonId);
		return $this->finishLesson(
			$address['partition_id'],
			$address['topic_id'],
			$address['topic_level'],
			$address['lesson_number']
		);
	}
	
	public function getLessonById($lessonId) {
		$address = LessonsList::getLessonAddress($lessonId);
		return $this->getLesson(
			$address['partition_id'],
			$address['topic_id'],
			$address['topic_level'],
			$address['lesson_number']
		);
	}

}
