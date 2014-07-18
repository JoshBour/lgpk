<?php
/**
 * User: Josh
 * Date: 12/9/2013
 * Time: 6:13 μμ
 */

namespace Application\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManager;

class Translate extends AbstractPlugin
{

    /**
     * The service manager
     *
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * Translates the provided message using the zend translator
     *
     * @param string $message
     * @return string
     */
    public function __invoke($message)
    {
        return $this->serviceManager->get('translator')->translate($message);
    }

    /**
     * Set the service manager
     *
     * @param $serviceManager
     */
    public function setServiceManager($serviceManager){
        $this->serviceManager = $serviceManager;
    }
}