<?php

class Date {
	function __construct($time) {
		$this->time = $time;
		$this->results = $this->transformTime($this->time);
	}

	/**
	 * Get everything class could need
	 * @param $time
	 * @return void
	 */
	function transformTime($time) {
		$date = explode(';', date('d;N;z;W;m;t;Y;H;i;s;O', $time));
		$this->date_object = [
			'day_of_month' => intval($date[0]),
			'day_of_week' => intval($date[1]),
			'day_of_year' => intval($date[2]),
			'week_of_year' => intval($date[3]),
			'month' => intval($date[4]),
			'days_in_month' => intval($date[5]),
			'year' => intval($date[6]),
			'hour' => intval($date[7]),
			'minute' => intval($date[8]),
			'second' => intval($date[9]),
			'timezone' => intval($date[10])
		];
		if ($time == time()) {
			$this->current_date_object = $this->date_object;
		} else {
			$date = explode(';', date('d;N;z;W;m;t;Y;H;i;s;O'));
			$this->current_date_object = [
				'day_of_month' => intval($date[0]),
				'day_of_week' => intval($date[1]),
				'day_of_year' => intval($date[2]),
				'week_of_year' => intval($date[3]),
				'month' => intval($date[4]),
				'days_in_month' => intval($date[5]),
				'year' => intval($date[6]),
				'hour' => intval($date[7]),
				'minute' => intval($date[8]),
				'second' => intval($date[9]),
				'timezone' => intval($date[10])
			];
		}
	}

	/**
	 * Get time formatted to be shown in the messsenger.
	 * It's string like "12 января 2019", "7 мая", "вчера" or "сегодня"
	 * @return string
	 */
	function message_time_format() {
		$result = '';
		if ($this->date_object['year'] == $this->current_date_object['year']) {
			if ($this->date_object['month'] == $this->current_date_object['month']) {
				if ($this->date_object['day_of_month'] == $this->current_date_object['day_of_month']) {
					$result = Lang::getText('today');
				} else if ($this->date_object['day_of_month'] + 1 == $this->current_date_object['day_of_month']) {
					$result = Lang::getText('yesterday');
				}
			} else if ($this->date_object['month'] + 1 == $this->current_date_object['month']) {
				if ($this->date_object['day_of_month'] == $this->date_object['days_in_month'] and $this->current_date_object['day_of_month'] == 1) {
					$result = Lang::getText('yesterday');
				} else {
					$result = $this->full_date_wo_year();
				}
			} else {
				$result = $this->full_date_wo_year();
			}
		} else { 
			if ($this->date_object['year'] + 1 == $this->current_date_object['year']) {
				if ($this->date_object['month'] == 12 and $this->date_object['day_of_month'] == 31 and $this->current_date_object['month'] == 1 and $this->current_date_object['day_of_month'] == 1) {
					$result = Lang::getText('yesterday');
				} else {
					$result = $this->full_date();
				}
			} else {
				$result = $this->full_date();
			}
		}
		return $result;
	}

	/**
	 * Get date like "12 января 2019".
	 * @return string
	 */
	function full_date() {
		return $this->date_object['day_of_month'] . ' ' . Lang::getText('month_' . $this->date_object['month']) . ' ' . $this->date_object['year'];
	}

	/**
	 * Get date like "12 января".
	 * @return string
	 */
	function full_date_wo_year() {
		return $this->date_object['day_of_month'] . ' ' . Lang::getText('month_' . $this->date_object['month']);
	}

	/**
	 * Something like date() function. It replaces strings from the keys of $signs array with value of that key. 
	 * @param $req
	 * @return string
	 */
	function toString($req) {
		$signs = [
			'date_day_num' => $this->date_object['day_of_month'],
			'date_day_two_nums' => $this->date_object['day_of_month'] < 10 ? '0' . $this->date_object['day_of_month'] : '' . $this->date_object['day_of_month'],
			'date_month_full' => Lang::getText('month_' . $this->date_object['month']),
			'date_month_short' => Lang::getText('month_short_' . $this->date_object['month']),
			'date_month_num' => $this->date_object['month'],
			'date_month_two_nums' => $this->date_object['month'] < 10 ? '0' . $this->date_object['month'] : '' . $this->date_object['month'],
			'year' => $this->date_object['year'],
			'year_two' => $this->date_object['year'] % 100,
			'hour' => $this->date_object['hour'],
			'hour_two' => $this->date_object['hour'],
			'minute' => $this->date_object['minute'],
			'minute_two' => $this->date_object['minute'] < 10 ? '0' . $this->date_object['minute'] : '' . $this->date_object['minute'],
			'second' => $this->date_object['second'],
			'second_two' => $this->date_object['second'] < 10 ? '0' . $this->date_object['second'] : '' . $this->date_object['second'],
			'message_time_format' => $this->message_time_format()
		];
		$keys = array_keys($signs);
		for ($i = 0; $i < count($signs); $i++) {
			$req = str_replace($keys[$i], $signs[$i], $req);
		}
		return $req;
	}
}