<?php
namespace OCFram;

abstract class ApplicationComponent {
	protected $_app;

	public function __construct(Application $app) {
		$this->_app = $app;
	}

	public function app() {
		return this->_app;
	}
}
?>