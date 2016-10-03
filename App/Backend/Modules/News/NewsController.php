<?php

namespace App\Backend\Modules\News;

use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\News;
use \Entity\Comment;

class NewsController extends BackController {

    public function executeIndex(HTTPRequest $request){
        $this->_page->addVar('title','Gestion des news');

        $manager = $this->_managers->getManagerOf('News');

        $this->_page->addVar('listNews',$manager->getList());
        $this->_page->addVar('nombreNews',$manager->count());
    }

    public function executeInsert(HTTPRequest $request){
        if($request->postExists('auteur')){
            $this->processForm($request);
        }
        $this->_page->addVar('title','Ajout d\'une news');
    }

    public function processForm(HTTPRequest $request){
        $news = new News([
            'auteur' => $request->postData('auteur'),
            'titre' => $request->postData('titre'),
            'contenu' => $request->postData('contenu')
        ]);

        if($request->postExists('id')){
            $news->setId($request->postData('id'));
        }

        if($news->isValid()){
            $this->_managers->getManagerOf('News')->save($news);

            $this->_app->user()->setFlash($news->isNew() ? 'La news a bien été ajoutée !' : 'La news a bien été modifiée !');
        }
        else {
            $this->_page->addVar('erreurs',$news->errors());
        }

        $this->_page->addVar('news',$news);
    }

    public function executeUpdate(HTTPRequest $request){
        if($request->postExists('auteur')){
            $this->processForm($request);
        }
        else {
            $this->_page->addVar('News',$this->_managers->getManagerOf('News')->getNews($request->getData('id')));
        }
        $this->_page->addVar('title','Modification d\'une news');
    }

    public function executeDelete(HTTPRequest $request){
        $this->_managers->getManagerOf('News')->delete($request->getData('id'));
        $this->_managers->getManagerOf('Comments')->deleteFromNews($request->getData('id'));

        $this->_app->user()->setFlash('La news a bien été supprimée !');

        $this->_app->httpResponse()->redirect('.');
    }

    public function executeUpdateComment(HTTPRequest $request){
        $this->_page->addVar('title','Modification d\'un commentaire');

        if($request->getData('auteur')){
            $comment = new Comment([
                'news' => $request->getData('id'),
                'auteur' => $request->postData('pseudo'),
                'contenu' => $request->postData('contenu')
            ]);

            if($comment->isValid()){
                $this->_managers->getManagerOf('Comments')->save($comment);

                $this->_app->user()->setFlash('Le commentaire a bien été modifié !');

                $this->_app->httpResponse()->redirect('/news-'.$request->postData('news').'.html');
            }
            else {
                $this->_page->addVar('erreurs',$comment->errors());
            }

            $this->_page->addVar('comment',$comment);
        }
        else{
            $this->_page->addVar('comment',$this->_managers->getManagerOf('Comments')->get($request->getData('id')));
        }
    }

    public function executeDeleteComment(HTTPRequest $request){
        $this->_managers->getManagerOf('Comments')->delete($request->getData('id'));

        $this->_app->user()->setFlash('Le commentaire a bien été supprimé !');

        $this->_app->httpResponse()->redirect('.');
    }
}


