<?php
namespace OCFram;

abstract class BackController extends ApplicationComponent {
	protected $_action = '';
	protected $_module = '';
	protected $_page = null;
	protected $_view = '';

	public function __construct($app,$module,$action) {
		parent::__construct($app);
		$this->_page = new Page($app);
		$this->setAction($action);
		$this->setModule($module);
		$this->setView($view);
	}

	public function execute() {

	}

	public function page() {
		return $this->_page;
	}

	public function setModule($module) {
		$this->_module = $module;
	}

	public function setAction($action) {
		$this->_action = $action;
	}

	public function setView($view) {
		$this->_view = $view;
	}
}
?>