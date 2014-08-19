<?php
namespace Application\Controller;

use Application\Entity\ReferralApplication;
use Application\Entity\ReferralVisitor;
use Application\Model\SitemapXmlParser;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Session\Container;
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
    const CONTROLLER_NAME = "Application\Controller\Index";

    /**
     * @var \League\Service\League The league service
     */
    private $leagueService;

    /**
     * @var EntityRepository The referral application repository
     */
    private $referralApplicationRepository;

    /**
     * @var \Application\Repository\ReferralRepository The referral repository
     */
    private $referralRepository;

    /**
     * @var Form The referral form
     */
    private $referralForm;

    /**
     * @var Form The search form
     */
    private $searchForm;

    /**
     * @var \Application\Service\Search The search service
     */
    private $searchService;

    /**
     * @var EntityRepository The stream repository
     */
    private $streamRepository;

    /**
     * @var \Application\Service\Stream The stream service
     */
    private $streamService;

    /**
     * @var Form The tutorial form;
     */
    private $tutorialForm;

    /**
     * @var \League\Service\Tutorial The tutorial service
     */
    private $tutorialService;

    public function aboutAction()
    {
        return new ViewModel();
    }

    public function homeAction()
    {
        $form = $this->getSearchForm();
        $request = $this->getRequest();
        if ($referral = $this->params()->fromRoute('referral')) {
            $referralRepository = $this->getReferralRepository();
            $referral = $referralRepository->find(strtolower($referral));
            if ($referral) {
                if (!$referralRepository->findVisitorByIp($referral->getName(), $_SERVER["REMOTE_ADDR"])) {
                    $em = $this->getEntityManager();
                    $visitor = new ReferralVisitor($referral, $_SERVER["REMOTE_ADDR"]);
                    $referral->addVisitors($visitor);
                    $referral->increaseViews();
                    $em->persist($visitor);
                    $em->persist($referral);
                    $em->flush();
                };
            } else {
                return $this->notFoundAction();
            }
        }
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $params = array(
                    'summoner' => $data['search']['summoner'],
                    'region' => $data['search']['region'],
                    'position' => $data['search']['position'],
                    'hasCC' => $data['search']['hasCrowdControl'],
                    'hasMana' => $data['search']['hasMana']
                );
                if ($data['search']['opponent']) $params["opponent"] = $data['search']['opponent'];
                $this->redirect()->toRoute('result', $params);
            }
        }
        $session = new Container('summoner');
        $summoner = isset($session->summoner) ? $session->summoner : '';
        $region = isset($session->region) ? $session->region : '';
        return new ViewModel(array(
            "form" => $form,
            "summoner" => $summoner,
            "region" => $region,
            "bodyClass" => "homePage"
        ));
    }

    public function downloadAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $file = $this->params()->fromPost('image');
            $delete = $this->params()->fromPost('delete');

            if ($file) {
                if ($delete) {
                    unlink(ROOT_PATH . '/images/screenshots/' . $file . '.png');
                } else {
                    $decoded = base64_decode(str_replace('data:image/png;base64,', '', $file));
                    $name = 'results-' . rand(0, 1500000);

                    file_put_contents(ROOT_PATH . '/images/screenshots/' . $name . ".png", $decoded);
                    return new JsonModel(array(
                        "name" => $name
                    ));
                }
            }
        } else {
            $fileName = $this->params()->fromRoute("image");
            if ($fileName) {
                $file = ROOT_PATH . '/images/screenshots/' . $fileName . '.png';
//                $fileContents = file_get_contents($path);
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                ob_clean();
                flush();
                readfile($file);
                exit;
            }
        }
        return $this->notFoundAction();

    }

    public function tutorialsAction()
    {
        $form = $this->getTutorialForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($tutorials = $this->getTutorialService()->findTutorials($request->getPost())) {
                return array(
                    "tutorials" => $tutorials,
                    "bodyClass" => "tutorialPage"
                );
            } else {
                $vocabulary = $this->getVocabulary();
                $this->flashMessenger()->addMessage($vocabulary["ERROR_RESULTS_NOT_FOUND"]);
                return $this->redirect()->toRoute('tutorials');
            }
        }
        return array(
            "form" => $form
        );
    }

    public function streamsAction()
    {
        $streamId = $this->params()->fromRoute("streamId");
        if (!empty($streamId)) {
            $stream = $this->getStreamRepository()->find($streamId);
            if ($stream) {
                $this->getStreamService()->refreshCache($streamId);
//                echo "<pre>";
//                $isInGame = $this->getStreamService()->isInGame("DyrudeJstorm RMX","NA");
//                var_dump($isInGame);
//                echo "</pre>";
                $viewModel = new ViewModel(array(
                    "stream" => $stream
                ));
                $viewModel->setTemplate('stream_view');
                return $viewModel;
            } else {
                return $this->notFoundAction();
            }
        } else {
            $activeStreams = $this->getStreamService()->getActiveStreams();
            return array(
                "streams" => $activeStreams
            );
        }
    }

    public function referralAction()
    {
        $form = $this->getReferralForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            $vocabulary = $this->getVocabulary();
            if ($form->isValid()) {
                $referral = new ReferralApplication($data['referral']['email'], $data['referral']['uniqueId'], 0);
                $em = $this->getEntityManager();
                try {
                    $em->persist($referral);
                    $em->flush();
                    $this->flashMessenger()->addMessage($vocabulary["REFERRAL_SUCCESSFUL"]);
                } catch (\Exception $e) {
                    $this->flashMessenger()->addMessage($vocabulary["ERROR_REFERRAL"]);
                }
                $this->redirect()->toRoute('referral');
            }
        }
        return new ViewModel(array(
            "form" => $form,
            "bodyClass" => "referralPage"
        ));
    }

    public function resultAction()
    {
        $params = array(
            'summoner' => $this->params()->fromRoute("summoner"),
            'region' => $this->params()->fromRoute("region"),
            'opponent' => $this->params()->fromRoute("opponent"),
            'position' => $this->params()->fromRoute("position"),
            'hasCC' => $this->params()->fromRoute("hasCC"),
            'hasMana' => $this->params()->fromRoute("hasMana")
        );
        $searchService = $this->getSearchService();
        $session = new Container('summoner');
        $results = $searchService->getSearchResults($params['summoner'], $params['region'], $params);
        $suggestions = $results['suggestions'];
        if (!empty($suggestions["mainSuggestion"])) {
            $tutorials = $this->getTutorialService()->getTutorials($params, $suggestions["mainSuggestion"]);
        } else {
            $tutorials = array();
        }
        if (is_array($results)) {
            $session->summoner = $params['summoner'];
            $session->region = $params['region'];
            return new ViewModel(array(
                "results" => $suggestions,
                "found" => $results["found"],
                "params" => $params,
                "metaInfo" => $searchService->getResultMetaInfo($suggestions),
                "tutorials" => $tutorials,
                "bodyClass" => "resultPage",
                "pageTitle" => "LeaguePick - Champion Results"
            ));
        } else {
            $this->flashMessenger()->addMessage($results);
            $this->redirect()->toRoute("home");
        }
    }

    public function searchAction()
    {
        $params = array(
            'summoner' => $this->params()->fromRoute("summoner"),
            'region' => $this->params()->fromRoute("region"),
            'champion' => $this->params()->fromRoute("champion"),
            'opponent' => $this->params()->fromRoute("opponent"),
            'position' => $this->params()->fromRoute("position"),
            'hasCC' => $this->params()->fromRoute("hasCC"),
            'hasMana' => $this->params()->fromRoute("hasMana")
        );
        $vocabulary = $this->getVocabulary();
        $championStats = $this->getSearchService()->getUserChampionStats($params['summoner'], $params['region'], $params['champion']);
        $tutorials = $this->getTutorialService()->getTutorials(array(), $championStats);
        if ($championStats) {
            return new ViewModel(array(
                "champion" => $championStats,
                "params" => $params,
                "tutorials" => $tutorials
            ));
        } else {
            $this->flashMessenger()->addMessage($vocabulary["ERROR_SEARCH"]);
            $this->redirect()->toRoute("home");
        }
    }

    public function contactAction()
    {
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        $form = $this->getContactForm();
        $vocabulary = $this->getVocabulary();
        $contentRepository = $this->getContentRepository();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $email = $contentRepository->findOneBy(array("target" => "contactEmail"))->getContent();
                $password = $contentRepository->findOneBy(array("target" => "contactPassword"))->getContent();
                $message = new Message();
                $body = "From: " . $data['contact']['sender'] . "

                {$data['contact']['body']}
                ";
                $message->addTo($email)
                    ->addFrom($data['contact']['sender'])
                    ->setSubject($data['contact']['subject'])
                    ->addReplyTo($data['contact']['sender'])
                    ->setBody($body)
                    ->setEncoding("UTF-8");

                $transport = new SmtpTransport();
                $options = new SmtpOptions(array(
                    'host' => 'smtp.gmail.com',
                    'port' => '587',
                    'connection_class' => 'login',
                    'connection_config' => array(
                        'ssl' => 'tls',
                        'username' => $email, //info@infolightingco.com
                        'password' => $password, //7jhuP%KP
                    )
                ));
                $transport->setOptions($options);
                $transport->send($message);

                $this->flashMessenger()->addMessage($vocabulary["EMAIL_SUCCESS"]);
                return $this->redirect()->toRoute('contact');
            }

        }
        return new ViewModel(array(
            "form" => $form,
            "content" => $contentRepository->findOneBy(array("target" => "contact")),
            "useBlackLayout" => true,
            "bodyClass" => "contactPage",
            "pageTitle" => "Info - Contact Us"
        ));
    }

    public function sitemapAction()
    {
        $this->getResponse()->getHeaders()->addHeaders(array('Content-type' => 'application/xml; charset=utf-8'));
        $type = $this->params()->fromRoute('type');
        $sitemapXmlParser = new SitemapXmlParser();
        $sitemapXmlParser->begin();
        if (!$type) {
            $sitemapXmlParser->addHeader("sitemapindex");
            $sitemapXmlParser->addSitemap("http://www.leaguepick.com/sitemap/static");
        } else {
            if ($type == "static") {
                $pages = $this->getServiceLocator()->get('Config')['static_pages'];
                $sitemapXmlParser->addHeader("urlset");
                foreach ($pages as $page) {
                    $sitemapXmlParser->addUrl("http://www.leaguepick.com" . $page, "1.0");
                }
            } else {
                $index = $this->params()->fromRoute("index");

                $entities = $index == "products" ? $this->getProductRepository()->findAll() : $this->getPostRepository()->findAll();

                $sitemapXmlParser->addHeader("urlset", true);
                $i = 0;
                foreach ($entities as $entity) {
                    if ($i == 20) {
                        $sitemapXmlParser->show();
                        $i = 0;
                    }
                    $sitemapXmlParser->addEntityInfo($entity);
                    $i++;
                }
            }

        }
        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('application/index/sitemap.xml');
        $sitemapXmlParser->close();
        $sitemapXmlParser->show();
        return $view;
    }

    /**
     * The change language action
     * Route: /change-language/:lang
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function changeLanguageAction()
    {
        $session = new Container("base");
        $response = $this->getResponse();
        $lang = $this->params()->fromRoute('lang');
        if ($lang == 'en') {
            $session->locale = 'en_US';
        } else if ($lang == 'el') {
            $session->locale = 'el_GR';
        } else {
            $session->locale = null;
        }
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        $this->redirect()->toUrl($url);
        return $response;
    }

    /**
     * @return \League\Service\League
     */
    public function getLeagueService()
    {
        if (null === $this->leagueService)
            $this->leagueService = $this->getServiceLocator()->get('league_service');
        return $this->leagueService;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getReferralApplicationRepository()
    {
        if (null === $this->referralApplicationRepository)
            $this->referralApplicationRepository = $this->getEntityManager()->getRepository('Application\Entity\ReferralApplication');
        return $this->referralApplicationRepository;
    }

    /**
     * @return \Application\Repository\ReferralRepository
     */
    public function getReferralRepository()
    {
        if (null === $this->referralRepository)
            $this->referralRepository = $this->getEntityManager()->getRepository('Application\Entity\Referral');
        return $this->referralRepository;
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getReferralForm()
    {
        if (null === $this->referralForm)
            $this->referralForm = $this->getServiceLocator()->get('referral_form');
        return $this->referralForm;
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getTutorialForm()
    {
        if (null === $this->tutorialForm)
            $this->tutorialForm = $this->getServiceLocator()->get('tutorial_form');
        return $this->tutorialForm;
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getSearchForm()
    {
        if (null === $this->searchForm)
            $this->searchForm = $this->getServiceLocator()->get('search_form');
        return $this->searchForm;
    }

    public function getStreamRepository()
    {
        if (null === $this->streamRepository)
            $this->streamRepository = $this->getEntityManager()->getRepository('Application\Entity\Stream');
        return $this->streamRepository;
    }

    public function getStreamService()
    {
        if (null === $this->streamService)
            $this->streamService = $this->getServiceLocator()->get('stream_service');
        return $this->streamService;
    }

    /**
     * @return \Application\Service\Search
     */
    public function getSearchService()
    {
        if (null === $this->searchService)
            $this->searchService = $this->getServiceLocator()->get('search_service');
        return $this->searchService;
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
}
