<?php
namespace OCFram;

class HTTPRequest extends ApplicationComponent {
	public function cookieData($key) {
		if (isset($_COOKIE[$key]))
			return $_COOKIE[$key];
		else 
			return null;
	}

	public function cookieExists($key) {
		return isset($_COOKIE[$key]);
	}
	public function getData($key) {
		if (isset($_GET[$key]))
			return $_GET[$key];
		else
			return null;
	}

	public function getExists($key) {
		return isset($_GET[$key]);
	}

	public function method() {
		return $_SERVER['REQUEST_METHOD'];
	}

	public function postData($key) {
		if (isset($_POST[$key])
			return $_POST[$key];
		else 
			return null;
	}

	public function postExists($key) {
		return isset($_POST[$key]);
	}

	public function requestURI() {
		return $_SERVER['REQUEST_URI'];
	}
}
?>
