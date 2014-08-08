<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:56 πμ
 */

namespace Admin\Form;


use Application\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\NotEmpty;

class AddTutorialFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    /**
     * The search fieldset initializer
     */
    public function init()
    {
        parent::__construct("tutorial");

        $this->add(array(
            'type' => 'text',
            'name' => 'player',
            'options' => array(
                'label' => 'Player',
            )
        ));

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

        $this->add(array(
            'type' => 'text',
            'name' => 'videoId',
            'options' => array(
                'label' => 'Video Id',
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
            'player' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_PLAYER_EMPTY"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'opponent' => array(
                'required' => false,
            ),
            'position' => array(
                'required' => false,
            ),
            'videoId' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_VIDEO_ID_EMPTY"])
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