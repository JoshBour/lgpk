<?php
namespace Admin\Form;

use Application\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class LoginFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    /**
     * The login fieldset constructor
     */
    public function init()
    {
        parent::__construct('admin');

        $this->add(array(
            'name' => 'username',
            'type' => 'text',
            'options' => array(
                'label' => $this->getTranslator()->translate($this->getVocabulary()["LABEL_USERNAME"])
            ),
            'attributes' => array(
                'placeholder' => $this->getTranslator()->translate($this->getVocabulary()["PLACEHOLDER_USERNAME"])
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'options' => array(
                'label' => $this->getTranslator()->translate($this->getVocabulary()["LABEL_PASSWORD"])
            ),
            'attributes' => array(
                'placeholder' => $this->getTranslator()->translate($this->getVocabulary()["PLACEHOLDER_PASSWORD"])
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'remember',
            'attributes' => array(
                'value' => "1"
            ),
            'options' => array(
                'label' => $this->getTranslator()->translate($this->getVocabulary()["LABEL_REMEMBER_ME"]),
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'username' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($this->getVocabulary()["ERROR_USERNAME_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 15,
                            'messages' => array(
                                StringLength::INVALID => $this->getTranslator()->translate($this->getVocabulary()["ERROR_USERNAME_INVALID"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'password' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($this->getVocabulary()["ERROR_PASSWORD_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 25,
                            'messages' => array(
                                StringLength::INVALID => $this->getTranslator()->translate($this->getVocabulary()["ERROR_PASSWORD_INVALID"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            )
        );
    }

}