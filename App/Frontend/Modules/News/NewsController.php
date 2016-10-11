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
	const MEMBER_STATUS_ADMIN = 1;
	/** affichage des news sur la page d'accueil
	 *
	 * @param HTTPRequest $request
	 */
	public function executeIndex( HTTPRequest $request ) {
		$this->run();
		
		$nbNews       = $this->_app->config()->get( 'nombre_news' );
		$nbCaracteres = $this->_app->config()->get( 'nombre_caracteres' );
		
		$this->_page->addVar( 'title', 'Liste des ' . $nbNews . ' dernières news' );
		
		//on récupère le manager des news
		/** @var NewsManager $manager */
		$manager = $this->_managers->getManagerOf( 'News' );
		
		$listeNews = $manager->getList( 0, $nbNews );
		
		foreach ( $listeNews as $news ) {
			$debut = mb_strimwidth( $news->contenu(), 0, $nbCaracteres, "..." );
			$news->setContenu( $debut );
		}
		
		//on passe à la vue les informations de lien, de titre et de contenu
		$links = [];
		foreach ( $listeNews as $news ) {
			$links[ $news[ 'titre' ] . '|' . $news[ 'contenu' ] ] = $this->app()->router()->provideRoute( 'Frontend', 'News', 'show', [ 'id' => $news[ 'id' ] ] );
		}
		$this->_page->addVar( 'links', $links );
	}
	
	/** affichage d'une news est ses commentaires
	 *
	 * @param HTTPRequest $request
	 */
	public function executeShow( HTTPRequest $request ) {
		$this->run();
		
		$manager = $this->_managers->getManagerOf( 'News' );
		
		$news = $manager->getNews( $request->getData( 'id' ) );
		
		if ( empty( $news ) ) {
			$this->_app->httpResponse()->redirect404();
		}
		
		$this->_page->addVar( 'title', $news->titre() );
		//passage de la news
		$this->_page->addVar( 'news', $news );
		//passage de la liste des commentaires
		$comments = $this->_managers->getManagerOf( 'Comments' )->getListOf( $news->id() );
		$this->_page->addVar( 'comments', $comments );
		//passage du nom de l'auteur de la news
		$news_author = $this->_managers->getManagerOf( 'News' )->getLoginFromNewsId( $news->id() );
		$this->_page->addVar( 'news_author', $news_author );
		
		//passage des liens
		$links = array();
		foreach ( $comments as $comment ) {
			if ( $comment[ 'member' ] != null ) {
				$comment_author = $this->_managers->getManagerOf( 'Members' )->getLoginMemberFromId( $comment[ 'member' ] );
			}
			else {
				$comment_author = $comment[ 'auteur' ];
			}
			if ( $this->getUser()->isAuthenticated() ) {
				if ( $this->getUser()->getStatus() == 'admin' ) {
					$links[ $comment_author . '|' . $comment[ 'date' ]->format( 'd/m/Y à H\hi' ) . '|' . $comment[ 'contenu' ] ][] = $this->app()->router()
																																		  ->provideRoute( 'Backend', 'News', 'updateComment', [ 'id' => $comment[ 'id' ] ] );
					$links[ $comment_author . '|' . $comment[ 'date' ]->format( 'd/m/Y à H\hi' ) . '|' . $comment[ 'contenu' ] ][] = $this->app()->router()
																																		  ->provideRoute( 'Backend', 'News', 'deleteComment', [ 'id' => $comment[ 'id' ] ] );
				}
				else {
					if ( $comment[ 'member' ] != null ) {
						$status = $this->_managers->getManagerOf( 'Members' )->getStatusMemberFromId( $comment[ 'member' ] );
						if ( $status != self::MEMBER_STATUS_ADMIN
							 && ( $this->getUser()->getLogin() == $comment[ 'auteur' ] || $this->getUser()->getLogin() == $news_author
								  || $this->getUser()->getStatus() == 'admin' )
						) {
							$links[ $comment_author . '|' . $comment[ 'date' ]->format( 'd/m/Y à H\hi' ) . '|' . $comment[ 'contenu' ] ][] = $this->app()->router()
																																				  ->provideRoute( 'Backend', 'News', 'updateComment', [ 'id' => $comment[ 'id' ] ] );
							$links[ $comment_author . '|' . $comment[ 'date' ]->format( 'd/m/Y à H\hi' ) . '|' . $comment[ 'contenu' ] ][] = $this->app()->router()
																																				  ->provideRoute( 'Backend', 'News', 'deleteComment', [ 'id' => $comment[ 'id' ] ] );
						}
					}
				}
			}
			else {
				$links[ $comment_author . '|' . $comment[ 'date' ]->format( 'd/m/Y à H\hi' ) . '|' . $comment[ 'contenu' ] ][] = null;
			}
		}
		$this->_page->addVar( 'links', $links );
		//var_dump($this->app()->router()->routes());
		$this->_page->addVar('add_comment',$this->app()->router()->provideRoute('Frontend','News','insertComment',['id' => $news['id']]));
	}
	
	/** insertion d'un commentaire
	 *
	 * @param HTTPRequest $request
	 */
	public function executeInsertComment( HTTPRequest $request ) {
		$this->run();
		
		if ( $request->method() == 'POST' ) {
			if ( $request->postExists( 'auteur' ) ) {
				$comment = new Comment( [
					'news'    => $request->getData( 'news' ),
					'auteur'  => $request->postData( 'auteur' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
			}
			else {
				$comment = new Comment( [
					'news'    => $request->getData( 'news' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
				$comment->setMember( $this->_managers->getManagerOf( 'Members' )->getIdMemberFromLogin( $this->getUser()->getLogin() ) );
			}
		}
		else {
			$comment = new Comment;
		}
		
		$formBuilder = new CommentFormBuilder( $comment );
		$formBuilder->build( $this->_app->user(), $this->_managers->getManagerOf( 'Members' ) );
		
		$form = $formBuilder->form();
		
		$formHandler = new FormHandler( $form, $this->_managers->getManagerOf( 'Comments' ), $request );
		
		if ( $formHandler->process() ) {
			$this->_app->user()->setFlash( 'Le commentaire a bien été ajouté, merci !' );
			$this->_app->httpResponse()->redirect( 'news-' . $request->getData( 'news' ) . '.html' );
		}
		
		$this->_page->addVar( 'title', 'Ajout d\'un commentaire' );
		$this->_page->addVar( 'comment', $comment );
		$this->_page->addVar( 'form', $form->createView() );
	}
}