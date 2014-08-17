<?php
namespace Admin\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    /**
     * The login form constructor
     */
    public function __construct()
    {
        parent::__construct('loginForm');

        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'standardForm'
        ));
//
//        $this->add(array(
//            'name' => 'security',
//            'type' => 'Zend\Form\Element\Csrf'
//        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit'
        ));

        $this->setValidationGroup(array(
//            'security',
            'admin' => array(
                'username',
                'password'
            )
        ));
    }
}