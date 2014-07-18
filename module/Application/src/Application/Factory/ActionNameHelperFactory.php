<?php
namespace Application\Factory;

use Application\View\Helper\ActionName;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ActionNameHelperFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ActionName($serviceLocator->getServiceLocator()->get('application')->getMvcEvent()->getRouteMatch());
    }

}

