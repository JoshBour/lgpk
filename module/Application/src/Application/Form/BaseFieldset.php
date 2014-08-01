<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 1/6/2014
 * Time: 9:26 μμ
 */

namespace Application\Form;


use Zend\Di\ServiceLocator;
use Zend\Form\Fieldset;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BaseFieldset extends Fieldset implements ServiceLocatorAwareInterface
{
    /**
     * The entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * The zend translator
     *
     * @var \Zend\I18n\Translator\Translator
     */
    protected $translator;

    /**
     * The lang vocabulary
     *
     * @var array
     */
    protected $vocabulary;

    /**
     * @var ServiceLocator
     */
    public $serviceLocator;

    public function getEntityManager()
    {
        if (null == $this->entityManager)
            $this->entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        return $this->entityManager;
    }

    public function getTranslator()
    {
        if (null == $this->translator)
            $this->translator = $this->getServiceLocator()->get('translator');
        return $this->translator;
    }

    public function getVocabulary()
    {
        if (null == $this->vocabulary)
            $this->vocabulary = $this->getServiceLocator()->get('Config')['vocabulary'];
        return $this->vocabulary;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @return \Zend\Di\ServiceLocator
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator->getServiceLocator();
    }


}