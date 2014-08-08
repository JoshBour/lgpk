<?php
namespace Admin\Controller;


use Application\Controller\BaseController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Form\Form;
use Doctrine\ORM\EntityRepository;

/**
 * Class IndexController
 * @package Admin\Controller
 * @method Request getRequest()
 * @method Response getResponse()
 */
class IndexController extends BaseController
{
    const CONTROLLER_NAME = "Admin\Controller\Index";

    /**
     * @var Form The add tutorial form
     */
    private $addTutorialForm;

    /**
     * @var EntityRepository The tutorial repository
     */
    private $tutorialRepository;

    /**
     * @var \League\Service\Tutorial The tutorial service
     */
    private $tutorialService;

    /**
     * @var \Youtube\Service\Youtube The youtube service
     */
    private $youtubeService;

    public function generateAction()
    {
        $nextToken = $this->params()->fromRoute("token", null);
        // LoLProGameplayTV
        // lolprovods CO4FEAA
        //leagueofporos
        // lolsoloq
        // LoLProGameplayTV
        $videoAssoc = $this->getTutorialService()->getYoutuberUploads("FissMortunePink", $nextToken);
        return new ViewModel(array(
            "nextToken" => $videoAssoc["nextToken"],
            "videos" => $videoAssoc["videos"],
            "previousToken" => $videoAssoc["previousToken"]
        ));
    }

    public function saveAction(){
        $request = $this->getRequest();
        if($request->isXmlHttpRequest()){
            $data = $request->getPost();
            $vocabulary = $this->getVocabulary();
            $success = 0;
            if($this->getTutorialService()->save($data)){
                $message = $vocabulary["MESSAGE_TUTORIAL_ADD_SUCCESS"];
                $success = 1;
            }else{
                $message = $vocabulary["ERROR_TUTORIAL_ADD"];
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        }
        return $this->notFoundAction();
    }

    public function addTutorialsAction()
    {
        $form = $this->getAddTutorialForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $vocabulary = $this->getVocabulary();
            if ($this->getTutorialService()->create($data)) {
                $this->flashMessenger()->addMessage($vocabulary["MESSAGE_TUTORIAL_ADD_SUCCESS"]);
                $this->redirect()->toRoute('admin/tutorials/add');
            } else {
                $this->flashMessenger()->addMessage($vocabulary["ERROR_TUTORIAL_ADD"]);
            }
        }
        return new ViewModel(array(
            "form" => $form
        ));
    }

    /**
     * @return Form
     */
    public function getAddTutorialForm()
    {
        if (null === $this->addTutorialForm)
            $this->addTutorialForm = $this->getServiceLocator()->get('add_tutorial_form');
        return $this->addTutorialForm;
    }

    /**
     * @return EntityRepository
     */
    public function getTutorialRepository()
    {
        if (null === $this->tutorialRepository)
            $this->tutorialRepository = $this->getEntityManager()->getRepository('League\Entity\Tutorial');
        return $this->tutorialRepository;
    }

    /**
     * @return \League\Service\Tutorial
     */
    public function getTutorialService()
    {
        if (null === $this->tutorialService)
            $this->tutorialService = $this->getServiceLocator()->get('tutorial_service');
        return $this->tutorialService;
    }

    /**
     * @return \Youtube\Service\Youtube
     */
    public function getYoutubeService()
    {
        if (null === $this->youtubeService)
            $this->youtubeService = $this->getServiceLocator()->get('youtube_service');
        return $this->youtubeService;
    }
}
