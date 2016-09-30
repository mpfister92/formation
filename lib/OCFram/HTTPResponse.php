<?php
namespace OCFram;

class HTTPResponse extends ApplicationComponent {
	protected $_page;

	public function addHeader($header) {
		header($header);
	}
	public function redirect($location) {
		header('Location: '.$location);
		exit;
	} 
	public function redirect404() {
		/* A compléter plus tard */
	} 
	public function send() {
		exit($this->page->getGeneratedPage());
	} 
	public function setCookie($name = '',$expire = 0,$path = null,$domain = null,$secure = false,$httpOnly = true) {
		setcookie($name,$expire,$path,$domain,$secure,$httpOnly);
	} 
	public function setPage($page) {
		$this->page = $page;
	} 
}
?>