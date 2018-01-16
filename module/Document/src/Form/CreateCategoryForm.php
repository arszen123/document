<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/11/18
 * Time: 12:01 PM
 */

namespace Document\Form;


use Zend\Form\Form;

class CreateCategoryForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('createCategoryForm');

        $this->add([
            'name' => 'name',
            'type' => 'text',
            'options' => [
                'label' => 'Name',
            ],
            'attributes' => [
                'placeholder' => 'Category name',
                'id' => 'name',
                'class' => 'form-control'
            ]
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Create category',
                'id'    => 'submitbutton',
                'class' => 'btn btn-primary'
            ],
        ]);
    }
}