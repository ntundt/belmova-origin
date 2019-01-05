<?php

class Arr {

	static function isInArray($element, $array) {
		for ($i = 0; $i < count($array); $i++) {
			if ($array[$i] == $element) {
				return true;
			}
		}
		return false;
	}

	static function getElementIndex($value, $array) {
		for ($i = 0; $i < count($array); $i++) {
			if ($array[$i] == $value) {
				return $i;
			}
		}
		return false;
	}

	static function findElementWith($element_name, $element_value, $array) {
		for ($i = 0; $i < count($array); $i++) {
			if ($array[$i][$element_name] == $element_value) {
				return $i;
			}
		} 
		return false;
	}

	static function getAllElements($parameter, $value, $array) {
		$elements = [];
		for ($i = 0; $i < count($array); $i++) {
			if ($array[$i][$parameter] == $value) {
				$elements[] = $array[$i];
			}
		}
		return $elements;
	}

	static function filterElementWithSameParameter($parameter, $array) {
		$unical_elements = []; 
		$elements = [];
		for ($i = 0; $i < count($array); $i++) {
			if (!in_array($array[$i][$parameter], $unical_elements)) {
				$elements[] = $array[$i];
				$unical_elements[] = $array[$i][$parameter];
			} 
		}
		return $elements;
	}

}
