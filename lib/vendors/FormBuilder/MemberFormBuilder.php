<?php

namespace FormBuilder;

use OCFram\ConfirmationValidator;
use \OCFram\FormBuilder;
use OCFram\Manager;
use \OCFram\StringField;
use \OCFram\MaxLengthValidator;
use \OCFram\NotNullValidator;
use \OCFram\PasswordField;
use \OCFram\EmailField;
use \OCFram\UniqueValidator;
use \OCFram\USer;

class MemberFormBuilder extends FormBuilder {
	
	public function build(User $user = null, Manager $manager = null ) {
		
		$this->_form->add(new StringField( [
			'label'      => 'Pseudo',
			'name'       => 'login',
			'maxLength'  => 20,
			'validators' => [
				new MaxLengthValidator( 'Le pseudo spécifié est trop long', 20 ),
				new NotNullValidator( 'Merci de spécifier un pseudo' ),
				new UniqueValidator( 'Ce login existe déjà !', $manager, 'existsMemberUsingLogin' ),
			],
		] ) );
		
		$password_field = new PasswordField( [
			'label'      => 'Mot de Passe',
			'name'       => 'password',
			'maxLength'  => 20,
			'validators' => [
				new MaxLengthValidator( 'Le mot de passe spécifié est trop long', 20 ),
				new NotNullValidator( 'Merci de spécifier un mot de passe' ),
			],
		] ) ;
		
		$this->_form->add($password_field);
		
		
		$this->_form->add( new PasswordField( [
			'label'      => 'Confirmation Mot de Passe',
			'name'       => 'password_confirmation',
			'maxLength'  => 20,
			'validators' => [
				new MaxLengthValidator( 'Le mot de passe spécifié est trop long', 20 ),
				//new NotNullValidator( 'Merci de confirmer votre mot de passe' ),
				new ConfirmationValidator('Erreur : mots de passe différents',$password_field),
			],
		] ) );
		
			
		$email_field = new EmailField( [
			'label'      => 'E-mail',
			'name'       => 'email',
			'maxLength'  => 50,
			'validators' => [
				new UniqueValidator( 'Cet e-mail existe déjà !', $manager, 'existsMemberUsingEmail' ),
				new NotNullValidator( 'Merci de renseigner un E-mail' ),
			],
		] ) ;
		
		$this->_form->add($email_field);
		
		$this->_form->add( new EmailField( [
			'label'      => 'Confirmation',
			'name'       => 'email_confirmation',
			'maxLength'  => 50,
			'validators' => [
				new NotNullValidator( 'Merci de confirmer votre E-mail' ),
				new ConfirmationValidator( 'Erreur : e-mails différents', $email_field),
			],
		] ) );
	}
}

