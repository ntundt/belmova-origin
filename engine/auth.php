<?php

class Auth {

	public static function authByVkId($vkId, $accessToken) {
		# TODO: Check given access_token

		$vkr = new VKRequest($accessToken);
		$vkr->setMethod('users.get');
		$vkresponse = $vkr->perform();

		Database::setCurrentTable('vk_auth');
		$vkd = Database::getLines('*', "`vk_user_id`={$vkId}");

		if (isset($vkd[0])) {
			if ($vkresponse['response'][0]['id'] == $vkd[0]['vk_user_id']) {
				$sid = self::generateToken(16);
				$time = time();

				Database::setCurrentTable('sessions');
				Database::append("DEFAULT, {$vkd[0]['vk_user_id']}, '{$_SERVER['REMOTE_ADDR']}', {$time}, '{$sid}'");
				return $sid;
			}
		} else if ($vkresponse['response'][0]['id'] == $vkId) {
			$table_name = 'users';

			$query = "
				SELECT 
					`AUTO_INCREMENT` 
				FROM 
					INFORMATION_SCHEMA.TABLES 
				WHERE 
					TABLE_SCHEMA = '".DB_NAME."' 
					AND TABLE_NAME = '".DB_TABLE_PREFIX."{$table_name}';
			";
			echo $query;
		
			$local_user_id = Database::query($query)->fetch_assoc()['AUTO_INCREMENT'];

			$res = Database::append("DEFAULT, '{$vkresponse['response'][0]['first_name']}', '{$vkresponse['response'][0]['last_name']}', 'id{$vkresponse['response'][0]['id']}', 'not_set', '/work/ui/default_profile_picture150x150.png', '', 1");

			Database::setCurrentTable('vk_auth');
			Database::append("DEFAULT, {$vkId}, {$local_user_id}, '{$accessToken}'");

			$sid = self::generateToken(16);
			$time = time();
			Database::setCurrentTable('sessions');
			Database::append("DEFAULT, {$vkId}, '{$_SERVER['REMOTE_ADDR']}', {$time}, '{$sid}'");
			return $sid;
		} else {
			return false;
		}
	}

	public static function userLogin($identificator, $password) {
		$password_hash = md5($password);

		Database::setCurrentTable('users');
		$userinfo = Database::getLines('password_hash, id, email_verified', "`login` = '{$identificator}' OR `email`='{$identificator}'");

		if (isset($userinfo[0])) {
			if (0 === strcmp($password_hash, $userinfo[0]['password_hash'])) {
				$sessionId = self::generateToken(16);
				$current_time = time();

				Database::setCurrentTable('sessions');
				Database::append("DEFAULT, {$userinfo[0]['id']}, '{$_SERVER['REMOTE_ADDR']}', {$current_time}, '{$sessionId}'");

				return $sessionId;
			} else {
				ErrorList::addError(102);
				return false;
			}
		} else {
			ErrorList::addError(101);
			return false;
		}
	}

	public static function userRegister($login, $password, $firstName, $lastName, $email) {
		$password = md5($password);

		$table_name = 'users';
		Database::setCurrentTable($table_name);
		
		$uid = Database::query("SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '{DB_NAME}' AND TABLE_NAME = '{$table_name}';")->fetch_assoc()['AUTO_INCREMENT'];
		$res = Database::append("DEFAULT, '{$firstName}', '{$lastName}', '{$login}', '{$password}', '{$email}', DEFAULT");

		if ($res === false) {
			ErrorList::addError(103);
			return false;
		} else {
			$email_confirmation_token = self::generateToken(16);

			Database::setCurrentTable('email_confirmation');
			Database::append('DEFAULT, 0, {$uid}, {$email_confirmation_token}');

			return true;
		}
	}

	public static function requestPasswordReset($email) {
		Database::setCurrentTable('users');
		$user_information = Database::getLines('id, fname, login', "`email` = {$email}");

		if (isset($user_information[0]['login'])) {
			$token = self::generateToken(16);

			Database::setCurrentTable('email_confirmation');
			Database::append("DEFAULT, 1, {$user_information[0]['id']}, {$token}");

			MailSender::send($email, Lang::getText('passwordResetConfirmMailText', [
				'fname' => $user_information[0]['fname'], 
				'login' => $user_information['login'], 
				'link' => URL_TO_DIR . "/passwordResetConfirm?token={$token}", 
				'name' => SERVICE_NAME
			]));

			return true;
		} else {
			ErrorList::addError(104);
			return false;
		}
	}

	public static function doPasswordReset($newPassword, $token) {
		$new_password_hash = md5($newPassword);

		Database::setCurrentTable('email_confirmation');
		$data = Database::getLines('uid', "`confirmation_token` = '{$token}'");

		if ($data !== false) {
			Database::setCurrentTable('users');
			Database::replace('password_hash', $new_password_hash, "`id`={$data[0]['uid']}");

			return true;
		} else {
			ErrorList::addError(106);
			return false;
		}
	}

	public static function getUserId($sessionId) {
		Database::setCurrentTable('sessions');
		$user_ids = Database::getLines('uid', "`sid` = '{$sessionId}'");
		
		if (!isset($user_ids[0])) {
			ErrorList::addError(105);
			return false;
		} 

		return $user_ids[0]['uid'];
	}

	private static function generateToken($length) { 
		$chars ="abcdef1234567890";
		$string ='';
		for ($i = 0; $i < $length; $i++) {
			$string .= $chars[rand(0, strlen($chars) - 1)];
		}
		return $string;
	}

}