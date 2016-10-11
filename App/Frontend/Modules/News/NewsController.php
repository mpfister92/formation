<?php


namespace App\Frontend\Modules\News;

use App\AppController;
use Model\NewsManager;
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \OCFram\FormHandler;


class NewsController extends BackController {
	use AppController;
	const MEMBER_STATUS_ADMIN  = 1;
	const MEMBER_STATUS_MEMBER = 2;
	
	/** affichage des news sur la page d'accueil
	 *
	 * @param HTTPRequest $request
	 */
	public function executeIndex( HTTPRequest $request ) {
		$this->run();
		
		$number_news       = $this->_app->config()->get( 'nombre_news' );
		$number_characters = $this->_app->config()->get( 'nombre_caracteres' );
		
		$this->_page->addVar( 'title', 'Liste des ' . $number_news . ' dernières news' );
		
		//on récupère le manager des news
		/** @var NewsManager $manager */
		$manager = $this->_managers->getManagerOf( 'News' );
		
		$List_news_a = $manager->getList( 0, $number_news );
		
		foreach ( $List_news_a as $News ) {
			$debut = mb_strimwidth( $News->contenu(), 0, $number_characters, "..." );
			$News->setContenu( $debut );
		}
		
		foreach ( $List_news_a as $News ) {
			$News->link = $this->app()->router()->provideRoute( 'Frontend', 'News', 'show', [ 'id' => $News[ 'id' ] ] );
		}
		$this->_page->addVar( 'List_news_a', $List_news_a );
	}
	
	/** affichage d'une news est ses commentaires
	 *
	 * @param HTTPRequest $request
	 */
	public function executeShow( HTTPRequest $request ) {
		$this->run();
		
		$Manager = $this->_managers->getManagerOf( 'News' );
		
		$News = $Manager->getNews( $request->getData( 'id' ) );
		
		if ( empty( $News ) ) {
			$this->_app->httpResponse()->redirect404();
		}
		
		//liste des commentaires
		$List_comments_a = $this->_managers->getManagerOf( 'Comments' )->getListOf( $News->id() );
		
		//nom de l'auteur de la news
		$news_author = $this->_managers->getManagerOf( 'News' )->getLoginFromNewsId( $News->id() );
				
		foreach ( $List_comments_a as $Comment ) {
			//recuperation du login de l'auteur du commentaire
			if ( $Comment[ 'fk_NMC' ] != null ) {
				$Comment->comment_author = $this->_managers->getManagerOf( 'Members' )->getLoginMemberFromId( $Comment[ 'fk_NMC' ] );
			}
			else {
				$Comment->comment_author = $Comment[ 'auteur' ];
			}
			$Comment->date_formated = $Comment->date()->format( 'd/m/Y à H\hi' );
			if ( $this->getUser()->isAuthenticated() ) {
				if ( $this->getUser()->getStatus() == 'admin' ) {
					$Comment->link_update = $this->app()->router()->provideRoute( 'Backend', 'News', 'updateComment', [ 'id' => $Comment[ 'id' ] ] );
					$Comment->link_delete = $this->app()->router()->provideRoute( 'Backend', 'News', 'deleteComment', [ 'id' => $Comment[ 'id' ] ] );
				}
				else {
					$status = self::MEMBER_STATUS_MEMBER;
					if ( $Comment[ 'fk_NMC' ] != null ) {
						$status = $this->_managers->getManagerOf( 'Members' )->getStatusMemberFromId( $Comment[ 'fk_NMC' ] );
					}
					if ( $status != self::MEMBER_STATUS_ADMIN
						 && ( $this->getUser()->getLogin() == $Comment[ 'auteur' ] || $this->getUser()->getLogin() == $news_author
							  || $this->getUser()->getStatus() == 'admin' )
					) {
						$Comment->link_update = $this->app()->router()->provideRoute( 'Backend', 'News', 'updateComment', [ 'id' => $Comment[ 'id' ] ] );
						$Comment->link_delete = $this->app()->router()->provideRoute( 'Backend', 'News', 'deleteComment', [ 'id' => $Comment[ 'id' ] ] );
					}
				}
			}
		}
		
		$this->_page->addVar( 'news_author', $news_author );
		$this->_page->addVar( 'title', $News->titre() );
		//passage de la news
		$this->_page->addVar( 'News', $News );
		$this->_page->addVar('List_comments_a',$List_comments_a);
		//passage du lien pour l'ajout d'un commentaire
		$this->_page->addVar( 'add_comment', $this->app()->router()->provideRoute( 'Frontend', 'News', 'insertComment', [ 'id' => $News[ 'id' ] ] ) );
	}
	
	/** insertion d'un commentaire
	 *
	 * @param HTTPRequest $request
	 */
	public function executeInsertComment( HTTPRequest $request ) {
		$this->run();
		
		if ( $request->method() == 'POST' ) {
			if ( $request->postExists( 'auteur' ) ) {
				$Comment = new Comment( [
					'news'    => $request->getData( 'news' ),
					'auteur'  => $request->postData( 'auteur' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
			}
			else {
				$Comment = new Comment( [
					'news'    => $request->getData( 'news' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
				$Comment->setMember( $this->_managers->getManagerOf( 'Members' )->getIdMemberFromLogin( $this->getUser()->getLogin() ) );
			}
		}
		else {
			$Comment = new Comment;
		}
		
		$formBuilder = new CommentFormBuilder( $Comment );
		$formBuilder->build( $this->_app->user(), $this->_managers->getManagerOf( 'Members' ) );
		
		$form = $formBuilder->form();
		
		$formHandler = new FormHandler( $form, $this->_managers->getManagerOf( 'Comments' ), $request );
		
		if ( $formHandler->process() ) {
			$this->_app->user()->setFlash( 'Le commentaire a bien été ajouté, merci !' );
			$this->_app->httpResponse()->redirect( 'news-' . $request->getData( 'news' ) . '.html' );
		}
		
		$this->_page->addVar( 'title', 'Ajout d\'un commentaire' );
		$this->_page->addVar( 'Comment', $Comment );
		$this->_page->addVar( 'form', $form->createView() );
	}
}