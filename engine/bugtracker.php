<?php

class Bugtracker {

	/**
	 * Add report to the feed.
	 * @param $parameters
	 * @param $fromId
	 * @return void
	 */
	public static function addPost($parameters = [], $fromId) {
		Database::setCurrentTable('feedbacks');
		if (isset($parameters['reply_to'])) {
			return self::addComment($parameters, $fromId);
		} else {
			$time = time();
			if (!isset($parameters['files'])) {
				$parameters['files'] = '';
			}
			return Database::append("
				DEFAULT, 
				'bug', 
				{$fromId}, 
				DEFAULT, 
				'{$parameters['title']}', 
				{$time}, 
				'{$parameters['description']}', 
				'{$parameters['fact_result']}', 
				'{$parameters['needed_result']}', 
				'not_seen', 
				'{$parameters['files']}'
			");
		}
	}

	/**
	 * Add comment to the report.
	 * @param $parameters
	 * @param $fromId
	 * @return comment object without 'id' field
	 */
	public static function addComment($parameters=[], $fromId) {
		$comment_publisher = new User($fromId);
		Database::setCurrentTable('feedbacks');
		$post = Database::getLines('type', "`id`='{$parameters['reply_to']}'");
		if (isset($post[0])) {
			$post = $post[0];
			if (0 === strcmp($post['type'], 'bug')) { 
				$time = time();
				$new_status = 'DEFAULT';
				if (isset($parameters['new_status'])) {
					if ($comment_publisher->hasRightTo('moderateBugs')) {
						$new_status = $parameters['new_status'];
						self::changeStatus($parameters['reply_to'], $parameters['new_status']);
					}
				}
				Database::setCurrentTable('feedbacks');
				$status_to_write = $new_status;
				if (strcmp($new_status, 'DEFAULT') !== 0) {
					$status_to_write = '\'' . $new_status . '\'';
				}
				Database::append("
					DEFAULT, 
					'comment', 
					{$fromId}, 
					{$parameters['reply_to']}, 
					DEFAULT, 
					{$time}, 
					'{$parameters['text']}', 
					DEFAULT, 
					DEFAULT, 
					{$status_to_write}, 
					''
				");
			} else {
				ErrorList::addError(301);
				return false;
			}
		} else {
			ErrorList::addError(301);
			return false;
		}
		$return_object = [
			#TODO: add id to the object
			//'id' => intval($post[$i]['id']),
			'from_id' => $comment_publisher->id,
			'from_name' => $comment_publisher->getName(),
			'from_profile_picture' => $comment_publisher->getProfilePicture(),
			'time' => $time,
			'date' => (new Date($time))->message_time_format_w_time(),
			'text' => $parameters['text'],
			'files' => ''
		];
		if (strcmp($new_status, 'DEFAULT') !== 0) {
			$return_object['new_status'] = $new_status;
		}
		return $return_object;
	}

	/**
	 * Change the status of the report to one of the following:
	 * not_seen, open, in_process, closed, waiting
	 * @param $postId
	 * @param $newStatus
	 * @return true if everything is ok and false if something went wrong
	 */
	private static function changeStatus($postId, $newStatus) {
		$allowed_status_values = ['not_seen', 'open', 'in_process', 'closed', 'waiting', 'fixed'];
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
			return Database::replace(
				'status', 
				"'{$newStatus}'", 
				'`id`=' . $postId
			);
		} else {
			ErrorList::addError(108);
			return false;
		}
	}

	/**
	 * Get object of the post with specified id.
	 * @param $postId
	 * @param $addComments
	 * @param $user
	 * @return array or boolean
	 */
	public static function getPost($postId, $addComments=false, $user=false) {
		Database::setCurrentTable('feedbacks');
		$post = Database::getLines('*', "`id`={$postId}" . ($addComments ? " OR `reply_to`={$postId}":''));
		if (strcmp($post[0]['type'], 'comment') === 0) {
			ErrorList::addError(301);
			return;
		}
		if (isset($post[0])) {
			$post_object = [
				'post_id' => intval($post[0]['id']), 
				'from_id' => intval($post[0]['from_id']),
				'title' => $post[0]['title'],
				'time' => intval($post[0]['time']),
				'date' => (new Date($post[0]['time']))->message_time_format_w_time(),
				'description' => $post[0]['description'],
				'fact_result' => $post[0]['fact_result'],
				'needed_result' => $post[0]['needed_result'],
				'status' => $post[0]['status']
			];
			if ($user !== false) {
				if ($user->hasRightTo('moderateBugs') and strcmp($post[0]['status'], 'not_seen') === 0) {
					self::changeStatus($postId, 'open');
					$post_object['status'] = 'open';
				}
				$post_object['can_reply'] = true;
			}
			if (isset($post[0]['files'])) {
				$post_object['files'] = $post[0]['files'];
			}
			if (isset($post[1]) and $addComments) {
				$post_object['comments'] = [];
				for ($i = 1; $i < count($post); $i++) {
					$current_comment_publisher = new User($post[$i]['from_id']);
					$post_object['comments'][] = [
						'id' => intval($post[$i]['id']),
						'from_id' => intval($post[$i]['from_id']),
						'from_name' => $current_comment_publisher->getName(),
						'from_profile_picture' => $current_comment_publisher->getProfilePicture(),
						'time' => intval($post[$i]['time']),
						'date' => (new Date($post[$i]['time']))->message_time_format_w_time(),
						'text' => $post[$i]['description'],
						'files' => $post[$i]['files']
					];
					if (isset($post[$i]['status'])) {
						$post_object['comments'][$i - 1]['new_status'] = $post[$i]['status'];
					}
				}
			}
		} else {
			ErrorList::addError(301);
			return false;
		}
		return $post_object;
	}

	/**
	 * Get bugtracker's feed
	 * @return array of arrays of arrays or boolean
	 */
	public static function getFeed() {
		Database::setCurrentTable('feedbacks');
		$post = Database::getLines('*', '`type`=\'bug\' ORDER BY id DESC');
		if (isset($post[0])) {
			for ($i = 0; $i < count($post); $i++) {
				$current_report_publisher_name = (new User($post[$i]['from_id']))->getName();
				Database::setCurrentTable('feedbacks');
				$post_object[] = [
					'post_id' => intval($post[$i]['id']), 
					'from_id' => intval($post[$i]['from_id']),
					'from_name' => $current_report_publisher_name,
					'title' => $post[$i]['title'],
					'time' => intval($post[$i]['time']),
					'date' => (new Date($post[$i]['time']))->message_time_format_w_time(),
					'description' => $post[$i]['description'],
					'fact_result' => $post[$i]['fact_result'],
					'needed_result' => $post[$i]['needed_result'],
					'status' => $post[$i]['status'],
					'comments_count' => count($res = Database::getLines('id', "`reply_to`={$post[$i]['id']}"))
				];
				//var_dump($res);
			}
		} else {
			ErrorList::addError(301);
			return false;
		}
		return $post_object;
	}
}