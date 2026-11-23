<?php

namespace SDLab;

class View {

	public $templates_folder = 'templates';

	private $_view_file;

	private $_data;

	public function __construct($view_file, $data = []) {
		$this->_view_file = $view_file;
		$this->_data      = $data;
	}

	public function set($key, $value = null) {

		if (is_array($key)) {
			$this->_data = array_merge_recursive($this->_data, $key);
		} elseif (is_string($key) && $value) {
			$this->_data[$key] = $value;
		}

		return $this;
	}

	public function renderView($echo = true, $params = []) {
		$slug = sprintf('%s/%s.php', $this->templates_folder,  $this->_view_file);
		$this->_data = $this->_data + $params;

		ob_start();
		extract($this->_data, EXTR_SKIP);

		require locate_template($slug);

		$html = ob_get_clean();

		if ($echo) {
			echo $html;
		} else {
			return $html;
		}
	}

	public static function render($view_file, $data = []) {
		(new self($view_file, $data))->renderView();
	}

	public static function returnView($view_file, $data = []) {
		return (new self($view_file, $data))->renderView(false);
	}

}