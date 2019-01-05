<?php

class DatabaseQueriesProcessor {

	private static $mysqlobj;
	public static $current_table;
	private static $table_prefix;
	private static $instance;

	public static function init(string $host = DB_HOST, string $login = DB_LOGIN, string $password = DB_PASSWORD, string $name = DB_NAME) {
		self::$mysqlobj = new mysqli($host, $login, $password, $name);
		mysqli_set_charset(self::$mysqlobj, 'utf8');
	}

	/*public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}*/

	public static function setTablePrefix(string $prefix) {
		self::$table_prefix = $prefix;
	}

	public static function setCurrentTable(string $table) {
		self::$current_table = $table;
	}

	public static function getLines(string $whatToGet, string $condition = '', $use_prefix = true) {
		$full_table_name = ($use_prefix ? self::$table_prefix : '') . self::$current_table;
		$sql_request = "SELECT {$whatToGet} FROM `{$full_table_name}`" . ((strcmp($condition, '') !== 0)?' WHERE ' . $condition:'') . ';';

		$sql_response = self::$mysqlobj->query($sql_request);

		if ($sql_response === false) {
			// debug
			var_dump(self::$mysqlobj);
			echo 'Request: ' . $sql_request;

			return false;
		}

		$out = array();
		$j = 0;
		while ($line = $sql_response->fetch_assoc()) {
			$keys = array_keys($line);
			for ($i = 0; $i < count($keys); $i++) {
				$out[$j][$keys[$i]] = $line[$keys[$i]];
			}
			$j++;
		}

		return $out;
	}

	public static function replace($column, $newValue, $condition, $use_prefix = true) {
		$full_table_name = ($use_prefix ? self::$table_prefix : '') . self::$current_table;
		$sql_request = "UPDATE `{$full_table_name}` SET `{$column}` = {$newValue} WHERE {$condition}";
		self::$mysqlobj->query($sql_request);
	}

	public static function append(string $whatToAppend, $use_prefix = true) {
		$full_table_name = ($use_prefix ? self::$table_prefix : '') . self::$current_table;
		$sql_response = self::$mysqlobj->query("INSERT INTO `{$full_table_name}` VALUES ({$whatToAppend});");
		if ($sql_response === false) {
			// debug
			var_dump(self::$mysqlobj);
			echo 'Request: ' . $sql_request;

			return false;
		}
		return ($sql_response === false ? false : true);
	}

	public static function query($query) {
		return self::$mysqlobj->query($query);
	}

}
