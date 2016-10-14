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
	
	/** affichage des news sur la page d'accueil
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeIndex( HTTPRequest $Request ) {
		$this->run();
		
		$number_news       = $this->_app->config()->get( 'nombre_news' );
		$number_characters = $this->_app->config()->get( 'nombre_caracteres' );
		
		$this->_page->addVar( 'title', 'Liste des ' . $number_news . ' dernières news' );
		
		//on récupère le manager des news
		/** @var NewsManager $manager */
		$manager = $this->_managers->getManagerOf( 'News' );
		
		$News_a = $manager->getList( 0, $number_news );
		
		foreach ( $News_a as $News ) {
			$shorten_content = mb_strimwidth( $News->contenu(), 0, $number_characters, "..." );
			$News->setContenu( $shorten_content );
			$News->link = $this->app()->router()->provideRoute( 'Frontend', 'News', 'show', [ 'id' => $News[ 'id' ] ] );
		}
		
		$this->_page->addVar( 'News_a', $News_a );
	}
	
	/** affichage d'une news est ses commentaires
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeShow( HTTPRequest $Request ) {
		$this->run();
		
		$Manager = $this->_managers->getManagerOf( 'News' );
		
		$News = $Manager->getNews( $Request->getData( 'id' ) );
		
		if ( empty( $News ) ) {
			$this->_app->httpResponse()->redirect404();
		}
		
		//liste des commentaires
		$Comments_a = $this->_managers->getManagerOf( 'Comments' )->getListOf( $News->id() );
		
		//nom de l'auteur de la news
		$news_author = $this->_managers->getManagerOf( 'News' )->getLoginFromNewsId( $News->id() );
		//affichage de la liste des commentaires
		foreach ( $Comments_a as $Comment ) {
			$Comment->date_formated = $Comment->date()->format( 'd/m/Y à H\hi' );
			
			$this->addLinksUpdateDeleteToComment($Comment);
		}
		
		$formBuilder = new CommentFormBuilder( new Comment() );
		$formBuilder->build( $this->_app->user(), $this->_managers->getManagerOf( 'Members' ) );
		
		$this->_page->addVar( 'add_comment_form', $formBuilder->form()->createView() );
		
		$this->_page->addVar( 'news_author', $news_author );
		$this->_page->addVar( 'title', $News->titre() );
		//passage de la news
		$this->_page->addVar( 'News', $News );
		$this->_page->addVar( 'Comments_a', $Comments_a );
		//passage du lien pour l'ajout d'un commentaire
		$this->_page->addVar( 'add_comment', $this->app()->router()->provideRoute( 'Frontend', 'News', 'insertComment', [ 'id' => $News[ 'id' ] ] ) );
		
		$this->_page->addVar( 'url_response_form', $this->app()->router()->provideRoute( 'Frontend', 'News', 'insertCommentAjax', [ 'news' => $News[ 'id' ] ] ) );
		
		$this->_page->addVar( 'for_refresh_url', $this->app()->router()->provideRoute( 'Frontend', 'News', 'getCommentList', [ 'news' => $News[ 'id' ] ] ) );
	}
	
	private function addLinksUpdateDeleteToComment(Comment $Comment){
		
		//nom de l'auteur de la news
		$news_author = $this->_managers->getManagerOf('Comments')->getNewsAuthorFromIdComment($Comment['id']);
		
		//recuperation du login de l'auteur du commentaire
		if ( $Comment[ 'fk_NMC' ] != null ) {
			$Member = $this->_managers->getManagerOf('Members')->getMemberFromId($Comment['fk_NMC']);
			$Comment->setAuteur($Member['login']);
		}
		
		if ( $this->getUser()->isAuthenticated() ) {
			if ( $this->loggedUserIsAdmin() ) {
				$Comment->link_update = $this->app()->router()->provideRoute( 'Backend', 'News', 'updateComment', [ 'id' => $Comment[ 'id' ] ] );
				$Comment->link_delete = $this->app()->router()->provideRoute( 'Backend', 'News', 'deleteComment', [ 'id' => $Comment[ 'id' ] ] );
			}
			else {
				$status = self::STATUS_MEMBER_MEMBER;
				if ( $Comment[ 'fk_NMC' ] != null ) {
					$status = $this->_managers->getManagerOf( 'Members' )->getStatusMemberFromId( $Comment[ 'fk_NMC' ] );
				}
				if ( $status != self::STATUS_MEMBER_ADMIN
					 && ( $this->getUser()->getLogin() == $Comment['auteur'] || $this->getUser()->getLogin() == $news_author
						  || $this->loggedUserIsAdmin() )
				) {
					$Comment->link_update = $this->app()->router()->provideRoute( 'Backend', 'News', 'updateComment', [ 'id' => $Comment[ 'id' ] ] );
					$Comment->link_delete = $this->app()->router()->provideRoute( 'Backend', 'News', 'deleteComment', [ 'id' => $Comment[ 'id' ] ] );
				}
			}
		}
	}
	
	private function formTreatment( HTTPRequest $Request ) {
		if ( $Request->method() == 'POST' ) {
			if ( $Request->postExists( 'auteur' ) ) {
				$Comment = new Comment( [
					'fk_NNC'  => $Request->getData( 'news' ),
					'auteur'  => $Request->postData( 'auteur' ),
					'contenu' => $Request->postData( 'contenu' ),
				] );
			}
			else {
				$Comment = new Comment( [
					'fk_NNC'  => $Request->getData( 'news' ),
					'contenu' => $Request->postData( 'contenu' ),
				] );
				$Comment->setFk_NMC( $this->_managers->getManagerOf( 'Members' )->getIdMemberFromLogin( $this->getUser()->getLogin() ) );
			}
		}
		else {
			$Comment = new Comment;
		}
		
		//var_dump($Comment);
		
		return $Comment;
	}
	
	/** insertion d'un commentaire
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeInsertComment( HTTPRequest $Request ) {
		$this->run();
		
		$Comment = $this->formTreatment( $Request );
		
		$formBuilder = new CommentFormBuilder( $Comment );
		$formBuilder->build( $this->_app->user(), $this->_managers->getManagerOf( 'Members' ) );
		
		$form = $formBuilder->form();
		
		$formHandler = new FormHandler( $form, $this->_managers->getManagerOf( 'Comments' ), $Request );
		
		if ( $formHandler->process() ) {
			$this->_app->user()->setFlash( 'Le commentaire a bien été ajouté, merci !' );
			$this->_app->httpResponse()->redirect( 'news-' . $Request->getData( 'news' ) . '.html' );
		}
		
		$this->_page->addVar( 'title', 'Ajout d\'un commentaire' );
		$this->_page->addVar( 'Comment', $Comment );
		$this->_page->addVar( 'form', $form->createView() );
	}
	
	public function executeInsertCommentAjax( HTTPRequest $Request ) {
		$this->run();
		
		$Comment = $this->formTreatment( $Request );
		
		$this->_page->addVar( 'success', false );
		if ( !$Comment->isValid() ) {
			if ( !$Comment->contenu() && !$Comment->auteur() && !$this->getUser()->isAuthenticated() ) {
				$this->_page->addVar( 'error_message', 'Veuillez ajouter un auteur et un contenu' );
				$this->_page->addVar( 'name', 'auteur' );
				$this->_page->addVar( 'error_code', 1 );
			}
			else {
				if ( !$Comment->contenu() ) {
					$this->_page->addVar( 'error_message', 'Veuillez ajouter un contenu' );
					$this->_page->addVar( 'name', 'contenu' );
					$this->_page->addVar( 'error_code', 2 );
				}
				else {
					$this->_page->addVar( 'error_message', 'Veuillez ajouter auteur' );
					$this->_page->addVar( 'name', 'auteur' );
					$this->_page->addVar( 'error_code', 3 );
				}
			}
		}
		else {
			if ( $this->_managers->getManagerOf( 'Members' )->existsMemberUsingLogin( $Comment->auteur() ) ) {
				$this->_page->addVar( 'error_message', 'Vous ne pouvez pas utiliser ce nom pour votre commentaire' );
				$this->_page->addVar( 'name', 'auteur' );
				$this->_page->addVar( 'error_code', 4 );
			}
			else {
				$this->_page->addVar( 'success', true );
				$this->_page->addVar( 'validation_message', 'Le commentaire a bien été ajouté' );
				$this->_page->addVar( 'name', 'auteur' );
				if ( null != $Comment->fk_NMC() ) {
					$Comment->setAuteur( $this->_managers->getManagerOf( 'Members' )->getMemberFromId( $Comment->fk_NMC() )->login() );
				}
				
				$this->_managers->getManagerOf( 'Comments' )->add( $Comment );
				$Comment->date_formated = $Comment->date()->format( 'd/m/Y à H\hi' );
				$this->addLinksUpdateDeleteToComment($Comment);
				$this->_page->addVar( 'Comment', $Comment );
			}
		}
	}
	
	public function executeGetCommentList( HTTPRequest $Request ) {
		$this->run();
		
		$News_id = $Request->getData( 'news' );
		$last_Comment_id = $Request->postData('id');
		
		$Comments_a = $this->_managers->getManagerOf( 'Comments' )->getListOf( $News_id , $last_Comment_id);
		
		//nom de l'auteur de la news
		$news_author = $this->_managers->getManagerOf( 'News' )->getLoginFromNewsId( $News_id );
		
		foreach ($Comments_a as $Comment) {
			$Comment->date_formated = $Comment->date()->format( 'd/m/Y à H\hi' );
			$this->addLinksUpdateDeleteToComment($Comment);
		}
		
		$this->_page->addVar( 'Comments_a', $Comments_a );
	}
}