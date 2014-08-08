<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 16/3/2014
 * Time: 8:15 μμ
 */

namespace Admin\Factory;

use Admin\Form\AddTutorialFieldset;
use Admin\Form\AddTutorialForm;
use League\Entity\Tutorial;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class AddTutorialFormFactory implements FactoryInterface
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
        $entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        /**
         * @var AddTutorialFieldset $fieldset
         */
        $fieldset = $formManager->get('Admin\Form\AddTutorialFieldset');
        $form = new AddTutorialForm();
        $hydrator = new DoctrineHydrator($entityManager, 'League\Entity\Tutorial');


        $fieldset->setUseAsBaseFieldset(true)
                ->setHydrator($hydrator)
                ->setObject(new Tutorial());

        $form->add($fieldset)
            ->setInputFilter(new InputFilter())
            ->setHydrator($hydrator);
        return $form;
    }

}

