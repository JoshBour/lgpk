<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 2:11 πμ
 */

namespace Admin\Form;


use Zend\Form\Form;

class AddTutorialForm extends Form{
    /**
     * The post form constructor
     */
    public function __construct(){
        parent::__construct("addTutorialForm");

        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'standardForm'
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit'
        ));

        $this->setValidationGroup(array(
            'tutorial' => array(
                'player',
                'champion',
                'opponent',
                'position',
                'videoId',
            )
        ));
    }
} 