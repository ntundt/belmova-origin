<?php

class Debug {
	public static $info;
	public static function addInfo($info) {
		self::$info[] = $info;
	}
}