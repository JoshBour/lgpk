<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 2:11 πμ
 */

namespace Application\Form;


use Zend\Form\Form;

class ReferralForm extends Form{
    /**
     * The post form constructor
     */
    public function __construct(){
        parent::__construct("referralForm");

        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'standardForm'
        ));

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
            'referral' => array(
                'email',
                'uniqueId'
            )
        ));
    }
} 