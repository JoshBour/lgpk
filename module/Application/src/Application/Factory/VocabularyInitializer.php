<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 1/6/2014
 * Time: 7:55 μμ
 */

namespace Application\Factory;


use Application\Initializer\VocabularyAwareInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class VocabularyInitializer implements InitializerInterface
{

    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceof VocabularyAwareInterface) {
            $config = $serviceLocator->getServiceLocator()->get('Config');
            $instance->setVocabulary($config['vocabulary']);
        }
    }
}