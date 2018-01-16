<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/12/18
 * Time: 9:19 PM
 */

namespace Document\Form;


use Zend\Form\Element;
use Zend\Form\Form;

class EditPermissionForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('editPermissionForm');

        $this->add([
            'name' => 'upload',
            'type' => Element\Checkbox::class,
            'required' => true,
            'options' => [
                'label' => 'Upload',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
            'attributes' => [
                'id' => 'upload'
            ]
        ]);
        $this->add([
            'name' => 'download',
            'type' => Element\Checkbox::class,
            'required' => true,
            'options' => [
                'label' => 'Download',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
            'attributes' => [
                'id' => 'download'
            ]
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Save',
                'id'    => 'submitbutton',
                'class' => 'btn btn-default'
            ],
        ]);
    }


}