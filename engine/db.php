<?php

class DB {

	private $mysqlobj;
	public $table;

	function __construct(string $host = DB_HOST, string $login = DB_LOGIN, string $password = DB_PASSWORD, string $name = DB_NAME) {
		$this->mysqlobj = new mysqli($host, $login, $password, $name);
		mysqli_set_charset($this->mysqlobj, 'utf8');
	}

	function setTable(string $table) {
		$this->table = $table;
	}

	function getLines(string $whatToGet, string $condition = '') {
		$request = "SELECT {$whatToGet} FROM `{$this->table}`" . ((strcmp($condition, '') !== 0)?' WHERE ' . $condition:'') . ';';

		$res = $this->mysqlobj->query($request);

        if ($res === false) {
        	var_dump($this->mysqlobj);
        	echo 'Request: ' . $request;
            return false;
        }

        $out = array();
        $j = 0;
        while ($line = $res->fetch_assoc()) {
        	$keys = array_keys($line);
            for ($i = 0; $i < count($keys); $i++) {
            	$out[$j][$keys[$i]] = $line[$keys[$i]];
            }
            $j++;
        }

        return $out;
	}

	function replace($column, $newValue, $condition) {
		$this->mysqlobj->query("UPDATE `{$this->table}` SET `{$column}` = {$newValue} WHERE {$condition}");
	}

	function append(string $whatToAppend) {
		$res = $this->mysqlobj->query("INSERT INTO `{$this->table}` VALUES ({$whatToAppend});");
		return ($res === false ? false : true);
	}

	function query($query) {
		return $this->mysqlobj->query($query);
	}

}