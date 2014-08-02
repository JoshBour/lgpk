<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:56 πμ
 */

namespace Application\Form;


use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;

class ReferralFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    /**
     * The search fieldset initializer
     */
    public function init()
    {
        parent::__construct("referral");

        $this->add(array(
            'type' => 'email',
            'name' => 'email',
            'options' => array(
                'label' => 'Email',
            )
        ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $vocabulary = $this->getVocabulary();
        return array(
            'email' => array(
                'required' => true,
                'validators' => array(
                    array (
                        'name' => 'Regex',
                        'options' => array(
                            'pattern'=>'/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/',
                            'messages' => array(
                                Regex::NOT_MATCH    => $this->getTranslator()->translate($vocabulary["ERROR_EMAIL_INVALID"]),
                            ),
                        ),
                        'break_chain_on_failure' => true
                    ),
                    array(
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                EmailAddress::INVALID_FORMAT => $this->getTranslator()->translate($vocabulary["ERROR_EMAIL_INVALID"]),
                            )
                        )
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->getEntityManager()->getRepository('Application\Entity\ReferralApplication'),
                            'fields' => 'email',
                            'messages' => array(
                                'objectFound' => $this->getTranslator()->translate($vocabulary["ERROR_EMAIL_EXISTS"])
                            )
                        )
                    ),
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_EMAIL_EMPTY"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
        );
    }


} 