<?php

/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 12:16
 */

namespace App\Frontend\Modules\News;

use \OCFram\BackController;
use \OCFram\HTTPRequest;

class NewsController extends BackController {

    public function executeIndex(HTTPRequest $request){
        $nbNews = $this->_app->config()->get('nombre_news');
        $nbCaracteres = $this->_app->config()->get('nombre_caracteres');

        $this->_page->addVar('title','Liste des '.$nbNews.' dernières news');

        //on récupère le manager des news
        $manager = $this->_managers->getManagerOf('News');

        $listeNews = $manager->getList(0, $nbNews);

        foreach ($listeNews as $news)
        {
            if (strlen($news->contenu()) > $nombreCaracteres)
            {
                $debut = substr($news->contenu(), 0, $nbCaracteres);
                $debut = substr($debut, 0, strrpos($debut, ' ')) . '...';

                $news->setContenu($debut);
            }
        }

        // On ajoute la variable $listeNews à la vue.
        $this->_page->addVar('listeNews', $listeNews);
    }

    public function executeShow(){

    }
}