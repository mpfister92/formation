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
	
	const COMMENT_STATE_VALID = 1;
	const COMMENT_STATE_INVALID = 2;
	
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
			$News->auteur = $this->_managers->getManagerOf('Members')->getLoginMemberFromId($News['fk_NMC']);
			$News->link_auteur = $this->app()->router()->provideRoute('Frontend','News','getSummaryMember',['id' => $News['fk_NMC']]);
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
		$Comments_a = $this->_managers->getManagerOf( 'Comments' )->getListOf( $News->id(), null, self::COMMENT_STATE_VALID );
		
		//nom de l'auteur de la news
		$news_author = $this->_managers->getManagerOf( 'News' )->getLoginFromNewsId( $News->id() );
		//affichage de la liste des commentaires
		foreach ( $Comments_a as $Comment ) {
			$Comment->date_formated = $Comment->date()->format( 'd/m/Y à H\hi' );
			
			$this->addLinksUpdateDeleteToComment($Comment);
			$this->_page->addVar('for_update_comment_url',$this->app()->router()->provideRoute('Backend','News','updateCommentAjax',['id' => $Comment['id']]));
			$this->_page->addVar('for_delete_comment_url',$this->app()->router()->provideRoute('Backend','News','deleteCommentAjax',['id' => $Comment['id']]));
			
			//lien vers le résumé de l'auteur du commentaire
			if(null != $Comment['fk_NMC']) {
				$Comment->link_summary = $this->app()->router()->provideRoute( 'Frontend', 'News', 'getSummaryMember', [ 'id' => $Comment[ 'fk_NMC' ] ] );
			}
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
		
		$last_update_date = $this->_managers->getManagerOf('Comments')->getMaxEditionDate();
		$this->_page->addVar('last_update_date',$last_update_date);
		
		$News->link_auteur = $this->app()->router()->provideRoute('Frontend','News','getSummaryMember',['id' => $News['fk_NMC']]);
	}
	
	/** adds the links update and/or delete to the comment when needed
	 * @param Comment $Comment
	 */
	private function addLinksUpdateDeleteToComment(Comment $Comment){
		
		//nom de l'auteur de la news
		$news_author = $this->_managers->getManagerOf('Comments')->getNewsAuthorUsingIdComment($Comment['id']);
		
		//recuperation du login de l'auteur du commentaire
		if ( $Comment[ 'fk_NMC' ] != null ) {
			$Member = $this->_managers->getManagerOf('Members')->getMemberFromId($Comment['fk_NMC']);
			$Comment->setAuteur($Member['login']);
		}
		
		if ( $this->getUser()->isAuthenticated() ) {
			if ( $this->loggedUserIsAdmin() ) {
				$Comment->link_update = $this->app()->router()->provideRoute( 'Backend', 'News', 'updateComment', [ 'id' => $Comment[ 'id' ] ] );
				$Comment->link_delete = $this->app()->router()->provideRoute( 'Backend', 'News', 'deleteCommentAjax', [ 'id' => $Comment[ 'id' ] ] );
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
					$Comment->link_delete = $this->app()->router()->provideRoute( 'Backend', 'News', 'deleteCommentAjax', [ 'id' => $Comment[ 'id' ] ] );
				}
			}
		}
	}
	
	/** adding comment form treatment
	 * @param HTTPRequest $Request
	 *
	 * @return Comment
	 */
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
				$Comment->setFk_NMC( $this->_managers->getManagerOf( 'Members' )->getIdMemberUsingLogin( $this->getUser()->getLogin() ) );
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
	
	/** inserts a comment with the ajax method
	 * @param HTTPRequest $Request
	 */
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
				if(null != $Comment['fk_NMC']) {
					$Comment->summary_link = $this->app()->router()->provideRoute( 'Frontend', 'News', 'getSummaryMember', [ 'id' => $Comment[ 'fk_NMC' ] ] );
				}
				$this->_page->addVar( 'Comment', $Comment );
			}
		}
		
		$this->_page->addVar('new_update_date',$this->_managers->getManagerOf('Comments')->getMaxEditionDate());
	}
	
	/** adds a list of comments to the page
	 * @param HTTPRequest $Request
	 */
	public function executeGetCommentList( HTTPRequest $Request ) {
		$this->run();
		
		$News_id = $Request->getData( 'news' );
		
		$date = $Request->postData('date');
		
		$Comments_a = $this->_managers->getManagerOf( 'Comments' )->getListOf( $News_id , $date );
		
		foreach ($Comments_a as $Comment) {
			$Comment->date_formated = $Comment->date()->format( 'd/m/Y à H\hi' );
			$this->addLinksUpdateDeleteToComment($Comment);
		}
		
		$this->_page->addVar('new_update_date',$this->_managers->getManagerOf('Comments')->getMaxEditionDate());
		$this->_page->addVar( 'Comments_a', $Comments_a );
	}
	
	/** action permettant de générer la page de résumé d'un membre : toutes ses news + ses commentaires et les news associées
	 * @param HTTPRequest $Request
	 *
	 */
	public function executeGetSummaryMember(HTTPRequest $Request){
		$this->run();
		
		
		$id_member = $Request->getData('id');
		$login_member = $this->_managers->getManagerOf('Members')->getLoginMemberFromId($id_member);
		$News_a = $this->_managers->getManagerOf('Members')->getNewsAndCommentUsingMemberId_a($id_member);
		
		foreach ($News_a as $News){
			$id_member = $News['fk_NMC'];
			$News_a[$News['id']]['auteur_news'] = $this->_managers->getManagerOf('Members')->getLoginMemberFromId($id_member);
			$dateAjout_news = new \DateTime($News['dateAjout']);
			$dateModif_news = new \DateTime($News['dateModif']);
			$date_formated_ajout_news = $dateAjout_news->format( 'd/m/Y à H\hi' );
			$date_formated_modif_news = $dateModif_news->format( 'd/m/Y à H\hi' );
			$News_a[$News['id']]['dateAjout'] = $date_formated_ajout_news;
			$News_a[$News['id']]['dateModif'] = $date_formated_modif_news;
			$News_a[$News['id']]['link_news'] = $this->app()->router()->provideRoute('Frontend','News','show',['id' => $News['id']]);
			$News_a[$News['id']]['link_summary'] = $this->app()->router()->provideRoute('Frontend','News','getSummaryMember',['id' => $News['fk_NMC']]);
			
			foreach ($News['comments'] as $Comment){
				$dateAjout_comment = new \DateTime($Comment['date_comment']);
				$dateModif_comment = new \DateTime($Comment['date_last_update']);
				$date_formated_ajout_comment = $dateAjout_comment->format( 'd/m/Y à H\hi' );
				$date_formated_modif_comment = $dateModif_comment->format( 'd/m/Y à H\hi' );
				$News_a[$News['id']]['comments'][$Comment['id_comment']]['date_comment'] = $date_formated_ajout_comment;
				$News_a[$News['id']]['comments'][$Comment['id_comment']]['date_last_update'] = $date_formated_modif_comment;
			}
			
		}
		
		$this->_page->addVar('number_news',$this->_managers->getManagerOf('News')->countNews($id_member));
		$this->_page->addVar('number_comments',$this->_managers->getManagerOf('Members')->countNumberCommentUsingIdMember($id_member));
		$this->_page->addVar('News_a',$News_a);
		$this->_page->addVar('login_member',$login_member);
	}
}