<?php

class Auth {

	public static function userLogin($identificator, $password) {
		$password_hash = md5($password);

		DatabaseQueriesProcessor::setCurrentTable('users');
		$userinfo = DatabaseQueriesProcessor::getLines('password_hash, id, email_verified', "`login` = '{$identificator}' OR `email`='{$identificator}'");

		if (isset($userinfo[0])) {
			if (0 === strcmp($password_hash, $userinfo[0]['password_hash'])) {
				$sessionId = self::generateToken(16);
				$current_time = time();

				DatabaseQueriesProcessor::setCurrentTable('sessions');
				DatabaseQueriesProcessor::append("DEFAULT, {$userinfo[0]['id']}, '{$_SERVER['REMOTE_ADDR']}', {$current_time}, '{$sessionId}'");

				return $sessionId;
			} else {
				return new OutputError(102);
			}
		} else {
			return new OutputError(101);
		}
	}

	public static function userRegister($login, $password, $firstName, $lastName, $email) {
		$password = md5($password);

		$table_name = 'users';
		DatabaseQueriesProcessor::setCurrentTable($table_name);
		
		$uid = DatabaseQueriesProcessor::query("SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '{DB_NAME}' AND TABLE_NAME = '{$table_name}';")->fetch_assoc()['AUTO_INCREMENT'];
		$res = DatabaseQueriesProcessor::append("DEFAULT, '{$firstName}', '{$lastName}', '{$login}', '{$password}', '{$email}', DEFAULT");

		if ($res === false) {
			return new OutputError(103);
		} else {
			$email_confirmation_token = self::generateToken(16);

			DatabaseQueriesProcessor::setCurrentTable('email_confirmation');
			DatabaseQueriesProcessor::append('DEFAULT, 0, {$uid}, {$email_confirmation_token}');

			return true;
		}
	}

	public static function requestPasswordReset($email) {
		DatabaseQueriesProcessor::setCurrentTable('users');
		$user_information = DatabaseQueriesProcessor::getLines('id, fname, login', "`email` = {$email}");

		if (isset($user_information[0]['login'])) {
			$token = self::generateToken(16);

			DatabaseQueriesProcessor::setCurrentTable('email_confirmation');
			DatabaseQueriesProcessor::append("DEFAULT, 1, {$user_information[0]['id']}, {$token}");

			MailSender::send($email, Lang::getText('passwordResetConfirmMailText', [
				'fname' => $user_information[0]['fname'], 
				'login' => $user_information['login'], 
				'link' => URL_TO_DIR . "/index.php?act=passwordReset&token={$token}", 
				'name' => SERVICE_NAME
			]));

			return true;
		} else {
			return new OutputError(104);
		}
	}

	public static function doPasswordReset($newPassword, $token) {
		$new_password_hash = md5($newPassword);

		DatabaseQueriesProcessor::setCurrentTable('email_confirmation');
		$data = DatabaseQueriesProcessor::getLines('uid', "`confirmation_token` = '{$token}'");

		if ($data !== false) {
			DatabaseQueriesProcessor::setCurrentTable('users');
			DatabaseQueriesProcessor::replace('password_hash', $new_password_hash, "`id`={$data[0]['uid']}");

			return true;
		} else {
			return new OutputError(106);
		}
	}

	public static function getUserId($sessionId) {
		DatabaseQueriesProcessor::setCurrentTable('sessions');
		$user_id = DatabaseQueriesProcessor::getLines('uid', "`sid` = '{$sessionId}'")[0]['uid'];
		
		if (!isset($user_id)) {
			return new OutputError(105);
		} 

		return $user_id;
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