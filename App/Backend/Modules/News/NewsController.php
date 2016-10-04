<?php

namespace App\Backend\Modules\News;

use \OCFram\BackController;
use \FormBuilder\CommentFormBuilder;
use \OCFram\HTTPRequest;
use \Entity\News;
use \Entity\Comment;
use \FormBuilder\NewsFormBuilder;
use \OCFram\FormHandler;

class NewsController extends BackController {
	public function executeIndex( HTTPRequest $request ) {
		$this->_page->addVar( 'title', 'Gestion des news' );
		
		$manager = $this->_managers->getManagerOf( 'News' );
		
		$this->_page->addVar( 'listeNews', $manager->getList() );
		$this->_page->addVar( 'nombreNews', $manager->count() );
	}
	
	public function executeInsert( HTTPRequest $request ) {
		$this->processForm( $request );
		
		$this->_page->addVar( 'title', 'Ajout d\'une news' );
	}
	
	public function processForm( HTTPRequest $request ) {
		if ( $request->method() == 'POST' ) {
			$news = new News( [
				'auteur'  => $request->postData( 'auteur' ),
				'titre'   => $request->postData( 'titre' ),
				'contenu' => $request->postData( 'contenu' ),
			] );
			
			if ( $request->getExists( 'id' ) ) {
				$news->setId( $request->getData( 'id' ) );
			}
		}
		else {
			if ( $request->getExists( 'id' ) ) {
				$news = $this->_managers->getManagerOf( 'News' )->getNews( $request->getData( 'id' ) );
			}
			else {
				$news = new News;
			}
		}
		
		$formBuilder = new NewsFormBuilder( $news );
		$formBuilder->build();
		
		$form = $formBuilder->form();
		
		$formHandler = new FormHandler( $form, $this->_managers->getManagerOf( 'News' ), $request );
		
		if ( $formHandler->process() ) {
			$this->_app->user()->setFlash( $news->isNew() ? 'La news a bien été ajoutée !' : 'La news a bien été modifiée !' );
			$this->_app->httpResponse()->redirect( '/admin/' );
		}
		$this->_page->addVar( 'form', $form->createView() );
	}
	
	public function executeUpdate( HTTPRequest $request ) {
		$this->processForm( $request );
		
		$this->_page->addVar( 'title', 'Modification d\'une news' );
	}
	
	public function executeDelete( HTTPRequest $request ) {
		$this->_managers->getManagerOf( 'News' )->delete( $request->getData( 'id' ) );
		$this->_managers->getManagerOf( 'Comments' )->deleteFromNews( $request->getData( 'id' ) );
		
		$this->_app->user()->setFlash( 'La news a bien été supprimée !' );
		
		$this->_app->httpResponse()->redirect( '.' );
	}
	
	public function executeUpdateComment( HTTPRequest $request ) {
		$this->_page->addVar( 'title', 'Modification d\'un commentaire' );
		
		if ( $request->method() == 'POST' ) {
			$comment = new Comment( [
				'news'    => $request->getData( 'id' ),
				'auteur'  => $request->postData( 'pseudo' ),
				'contenu' => $request->postData( 'contenu' ),
			] );
		}
		else {
			$comment = $this->_page->addVar( 'comment', $this->_managers->getManagerOf( 'Comments' )->get( $request->getData( 'id' ) ) );
		}
		
		$formBuilder = new CommentFormBuilder( $comment );
		$formBuilder->build();
		
		$form = $formBuilder->form();
		
		$formHandler = new \OCFram\FormHandler( $form, $this->_managers->getManagerOf( 'Comments' ), $request );
		
		if ( $formHandler->process() ) {
			$this->_app->user()->setFlash( 'Le commentaire a bien été modifié !' );
			$this->_app->httpResponse()->redirect( '/news-' . $request->postData( 'news' ) . '.html' );
		}
		$this->_page->addVar( 'form', $form->createView() );
	}
	
	public function executeDeleteComment( HTTPRequest $request ) {
		$this->_managers->getManagerOf( 'Comments' )->delete( $request->getData( 'id' ) );
		
		$this->_app->user()->setFlash( 'Le commentaire a bien été supprimé !' );
		
		$this->_app->httpResponse()->redirect( '.' );
	}
}


