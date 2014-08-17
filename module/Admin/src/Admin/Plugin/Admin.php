<?php
/**
 * User: Josh
 * Date: 12/9/2013
 * Time: 6:13 μμ
 */

namespace Admin\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManager;
use Zend\Authentication\AuthenticationService;

class Admin extends AbstractPlugin{
    /**
     * The authentication service
     *
     * @var AuthenticationService
     */
    private $authService;

    /**
     * The entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * The service manager
     *
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * Returns the current active user or false if none exists.
     *
     * @return \Admin\Entity\Admin|Admin
     */
    public function __invoke(){
       return $this->getAdmin();
    }

    /**
     * Returns the active user entity
     *
     * @return bool|\Admin\Entity\Admin
     */
    public function getAdmin(){
        $em = $this->getEntityManager();
        $auth = $this->getAuthService();
        if($auth->hasIdentity()){
            $admin = $em->getRepository('Admin\Entity\Admin')->find($auth->getIdentity()->getUserId());
        }else{
            $admin = false;
        }
        return $admin;
    }

    /**
     * Get the authentication service
     *
     * @return AuthenticationService
     */
    public function getAuthService(){
        if(null === $this->authService)
            $this->authService = $this->getServiceManager()->get('auth_service');
        return $this->authService;
    }

    /**
     * Get the entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
        if(null === $this->entityManager)
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        return $this->entityManager;
    }

    /**
     * Get the service manager;
     *
     * @return ServiceManager
     */
    public function getServiceManager(){
        return $this->serviceManager;
    }

    /**
     * Set the service manager
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager($serviceManager){
        $this->serviceManager = $serviceManager;
    }
}