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
	    //création d'une instance de la classe page
		$this->_page = new Page($this->_app);
		//assignation du fichier qui fait office de vue à générer
        $this->_page->setContentFile(__DIR__.'/../../App/Errors/404.php');
        //ajout d'un header
        $this->addHeader('HTTP/1.0 404 Not Found');
        //envoie de la réponse
        $this->send();
	}
	public function send() {
		exit($this->_page->getGeneratedPage());
	} 
	public function setCookie($name = '',$expire = 0,$path = null,$domain = null,$secure = false,$httpOnly = true) {
		setcookie($name,$expire,$path,$domain,$secure,$httpOnly);
	} 
	public function setPage($page) {
		$this->_page = $page;
	} 
}
?>