<?php

namespace App\Backend\Modules\News;

use App\AppController;
use \OCFram\BackController;
use \FormBuilder\CommentFormBuilder;
use \OCFram\HTTPRequest;
use \Entity\News;
use \Entity\Comment;
use \FormBuilder\NewsFormBuilder;
use \OCFram\FormHandler;


class NewsController extends BackController {
	use AppController;
	
	/** affichage des news pour le backend
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeIndex( HTTPRequest $Request ) {
		$this->run();
		
		$this->_page->addVar( 'title', 'Gestion des news' );
		
		$manager = $this->_managers->getManagerOf( 'News' );
		
		if ( $this->_app->user()->getStatus() == 'admin' ) {
			$List_news_a = $manager->getList();
			$number_news = $manager->countNews();
		}
		else {
			$id = $this->getUser()->getId();
			$List_news_a = $manager->getList( -1, -1, $id );
			$number_news = $manager->countNews( $id );
		}
		
		foreach ( $List_news_a as $News ) {
			$News->link_edition      = $this->app()->router()->provideRoute( 'Backend', 'News', 'update', [ 'id' => $News[ 'id' ] ] );
			$News->link_delete       = $this->app()->router()->provideRoute( 'Backend', 'News', 'delete', [ 'id' => $News[ 'id' ] ] );
			$News->dateAjoutFormated = $News->dateAjout()->format( 'd/m/Y à H\hi' );
			$News->dateModifFormated = $News->dateModif()->format( 'd/m/Y à H\hi' );
			$News->setMember($this->_managers->getManagerOf('Members')->getMemberFromId($News->fk_NMC()));
		}
		
		$this->_page->addVar( 'List_news_a', $List_news_a );
		$this->_page->addVar( 'number_news', $number_news );
		$this->_page->addVar( 'add_news', $this->app()->router()->provideRoute( 'Backend', 'News', 'insert', [] ) );
	}
	
	/** insertion d'une news
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeInsert( HTTPRequest $Request ) {
		$this->run();
		
		$this->processForm( $Request );
		
		$this->_page->addVar( 'title', 'Ajout d\'une news' );
	}
	
	/** gestion du formulaire pour l'insertion et l'update
	 *
	 * @param HTTPRequest $Request
	 */
	public function processForm( HTTPRequest $Request ) {
		if ( $Request->method() == 'POST' ) {
			if ( $Request->postExists( 'auteur' ) ) {
				$News = new News( [
					'auteur'  => $Request->postData( 'auteur' ),
					'titre'   => $Request->postData( 'titre' ),
					'contenu' => $Request->postData( 'contenu' ),
				] );
			}
			else {
				$News = new News( [
					'titre'   => $Request->postData( 'titre' ),
					'contenu' => $Request->postData( 'contenu' ),
				] );
				$News->setFk_NMC( $this->_managers->getManagerOf( 'Members' )->getIdMemberFromLogin( $this->getUser()->getLogin() ) );
			}
			
			if ( $Request->getExists( 'id' ) ) {
				$News->setId( $Request->getData( 'id' ) );
			}
		}
		else {
			if ( $Request->getExists( 'id' ) ) {
				$News = $this->_managers->getManagerOf( 'News' )->getNews( $Request->getData( 'id' ) );
			}
			else {
				$News = new News;
			}
		}
		
		$formBuilder = new NewsFormBuilder( $News );
		$formBuilder->build( $this->_app->user() );
		
		$form = $formBuilder->form();
		
		$formHandler = new FormHandler( $form, $this->_managers->getManagerOf( 'News' ), $Request );
		
		if ( $formHandler->process() ) {
			$this->_app->user()->setFlash( $News->isNew() ? 'La news a bien été ajoutée !' : 'La news a bien été modifiée !' );
			$this->_app->httpResponse()->redirect( '/' );
		}
		$this->_page->addVar( 'form', $form->createView() );
	}
	
	/** update d'une news
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeUpdate( HTTPRequest $Request ) {
		$this->run();
		
		$id    = $Request->getData( 'id' );
		$login = $this->_managers->getManagerOf( 'News' )->getLoginFromNewsId( $id );
		if ( $login !== $this->_app->user()->getLogin() && $this->_app->user()->getStatus() !== 'admin' ) {
			$this->_app->httpResponse()->redirect404();
		}
		else {
			$this->processForm( $Request );
		}
	}
	
	/** suppression d'une news
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeDelete( HTTPRequest $Request ) {
		$this->run();
		
		if ( $Request->getExists( 'id' ) ) {
			$id    = $Request->getData( 'id' );
			$login = $this->_managers->getManagerOf( 'News' )->getLoginFromNewsId( $id );
			if ( $login == $this->_app->user()->getLogin() || $this->_app->user()->getStatus() == 'admin' ) {
				$this->_managers->getManagerOf( 'News' )->delete( $Request->getData( 'id' ) );
				$this->_managers->getManagerOf( 'Comments' )->deleteFromNews( $Request->getData( 'id' ) );
				
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
	 * @param HTTPRequest $Request
	 */
	public function executeUpdateComment( HTTPRequest $Request ) {
		$this->run();
		
		$this->_page->addVar( 'title', 'Modification d\'un commentaire' );
		
		if ( $Request->method() == 'POST' ) {
			if ( $Request->postExists( 'auteur' ) ) {
				$comment = new Comment( [
					'id'      => $Request->getData( 'id' ),
					'auteur'  => $Request->postData( 'auteur' ),
					'contenu' => $Request->postData( 'contenu' ),
				] );
			}
			else {
				$comment = new Comment ( [
					'id'      => $Request->getData( 'id' ),
					'contenu' => $Request->postData( 'contenu' ),
				] );
				$comment->setAuteur( $this->_managers->getManagerOf( 'Comments' )->getCommentAuthorFromId( $Request->getData( 'id' ) ) );
			}
		}
		else {
			$comment = $this->_managers->getManagerOf( 'Comments' )->get( $Request->getData( 'id' ) );
		}
		
		if ( $Request->getExists( 'id' ) ) {
			$id_comment     = $Request->getData( 'id' );
			$news_author    = $this->_managers->getManagerOf( 'Comments' )->getNewsAuthorFromIdComment( $id_comment );
			$comment_author = $this->_managers->getManagerOf( 'Comments' )->getCommentAuthorFromId( $id_comment );
			if ( ( $comment_author != 'admin' && ( $comment_author == $this->_app->user()->getLogin() || $news_author == $this->_app->user()->getLogin() )
				   || ( $this->_app->user()->getStatus() == 'admin' ) )
			) {
				$formBuilder = new CommentFormBuilder( $comment );
				$formBuilder->build( $this->_app->user(), $this->_managers->getManagerOf( 'Members' ) );
				
				$form = $formBuilder->form();
				
				$formHandler = new FormHandler( $form, $this->_managers->getManagerOf( 'Comments' ), $Request );
				
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
	 * @param HTTPRequest $Request
	 */
	public function executeDeleteComment( HTTPRequest $Request ) {
		$this->run();
		
		$id_comment     = $Request->getData( 'id' );
		$news_author    = $this->_managers->getManagerOf( 'Comments' )->getNewsAuthorFromIdComment( $id_comment );
		$comment_author = $this->_managers->getManagerOf( 'Comments' )->getCommentAuthorFromId( $id_comment );
		if ( ( $comment_author != 'admin' && ( $comment_author == $this->_app->user()->getLogin() || $news_author == $this->_app->user()->getLogin() )
			   || ( $this->_app->user()->getStatus() == 'admin' ) )
		) {
			$this->_managers->getManagerOf( 'Comments' )->delete( $Request->getData( 'id' ) );
			$this->_app->user()->setFlash( 'Le commentaire a bien été supprimé !' );
			$this->_app->httpResponse()->redirect( '.' );
		}
		else {
			$this->_app->httpResponse()->redirect404();
		}
	}
}


