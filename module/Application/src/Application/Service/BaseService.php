<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:44 πμ
 */

namespace Application\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class BaseService implements ServiceManagerAwareInterface
{
    /**
     * The entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * The service manager
     *
     * @var ServiceManager;
     */
    private $serviceManager;

    /**
     * The zend translator
     *
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    /**
     * Magic method used to return entity repositories
     * @param $name
     * @param $variables
     * @return \Doctrine\ORM\EntityRepository
     */
    public function __call($name,$variables){
        $entity = ucfirst(substr($name,3,strlen($name) - 13));
        $namespace = ucfirst($variables[0]);
        return $this->getEntityManager()->getRepository("{$namespace}\\Entity\\{$entity}");
    }

    /**
     * Get the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
        if(null === $this->entityManager)
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        return $this->entityManager;
    }

    /**
     * Set service manager
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Get the service manager
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Get the zend translator
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (null === $this->translator)
            $this->translator = $this->getServiceManager()->get('translator');
        return $this->translator;
    }
} 