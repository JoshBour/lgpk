<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 16/3/2014
 * Time: 8:15 μμ
 */

namespace Application\Factory;

use Application\Form\ReferralFieldset;
use Application\Form\ReferralForm;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReferralFormFactory implements FactoryInterface
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
         * @var ReferralFieldset $fieldset
         */
        $fieldset = $formManager->get('Application\Form\ReferralFieldset');
        $form = new ReferralForm();

        $form->add($fieldset)
            ->setInputFilter(new InputFilter());
        return $form;
    }

}

