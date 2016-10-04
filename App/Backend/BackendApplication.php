<?php


namespace App\Backend;

use \OCFram\Application;

class BackendApplication extends Application {

    public function __construct(){
        parent::__construct();
        $this->_name = 'Backend';
    }

    public function run(){
        if($this->user()->isAuthenticated() == true) {
            $controller = $this->getController();
        }
        else {
            $controller = new Modules\Connexion\ConnexionController($this,'Connexion','index');
        }

        $controller->execute();

        $this->_httpResponse->setPage($controller->page());
        $this->_httpResponse->send();
    }
}