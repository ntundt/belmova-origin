<?php

class Database {

	private static $mysqlobj;
	public static $current_table;
	private static $table_prefix;
	private static $previous_table = false;

	/**
	 * Initialize the database. Should be called when the script starts working.
	 * @param $host — Host on which database is being runned.
	 * @param $login — Database login
	 * @param $password — Database password
	 * @param $name — Database name
	 * @return void
	 */
	public static function init(string $host = DB_HOST, string $login = DB_LOGIN, string $password = DB_PASSWORD, string $name = DB_NAME) {
		self::$mysqlobj = new mysqli($host, $login, $password, $name);
		mysqli_set_charset(self::$mysqlobj, 'utf8');
	}

	/**
	 * Set prefix which is being inserted before every table's name
	 * @param $prefix
	 * @return void
	 */
	public static function setTablePrefix(string $prefix) {
		self::$table_prefix = $prefix;
	}

	/**
	 * Set the table you're gonna work with
	 * @param $table — That table's name
	 * @return void
	 */
	public static function setCurrentTable(string $table) {
		Debug::addInfo('Setting table name to ' . $table);
		if (strcmp($table, self::$current_table) !== 0) {
			self::$previous_table = self::$current_table;
			Debug::addInfo('Setting previous table to ' . self::$previous_table);
		}
		self::$current_table = $table;
	}

	/**
	 * Get from the database lines where condition is true
	 * @param $whatToGet — Columns' names you'd like to get. Like 'id, name, etc'.
	 * @param $condition — Condition which must be true on the row to return it. Like 'id=1' or '`this` and `that`'.
	 * @param $usePrefix ­— If you'd like class to insert the prefix before the table's name, give the method true here and false if wouldn't
	 * @return array or boolean
	 */
	public static function getLines(string $whatToGet, string $condition = '', $usePrefix = true) {
		$full_table_name = ($usePrefix ? self::$table_prefix : '') . self::$current_table;
		$sql_request = "SELECT {$whatToGet} FROM `{$full_table_name}`" . ((strcmp($condition, '') !== 0)?' WHERE ' . $condition:'') . ';';

		$sql_response = self::$mysqlobj->query($sql_request);

		if ($sql_response === false) {
			Debug::addInfo(var_export(self::$mysqlobj, true));
			Debug::addInfo($sql_request);
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

	/**
	 * Replace the value of $column to $newValue where $condition is true.
	 * @param $column
	 * @param $newValue
	 * @param $condition
	 * @return boolean
	 */
	public static function replace($column, $newValue, $condition, $usePrefix = true) {
		$full_table_name = ($usePrefix ? self::$table_prefix : '') . self::$current_table;
		$sql_request = "UPDATE `{$full_table_name}` SET `{$column}` = {$newValue} WHERE {$condition}";
		$sql_response = self::$mysqlobj->query($sql_request);
		if ($sql_response === false) {
			Debug::addInfo(var_export(self::$mysqlobj, true));
			Debug::addInfo($sql_request);
		}
		if (false === $sql_response) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Set self::$table value to the its previous value.
	 * @return void
	 */
	public static function setPreviousTable() {
		if (self::$previous_table !== false) {
			Debug::addInfo('Setting the table back to ' . self::$previous_table);
			self::setCurrentTable(self::$previous_table);
		}
	}

	/**
	 * Append the row to the end of the table.
	 * @param $whatToAppend — values to insert like "'some string', DEFAULT, 1"
	 * @return boolean
	 */
	public static function append(string $whatToAppend, $usePrefix = true) {
		$full_table_name = ($usePrefix ? self::$table_prefix : '') . self::$current_table;
		$sql_request = "INSERT INTO `{$full_table_name}` VALUES ({$whatToAppend});";
		$sql_response = self::$mysqlobj->query($sql_request);
		if ($sql_response === false) {
			Debug::addInfo(var_export(self::$mysqlobj, true));
			Debug::addInfo($sql_request);
			return false;
		}
		return ($sql_response === false ? false : true);
	}

	/**
	 * Just a query to the database.
	 * @param $query
	 * @return MySQL response object
	 */
	public static function query($query) {
		return self::$mysqlobj->query($query);
	}

}
