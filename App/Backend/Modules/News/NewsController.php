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
	 * @param HTTPRequest $request
	 */
	public function executeIndex( HTTPRequest $request ) {
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
	 * @param HTTPRequest $request
	 */
	public function executeInsert( HTTPRequest $request ) {
		$this->run();
		
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
				$news->setAuteur( $this->_managers->getManagerOf( 'Members' )->getIdMemberFromLogin( $this->getUser()->getLogin() ) );
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
		$this->run();
		
		$id    = $request->getData( 'id' );
		$login = $this->_managers->getManagerOf( 'News' )->getLoginFromNewsId( $id );
		if ( $login !== $this->_app->user()->getLogin() && $this->_app->user()->getStatus() !== 'admin' ) {
			$this->_app->httpResponse()->redirect404();
		}
		else {
			$this->processForm( $request );
			$this->_page->addVar( 'title', 'Modification d\'une news' );
			
			$this->processForm( $request );
		}
	}
	
	/** suppression d'une news
	 *
	 * @param HTTPRequest $request
	 */
	public function executeDelete( HTTPRequest $request ) {
		$this->run();
		
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
		$this->run();
		
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
				$comment->setAuteur( $this->_managers->getManagerOf( 'Comments' )->getCommentAuthorFromId( $request->getData( 'id' ) ) );
			}
		}
		else {
			$comment = $this->_managers->getManagerOf( 'Comments' )->get( $request->getData( 'id' ) );
		}
		
		if ( $request->getExists( 'id' ) ) {
			$id_comment     = $request->getData( 'id' );
			$news_author    = $this->_managers->getManagerOf( 'Comments' )->getNewsAuthorFromIdComment( $id_comment );
			$comment_author = $this->_managers->getManagerOf( 'Comments' )->getCommentAuthorFromId( $id_comment );
			if ( ( $comment_author != 'admin' && ( $comment_author == $this->_app->user()->getLogin() || $news_author == $this->_app->user()->getLogin() )
				   || ( $this->_app->user()->getStatus() == 'admin' ) )
			) {
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
		$this->run();
		
		$id_comment     = $request->getData( 'id' );
		$news_author    = $this->_managers->getManagerOf( 'Comments' )->getNewsAuthorFromIdComment( $id_comment );
		$comment_author = $this->_managers->getManagerOf( 'Comments' )->getCommentAuthorFromId( $id_comment );
		if ( ( $comment_author != 'admin' && ( $comment_author == $this->_app->user()->getLogin() || $news_author == $this->_app->user()->getLogin() )
			   || ( $this->_app->user()->getStatus() == 'admin' ) )
		) {
			$this->_managers->getManagerOf( 'Comments' )->delete( $request->getData( 'id' ) );
			$this->_app->user()->setFlash( 'Le commentaire a bien été supprimé !' );
			$this->_app->httpResponse()->redirect( '.' );
		}
		else {
			$this->_app->httpResponse()->redirect404();
		}
	}
}


