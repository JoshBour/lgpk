<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 16/3/2014
 * Time: 8:15 μμ
 */

namespace Application\Factory;

use Application\Form\SearchFieldset;
use Application\Form\SearchForm;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SearchFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $formManager = $serviceLocator->get('FormElementManager');
        /**
         * @var SearchFieldset $fieldset
         */
        $fieldset = $formManager->get('Application\Form\SearchFieldset');
        $form = new SearchForm();

        $form->add($fieldset)
            ->setInputFilter(new InputFilter());
        return $form;
    }

}

