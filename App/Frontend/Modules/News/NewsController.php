<?php


namespace App\Frontend\Modules\News;

use Model\NewsManager;
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \OCFram\FormHandler;

class NewsController extends BackController {
	/** affichage des news sur la page d'accueil
	 * @param HTTPRequest $request
	 */
	public function executeIndex( HTTPRequest $request ) {
		$nbNews       = $this->_app->config()->get( 'nombre_news' );
		$nbCaracteres = $this->_app->config()->get( 'nombre_caracteres' );
		
		$this->_page->addVar( 'title', 'Liste des ' . $nbNews . ' dernières news' );
		
		//on récupère le manager des news
		/** @var NewsManager $manager */
		$manager = $this->_managers->getManagerOf( 'News' );
		
		$listeNews = $manager->getList( 0, $nbNews );
		
		foreach ( $listeNews as $news ) {
			if ( strlen( $news->contenu() ) > $nbCaracteres ) {
				$debut = substr( $news->contenu(), 0, $nbCaracteres );
				$debut = substr( $debut, 0, strrpos( $debut, ' ' ) ) . '...';
				
				$news->setContenu( $debut );
			}
		}
		
		// On ajoute la variable $listeNews à la vue.
		$this->_page->addVar( 'listeNews', $listeNews );
	}
	
	/** affichage d'une news est ses commentaires
	 * @param HTTPRequest $request
	 */
	public function executeShow( HTTPRequest $request ) {
		$manager = $this->_managers->getManagerOf( 'News' );
		
		$news = $manager->getNews( $request->getData( 'id' ) );
		
		if ( empty( $news ) ) {
			$this->_app->httpResponse()->redirect404();
		}
		
		$this->_page->addVar( 'title', $news->titre() );
		$this->_page->addVar( 'news', $news );
		$this->_page->addVar( 'comments', $this->_managers->getManagerOf( 'Comments' )->getListOf( $news->id() ) );
	}
	
	/** insertion d'un commentaire
	 * @param HTTPRequest $request
	 */
	public function executeInsertComment( HTTPRequest $request ) {
		if ( $request->method() == 'POST' ) {
			if($request->postExists('auteur')) {
				$comment = new Comment( [
					'news'    => $request->getData( 'news' ),
					'auteur'  => $request->postData( 'auteur' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
			}
			else{
				$comment = new Comment( [
					'news'    => $request->getData( 'news' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
				$comment->setAuteur($this->_app->user()->getLogin());
			}
		}
		else {
			$comment = new Comment;
		}
		
		$formBuilder = new CommentFormBuilder( $comment );
		$formBuilder->build($this->_app->user(),$this->_managers->getManagerOf('Members'));
		
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