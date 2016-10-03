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
use \Entity\Comment;
use \OCFram\Form;
use \OCFram\StringField;
use \OCFram\TextField;

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

    public function executeShow(HTTPRequest $request){
        $manager = $this->_managers->getManagerOf('News');

        $news = $manager->getNews($request->getData('id'));

        if(empty($news)){
            $this->_app->httpResponse()->redirect404();
        }

        $this->_page->addVar('title',$news->titre());
        $this->_page->addVar('news',$news);
        $this->_page->addVar('comments',$this->_managers->getManagerOf('Comment')->getListOf($news->id()));
    }

    public function executeInsertComment(HTTPRequest $request){
        if($request->method() == 'POST') {
            if ($request->postExists('pseudo')) {
                $comment = new Comment([
                    'news' => $request->getData('news'),
                    'auteur' => $request->postData('auteur'),
                    'contenu' => $request->postData('contenu')
                ]);
            }
        }
        else {
            $comment = new Comment;
        }

        $form = new Form($comment);

        $form->add(new StringField([
            'label' => 'Auteur',
            'name' => 'auteur',
            'maxLength' => 50
        ]));
        $form->add(new TextField([
            'label' => 'Contenu',
            'name' => 'contenu',
            'rows' => 7,
            'cols' => 50
        ]));

        if($form->isValid()){
            $this->_managers->getManagerOf('Comment')->save($comment);
            $this->_app->user()->setFlash('Le commentaire a bien été ajouté, merci !');
            $this->_app->httpResponse()->redirect('news-' . $request->getData('news') . '.html');
        }
        else {
            $this->_page->addVar('erreurs', $comment->errors());
        }

        $this->_page->addVar('title','Ajout d\'un commentaire');
        $this->_page->addVar('comment', $comment);
    }
}