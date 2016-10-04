<?php

namespace OCFram;

use \OCFram\FormBuilder;
use \OCFram\StringField;
use \OCFram\TextField;
use \OCFram\MaxLengthValidator;
use \OCFram\NotNullValidator;

class CommentFormBuilder extends FormBuilder {
    public function build(){
        $this->_form->add(new StringField([
            'label' => 'Auteur',
            'name' => 'auteur',
            'maxLength' => 50,
            'validators' => [new MaxLengthValidator('L\'auteur spécifié est trop long',50),
                new NotNullValidator('Merci de spécifier l\'auteur du commentaire'),],
        ]));
        $this->_form->add(new TextField([
            'label' => 'Contenu',
            'name' => 'contenu',
            'rows' => 7,
            'cols' => 50,
            'validators' => [new NotNullValidator('Merci de spécifier votre commentaire'),],
        ]));
    }
}