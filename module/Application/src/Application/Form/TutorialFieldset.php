<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:56 πμ
 */

namespace Application\Form;


use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\NotEmpty;

class TutorialFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    /**
     * The search fieldset initializer
     */
    public function init()
    {
        parent::__construct("tutorial");

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'champion',
            'attributes' => array(
                'class' => 'selectable championSelect'
            ),
            'options' => array(
                'label' => 'Champion',
                'object_manager' => $this->getEntityManager(),
                'target_class' => 'League\Entity\Champion',
                'property' => 'name',
                'disable_inarray_validator' => true,
                'is_method' => true,
                'find_method' => array(
                    'name' => 'findBy',
                    'params' => array(
                        'criteria' => array(),
                        // Use key 'orderBy' if using ORM
                        'orderBy' => array('name' => 'ASC'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'opponent',
            'attributes' => array(
                'class' => 'selectable championSelect'
            ),
            'options' => array(
                'label' => 'Opponent',
                'object_manager' => $this->getEntityManager(),
                'empty_option' => 'None',
                'target_class' => 'League\Entity\Champion',
                'property' => 'name',
                'disable_inarray_validator' => true,
                'is_method' => true,
                'find_method' => array(
                    'name' => 'findBy',
                    'params' => array(
                        'criteria' => array(),
                        // Use key 'orderBy' if using ORM
                        'orderBy' => array('name' => 'ASC'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'position',
            'options' => array(
                'label' => 'Position',
                'empty_option' => 'None',
                'value_options' => array(
                    'top' => 'Top',
                    'middle' => 'Middle',
                    'jungle' => 'Jungle',
                    'marksman' => 'Marksman',
                    'support' => 'Support',
                ),
            ),
            'attributes' => array(
                'class' => 'selectable'
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

            'summoner' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_OPPONENT_EMPTY"])
                            )
                        )
                    ),
                ),
            ),
            'opponent' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_OPPONENT_EMPTY"])
                            )
                        )
                    ),
                ),
            ),
            'position' => array(
                'required' => false,
            ),
        );
    }


} 