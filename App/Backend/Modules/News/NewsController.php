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
	/** affichage des news pour le backend
	 *
	 * @param HTTPRequest $request
	 */
	public function executeIndex( HTTPRequest $request ) {
		$this->_page->addVar( 'title', 'Gestion des news' );
		
		$manager = $this->_managers->getManagerOf( 'News' );
		
		if ( $this->_app->user()->getStatus() == 'admin' ) {
			$this->_page->addVar( 'listeNews', $manager->getList() );
			$this->_page->addVar( 'nombreNews', $manager->countNews() );
		}
		else {
			if ( $this->_app->user()->getStatus() == 'member' ) {
				$this->_page->addVar( 'listeNews', $manager->getList( -1, -1, $this->_app->user()->getLogin() ) );
				$this->_page->addVar( 'nombreNews', $manager->countNews( $this->_app->user()->getLogin() ) );
			}
		}
	}
	
	/** insertion d'une news
	 *
	 * @param HTTPRequest $request
	 */
	public function executeInsert( HTTPRequest $request ) {
		$this->processForm( $request );
		
		$this->_page->addVar( 'title', 'Ajout d\'une news' );
	}
	
	/** gestion du formulaire pour l'insertion et l'update
	 *
	 * @param HTTPRequest $request
	 */
	public function processForm( HTTPRequest $request ) {
		if ( $request->method() == 'POST' ) {
			if ( $request->postExists( 'auteur' ) ) {
				$news = new News( [
					'auteur'  => $request->postData( 'auteur' ),
					'titre'   => $request->postData( 'titre' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
			}
			else {
				$news = new News( [
					'titre'   => $request->postData( 'titre' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
				$news->setAuteur( $this->_app->user()->getLogin() );
			}
			
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
		$formBuilder->build( $this->_app->user() );
		
		$form = $formBuilder->form();
		
		$formHandler = new FormHandler( $form, $this->_managers->getManagerOf( 'News' ), $request );
		
		if ( $formHandler->process() ) {
			$this->_app->user()->setFlash( $news->isNew() ? 'La news a bien été ajoutée !' : 'La news a bien été modifiée !' );
			$this->_app->httpResponse()->redirect( '/' );
		}
		$this->_page->addVar( 'form', $form->createView() );
	}
	
	/** update d'une news
	 *
	 * @param HTTPRequest $request
	 */
	public function executeUpdate( HTTPRequest $request ) {
		if ( $request->getExists( 'id' ) ) {
			$id    = $request->getData( 'id' );
			$login = $this->_managers->getManagerOf( 'News' )->getLoginFromNewsId( $id );
			if ( $login == $this->_app->user()->getLogin() || $this->_app->user()->getStatus() == 'admin' ) {
				$this->processForm( $request );
				$this->_page->addVar( 'title', 'Modification d\'une news' );
			}
			else {
				$this->_app->httpResponse()->redirect404();
			}
		}
	}
	
	/** suppression d'une news
	 *
	 * @param HTTPRequest $request
	 */
	public function executeDelete( HTTPRequest $request ) {
		if ( $request->getExists( 'id' ) ) {
			$id    = $request->getData( 'id' );
			$login = $this->_managers->getManagerOf( 'News' )->getLoginFromNewsId( $id );
			if ( $login == $this->_app->user()->getLogin() || $this->_app->user()->getStatus() == 'admin' ) {
				$this->_managers->getManagerOf( 'News' )->delete( $request->getData( 'id' ) );
				$this->_managers->getManagerOf( 'Comments' )->deleteFromNews( $request->getData( 'id' ) );
				
				$this->_app->user()->setFlash( 'La news a bien été supprimée !' );
				
				$this->_app->httpResponse()->redirect( '.' );
			}
			else {
				$this->_app->httpResponse()->redirect404();
			}
		}
	}
	
	/** update d'un commentaire
	 *
	 * @param HTTPRequest $request
	 */
	public function executeUpdateComment( HTTPRequest $request ) {
		$this->_page->addVar( 'title', 'Modification d\'un commentaire' );
		
		if ( $request->method() == 'POST' ) {
			if ( $request->postExists( 'auteur' ) ) {
				$comment = new Comment( [
					'id'      => $request->getData( 'id' ),
					'auteur'  => $request->postData( 'auteur' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
			}
			else {
				$comment = new Comment ( [
					'id'      => $request->getData( 'id' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
				$comment->setAuteur( $this->_app->user()->getLogin() );
			}
		}
		else {
			$comment = $this->_managers->getManagerOf( 'Comments' )->get( $request->getData( 'id' ) );
		}
		
		if ( $request->getExists( 'id' ) ) {
			$id_comment     = $request->getData( 'id' );
			$news_author    = $this->_managers->getManagerOf( 'Comments' )->getNewsAuthorFromIdComment( $id_comment );
			$comment_author = $this->_managers->getManagerOf( 'Comments' )->getCommentAuthorFromId( $id_comment );
			if ( ($comment_author != 'admin' && ($comment_author == $this->_app->user()->getLogin() || $news_author == $this->_app->user()->getLogin()) || ($this->_app->user()->getStatus() == 'admin'))) {
				$formBuilder = new CommentFormBuilder( $comment );
				$formBuilder->build( $this->_app->user(), $this->_managers->getManagerOf( 'Members' ) );
				
				$form = $formBuilder->form();
				
				$formHandler = new FormHandler( $form, $this->_managers->getManagerOf( 'Comments' ), $request );
				
				if ( $formHandler->process() ) {
					$this->_app->user()->setFlash( 'Le commentaire a bien été modifié !' );
					$this->_app->httpResponse()->redirect( '/admin/' );
				}
				$this->_page->addVar( 'form', $form->createView() );
			}
			else {
				$this->_app->httpResponse()->redirect404();
			}
		}
	}
	
	/** suppression d'un commentaire
	 *
	 * @param HTTPRequest $request
	 */
	public function executeDeleteComment( HTTPRequest $request ) {
		$id_comment     = $request->getData( 'id' );
		$news_author    = $this->_managers->getManagerOf( 'Comments' )->getNewsAuthorFromIdComment( $id_comment );
		$comment_author = $this->_managers->getManagerOf( 'Comments' )->getCommentAuthorFromId( $id_comment );
		if ( ($comment_author != 'admin' && ($comment_author == $this->_app->user()->getLogin() || $news_author == $this->_app->user()->getLogin()) || ($this->_app->user()->getStatus() == 'admin'))) {
			$this->_managers->getManagerOf( 'Comments' )->delete( $request->getData( 'id' ) );
			$this->_app->user()->setFlash( 'Le commentaire a bien été supprimé !' );
			$this->_app->httpResponse()->redirect( '.' );
		}
		else {
			$this->_app->httpResponse()->redirect404();
		}
	}
}


