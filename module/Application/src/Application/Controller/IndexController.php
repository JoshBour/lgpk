<?php
namespace Application\Controller;

use Application\Model\SitemapXmlParser;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 * @package Application\Controller
 * @method Request getRequest()
 * @method Response getResponse()
 */
class IndexController extends BaseController
{
    const CONTROLLER_NAME = "Application\Controller\Index";

    private $leagueService;

    private $searchForm;

    private $searchService;

    public function homeAction()
    {
        $form = $this->getSearchForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $params = array(
                    'summoner' => $data['search']['summoner'],
                    'region' => $data['search']['region'],
                );
                if($data['search']['opponent']) $params["opponent"] = $data['search']['opponent'];
                $this->redirect()->toRoute('result', $params);
            }
        }
        $session = new Container('summoner');
        $summoner = isset($session->summoner) ? $session->summoner : '';
        $region = isset($session->region) ? $session->region : '';
        return new ViewModel(array(
            "form" => $form,
            "summoner" => $summoner,
            "region" => $region
        ));
    }

    public function resultAction()
    {
        $summoner = $this->params()->fromRoute("summoner");
        $region = $this->params()->fromRoute("region");
        $opponent = $this->params()->fromRoute("opponent");
        $session = new Container('summoner');
        $results = $this->getSearchService()->getSearchResults($summoner,$region,$opponent);
        if (is_array($results)) {
            $session->summoner = $summoner;
            $session->region = $region;
            return new ViewModel(array(
                "results" => $results,
                "summoner" => $summoner,
                "region" => $region,
                "opponent" => $opponent
            ));
        } else {
            $this->flashMessenger()->addMessage($results);
            $this->redirect()->toRoute("home");
        }
    }

    public function searchAction()
    {
        $summoner = $this->params()->fromRoute("summoner");
        $region = $this->params()->fromRoute("region");
        $champion = $this->params()->fromRoute("champion");
        $vocabulary = $this->getVocabulary();
        $championStats = $this->getSearchService()->getUserChampionStats($summoner, $region, $champion);
        if ($championStats) {
            return new ViewModel(array(
                "champion" => $championStats,
                "region" => $region,
                "summoner" => $summoner,
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
     * @return \Zend\Form\Form
     */
    public function getSearchForm()
    {
        if (null === $this->searchForm)
            $this->searchForm = $this->getServiceLocator()->get('search_form');
        return $this->searchForm;
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
}
