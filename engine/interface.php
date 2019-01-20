<?php

class InterfaceDrawer {
	private $scripts = [];
	private $styles = [];
	private $title;
	private $header;
	private $content;
	private $footer;
	private $user;
	private $wrapper;

	public function __construct($user) {
		$this->user = $user;
	}
	public function header($header) {
		$this->header = $header;
	}
	public function title($title) {
		$this->title = $title;
	}
	public function scriptName($script) {
		$this->scripts[] = '<script src="/work/js/' . $script . '"></script>';
	}
	public function script($script) {
		$this->scripts[] = '<script>' . $script . '</script>';
	}
	public function styleFileName($style) {
		$this->styles[] = '<link rel="stylesheet" type="text/css" href="/work/css/' . $style . '">';
	}
	public function contentWrapper($wrapper) {
		$this->wrapper = $wrapper;
	}
	public function content($content) {
		$this->content = $content;
	}
	public function footer($footer) {
		$this->footer = $footer;
	}
	public function draw() {
		$headElems = "\n\t";

		for ($i = 0; $i < count($this->scripts); $i++) { 
			$headElems .= $this->scripts[$i] . "\n";
		}
		for ($i = 0; $i < count($this->styles); $i++) {
			$headElems .= $this->styles[$i] . "\n";
		}

		include 'markup/common_new.phtml';
	}
}