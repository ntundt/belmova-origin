<?php

class Database {

	private static $mysqlobj;
	public static $current_table;
	private static $table_prefix;
	private static $previous_table = false;

	public static function init(string $host = DB_HOST, string $login = DB_LOGIN, string $password = DB_PASSWORD, string $name = DB_NAME) {
		self::$mysqlobj = new mysqli($host, $login, $password, $name);
		mysqli_set_charset(self::$mysqlobj, 'utf8');
	}

	public static function setTablePrefix(string $prefix) {
		self::$table_prefix = $prefix;
	}

	public static function setCurrentTable(string $table) {
		if (strcmp($table, self::$previous_table) !== 0) {
			self::$previous_table = self::$current_table;
		}
		self::$current_table = $table;
	}

	public static function getLines(string $whatToGet, string $condition = '', $usePrefix = true) {
		$full_table_name = ($usePrefix ? self::$table_prefix : '') . self::$current_table;
		$sql_request = "SELECT {$whatToGet} FROM `{$full_table_name}`" . ((strcmp($condition, '') !== 0)?' WHERE ' . $condition:'') . ';';

		$sql_response = self::$mysqlobj->query($sql_request);

		if ($sql_response === false) {
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

	public static function replace($column, $newValue, $condition, $usePrefix = true) {
		$full_table_name = ($usePrefix ? self::$table_prefix : '') . self::$current_table;
		$sql_request = "UPDATE `{$full_table_name}` SET `{$column}` = {$newValue} WHERE {$condition}";
		$sql_response = self::$mysqlobj->query($sql_request);
		if ($sql_response === false) {
			var_dump(self::$mysqlobj);
			echo 'Request: ' . $sql_request;
		}
		Debug::addInfo(var_export(self::$mysqlobj, true));
		Debug::addInfo($sql_request);
		if (false === $sql_response) {
			return false;
		} else {
			return true;
		}
	}

	public static function setPreviousTable() {
		if (self::$previous_table !== false) {
			self::setCurrentTable(self::$previous_table);
		}
	}

	public static function append(string $whatToAppend, $usePrefix = true) {
		$full_table_name = ($usePrefix ? self::$table_prefix : '') . self::$current_table;
		$sql_request = "INSERT INTO `{$full_table_name}` VALUES ({$whatToAppend});";
		$sql_response = self::$mysqlobj->query($sql_request);
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
