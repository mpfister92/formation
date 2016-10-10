<?php

namespace FormBuilder;

use \OCFram\FormBuilder;
use \OCFram\StringField;
use \OCFram\TextField;
use \OCFram\MaxLengthValidator;
use \OCFram\NotNullValidator;
use OCFram\Manager;
use \OCFram\USer;

class NewsFormBuilder extends FormBuilder {
	public function build(User $user = null,Manager $manager = null) {
		$this->_form->add( new StringField( [
			'label'      => 'Titre',
			'name'       => 'titre',
			'maxLength'  => 100,
			'validators' => [
				new MaxLengthValidator( 'Le titre spécifié est trop long', 100 ),
				new NotNullValidator( 'Merci de spécifier un titre' ),
			],
		] ) );
		
		$this->_form->add( new TextField( [
			'label'      => 'Contenu',
			'name'       => 'contenu',
			'rows'       => 8,
			'cols'       => 60,
			'validators' => [ new NotNullValidator( 'Merci de spécifier le contenu de la news' ), ],
		] ) );
	}
}