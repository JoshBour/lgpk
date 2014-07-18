<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 1/6/2014
 * Time: 8:11 μμ
 */

namespace Application\Controller;

use Application\Initializer\EntityManagerAwareInterface;
use Application\Initializer\VocabularyAwareInterface;
use Zend\Mvc\Controller\AbstractActionController;

class BaseController extends AbstractActionController implements EntityManagerAwareInterface, VocabularyAwareInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Application\Service\FileUtils
     */
    protected $fileUtilService;

    /**
     * @var array
     */
    protected $vocabulary;

    /**
     * Get the entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Set the entity manager
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get the file util service
     *
     * @return \Application\Service\FileUtils
     */
    public function getFileUtilService(){
        if(null === $this->fileUtilService)
            $this->fileUtilService = $this->getServiceLocator()->get('file_util_service');
        return $this->fileUtilService;
    }

    /**
     * @param array $vocabulary
     */
    public function setVocabulary($vocabulary)
    {
        $this->vocabulary = $vocabulary;
    }

    /**
     * @return array
     */
    public function getVocabulary()
    {
        return $this->vocabulary;
    }


} 