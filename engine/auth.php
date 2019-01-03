<?php

class Auth {

	private $db;

	function __construct() {
		require_once __DIR__ . '/db.php';
		require_once __DIR__ . '/../config.php';

		$this->db = new DB();
	}

	function userLogin($identificator, $password) {
		$password = md5($password);

		$this->db->setTable(DB_TABLE_PREFIX . 'users');
		$userinfo = $this->db->getLines('password_hash, id, email_verified', "`login` = '{$identificator}' OR `email`='{$identificator}'");

		if (isset($userinfo[0])) {
			if (strcmp($password, $userinfo[0]['password_hash']) === 0) {
				$sessionId = $this->generateToken(16);

				$this->db->setTable(DB_TABLE_PREFIX . 'sessions');
				$this->db->append("DEFAULT, {$userinfo[0]['id']}, '{$_SERVER['REMOTE_ADDR']}', '{$sessionId}'");

				return $sessionId;
			} else {
				return new OutputError(102);
			}
		} else {
			return new OutputError(101);
		}
	}

	function userRegister($login, $password, $first_name, $last_name, $email) {
		$password = md5($password);

		$table_name = DB_TABLE_PREFIX . 'users';
		$this->db->setTable($table_name);
		
		$uid = $this->db->query("SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '{DB_NAME}' AND TABLE_NAME = '{$table_name}';")->fetch_assoc()['AUTO_INCREMENT'];
		$res = $this->db->append("DEFAULT, '{$first_name}', '{$last_name}', '{$login}', '{$password}', '{$email}', DEFAULT");

		if ($res === false) {
			return new OutputError(103);
		} else {
			$email_confirmation_token = $this->generateToken(16);

			$this->db->setTable(DB_TABLE_PREFIX . 'email_confirmation');
			$this->db->append('DEFAULT, 0, {$uid}, {$email_confirmation_token}');

			return true;
		}
	}

	function passwordResetConfirm($email) {
		$this->db->setTable(DB_TABLE_PREFIX . 'users');
		$data = $this->db->getLines('id, fname, login', "`email` = {$email}");

		if (isset($data[0]['login'])) {
			$token = $this->generateToken(16);

			$this->db->setTable(DB_TABLE_PREFIX . 'email_confirmation');
			$this->db->append("DEFAULT, 1, {$data[0]['id']}, {$token}");

			MailSender::send($email, Lang::getText('passwordResetConfirmMailText', ['fname' => $data[0]['fname'], 'login' => $data['login'], 'link' => URL_TO_DIR . "/index.php?act=passwordReset&token={$token}", 'name' => SERVICE_NAME]));

			return true;
		} else {
			return new OutputError(104);
		}
	}

	function passwordResetProcess($token, $new_password) {
		$new_password = md5($new_password);

		$this->db->setTable(DB_TABLE_PREFIX . 'email_confirmation');
		$data = $this->db->getLines('uid', "`confirmation_token` = '{$token}'");

		if ($data !== false) {
			$this->db->setTable(DB_TABLE_PREFIX . 'users');
			$this->db->replace('password', $new_password, "`id`={$data[0]['uid']}");

			return true;
		} else {
			return new OutputError(106);
		}
	}

	function getUserId($session_id) {
		$this->db->setTable(DB_TABLE_PREFIX . 'sessions');
		$user_id = $this->db->getLines('uid', "`sid` = '{$session_id}'")[0]['uid'];
		
		if (!isset($user_id)) {
			return new OutputError(105);
		} 

		return $user_id;
	}

	private function generateToken($length) { 
		$chars ="abcdef1234567890";
		$string ='';
		for ($i = 0; $i < $length; $i++) {
			$string .= $chars[rand(0, strlen($chars) - 1)];
		}
		return $string;
	}

}