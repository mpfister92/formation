<?php

namespace OCFram;


abstract class FormBuilder
{
    protected $_form;

    public function __construct(Entity $entity)
    {
        $this->setForm(new Form($entity));
    }

    abstract public function build(User $user = null, Manager $manager = null);

    public function form()
    {
        return $this->_form;
    }

    public function setForm(Form $form)
    {
        $this->_form = $form;
    }
}