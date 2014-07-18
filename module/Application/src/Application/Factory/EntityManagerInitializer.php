<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 1/6/2014
 * Time: 7:55 μμ
 */

namespace Application\Factory;


use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Initializer\EntityManagerAwareInterface;

class EntityManagerInitializer implements InitializerInterface{

    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceof EntityManagerAwareInterface) {
            $entityManager = $serviceLocator->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $instance->setEntityManager($entityManager);
        }
    }
}