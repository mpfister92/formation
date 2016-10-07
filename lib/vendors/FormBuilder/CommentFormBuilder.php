<?php

namespace FormBuilder;

use \OCFram\FormBuilder;
use \OCFram\StringField;
use \OCFram\TextField;
use \OCFram\MaxLengthValidator;
use \OCFram\NotNullValidator;
use OCFram\Manager;
use OCFram\UniqueValidator;
use \OCFram\USer;

class CommentFormBuilder extends FormBuilder {
	public function build(User $user = null, Manager $manager = null) {
		if(!$user->getLogin() || $user->getStatus()== 'admin') {
			$this->_form->add( new StringField( [
				'label'      => 'Auteur',
				'name'       => 'auteur',
				'maxLength'  => 50,
				'validators' => [
					new MaxLengthValidator( 'L\'auteur spécifié est trop long', 50 ),
					new NotNullValidator( 'Merci de spécifier l\'auteur du commentaire' ),
					new UniqueValidator( 'Vous ne pouvez pas emprunter ce pseudo pour votre commentaire', $manager, 'existsMemberUsingLogin' ),
				],
			] ) );
		}
		$this->_form->add( new TextField( [
			'label'      => 'Contenu',
			'name'       => 'contenu',
			'rows'       => 7,
			'cols'       => 50,
			'validators' => [ new NotNullValidator( 'Merci de spécifier le contenu du commentaire' ), ],
		] ) );
	}
}