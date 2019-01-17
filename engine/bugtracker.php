<?php

class Bugtracker {
	public static function addPost($parameters = [], $fromId) {
		Database::setCurrentTable('feedbacks');
		if (isset($parameters['reply_to'])) {
			return self::addComment($parameters, $fromId);
		} else {
			$time = time();
			if (!isset($parameters['files'])) {
				$parameters['files'] = '';
			}
			return Database::append("DEFAULT, 'bug', {$fromId}, DEFAULT, '{$parameters['title']}', {$time}, '{$parameters['description']}', '{$parameters['fact_result']}', '{$parameters['needed_result']}', 'not_seen', '{$parameters['files']}'");
		}
	}
	public static function addComment($parameters = [], $fromId) {
		$post = Database::getLines('type', "`id`='{$parameters['reply_to']}'");
		if (isset($post[0])) {
			$post = $post[0];
			if (0 === strcmp($post['type'], 'bugreport')) { 
				$time = time();
				return Database::append("DEFAULT, 'comment', {$fromId}, {$parameters['reply_to']}, DEFAULT, {$time}, '{$parameters['text']}', '{$parameters['fact_result']}', '{$parameters['needed_result']}', DEFAULT, '{$parameters['files']}'");
			} else {
				ErrorList::addError(301);
				return false;
			}
		} else {
			ErrorList::addError(301);
			return false;
		}
		return true;
	}
	private static function changeStatus($postId, $newStatus) {
		$allowed_status_values = ['not_seen', 'in_process', 'closed', 'waiting', 'fixed'];
		$anything_is_ok = true;
		for ($i = 0; $i < count($allowed_status_values); $i++) {
			if (0 === strcmp($allowed_status_values[$i], $newStatus)) {
				$anything_is_ok = true;
			}
		}
		if (!$anything_is_ok) {
			ErrorList::addError(108);
			return false;
		}

		Database::setCurrentTable('feedbacks');
		$post = Database::getLines('type', "`id`={$postId}");
		if (isset($post[0])) {
			$post = $post[0];
			if (0 === strcmp($post['type'], 'comment')) {
				return Database::replace('status', $newStatus);
			} else {
				ErrorList::addError(108);
				return false;
			}
		} else {
			ErrorList::addError(108);
			return false;
		}
	}
	public static function getPost($postId, $addComments = false) {
		Database::setCurrentTable('feedbacks');
		$post = Database::getLines('*', "`id`={$postId}" . ($addComments ? " OR `reply_to`={$postId}":''));
		if (isset($post[0])) {
			$post_object = [
				'post_id' => $post[0]['id'], 
				'from_id' => $post[0]['from_id'],
				'title' => $post[0]['title'],
				'time' => $post[0]['time'],
				'date' => date('j M y H:i', $post[0]['time']),
				'description' => $post[0]['description'],
				'fact_result' => $post[0]['fact_result'],
				'needed_result' => $post[0]['needed_result'],
				'status' => $post[0]['status'],
				'files' => $post[0]['files']
			];
			if (isset($post[1]) and $addComments) {
				$post_object['comments'] = [];
				for ($i = 1; $i < count($post); $i++) {
					$post_object['comments'][] = [
						'id' => $post[$i]['id'],
						'from_id' => $post[$i]['from_id'],
						'time' => $post[$i]['time'],
						'date' => date('j M y H:i', $post[$i]['time']),
						'text' => $post[$i]['description'],
						'new_status' => $post[$i]['status'],
						'files' => $post[$i]['files']
					];
				}
			}
		} else {
			ErrorList::addError(301);
			return false;
		}
		return $post_object;
	}
	public static function getFeed() {
		Database::setCurrentTable('feedbacks');
		$post = Database::getLines('*', '`type`=\'bug\' ORDER BY id DESC');
		if (isset($post[0])) {
			for ($i = 0; $i < count($post); $i++) {
				$current_report_publisher_name = (new User($post[$i]['from_id']))->getName();
				Database::setCurrentTable('feedbacks');
				$post_object[] = [
					'post_id' => $post[$i]['id'], 
					'from_id' => $post[$i]['from_id'],
					'from_name' => $current_report_publisher_name,
					'title' => $post[$i]['title'],
					'time' => $post[$i]['time'],
					'date' => date('j M y H:i', $post[$i]['time']),
					'description' => $post[$i]['description'],
					'fact_result' => $post[$i]['fact_result'],
					'needed_result' => $post[$i]['needed_result'],
					'status' => $post[$i]['status'],
					'comments_count' => count(Database::getLines('id', "`reply_to`={$post[$i]['id']}"))
				];
			}
		} else {
			ErrorList::addError(301);
			return false;
		}
		return $post_object;
	}
}