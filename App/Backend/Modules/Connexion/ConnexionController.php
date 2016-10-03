<?php

namespace App\Backend\Modules\Connexion;

use \OCFram\BackController;
use \OCFram\HTTPRequest;

class ConnexionController extends BackController {

    public function executeIndex(HTTPRequest $request){
        if($request->postExists('login')){
            if ($request->getData('login') == $this->_app->config()->get('login') && $request->getData('password') == $this->_app->config()->get('pass')){
                $this->_app->user()->setAuthenticated(true);
                $this->_app->httpResponse()->redirect('.');
            }
            else {
                $this->_app->user()->setFlash('Le pseudo ou le mot de passe est incorrect');
            }
        }
    }
}