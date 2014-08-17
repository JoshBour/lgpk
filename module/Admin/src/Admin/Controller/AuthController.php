<?php
namespace Admin\Controller;

use Application\Controller\BaseController;
use Admin\Entity\Admin;
use Zend\Authentication\AuthenticationService;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class AuthController extends BaseController
{
    const CONTROLLER_NAME = "Admin\Controller\Auth";
    const ROUTE_LOGIN = "admin/login";
    const ROUTE_TUTORIALS_ADD = "admin/tutorials/add";
    const LAYOUT_ADMIN = "layout/admin";

    /**
     * The authentication service.
     *
     * @var AuthenticationService
     */
    private $authService = null;

    /**
     * The authentication storage.
     *
     * @var \Admin\Model\AuthStorage
     */
    private $authStorage = null;

    /**
     * The login form
     *
     * @var \Zend\Form\Form
     */
    private $loginForm;

    /**
     * The login action
     * Route: /
     *
     * @return mixed|\Zend\Http\Response|ViewModel
     */
    public function loginAction()
    {
        if (!$this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            $entity = new Admin();
            $loginForm = $this->getLoginForm();
            /**
             * @var $request \Zend\Http\Request
             */
            $request = $this->getRequest();
            $loginForm->bind($entity);
            if ($request->isPost()) {
                $data = $request->getPost();
                $loginForm->setData($data);
                if ($loginForm->isValid()) {
                    return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate',
                        'username' => $entity->getUsername(),
                        'password' => $entity->getPassword(),
                        'remember' => $data['user']['remember']));
                }
            }
            return new ViewModel(array(
                'form' => $loginForm,
                "pageTitle" => "Admin Login",
                "noAds" => true,
                "hideHeader" => true
            ));
        } else {
            return $this->redirect()->toRoute(self::ROUTE_TUTORIALS_ADD);
        }
    }

    /**
     * The authentication action.
     * Only accessed from the login and register actions.
     *
     * @return \Zend\Http\Response
     */
    public function authenticateAction()
    {
        $authService = $this->getAuthenticationService();
        /**
         * @var $adapter \Zend\Authentication\Adapter\AbstractAdapter
         */
        $adapter = $authService->getAdapter();

        $remember = $this->params()->fromRoute('remember', 1);
        $username = $this->params()->fromRoute('username');
        $password = $this->params()->fromRoute('password');
        $adapter->setIdentityValue($username);
        $adapter->setCredentialValue($password);
        $authResult = $authService->authenticate();
        if ($authResult->isValid()) {
            if ($remember == 1) {
                $this->getAuthStorage()->setRememberMe(1);
                #  $authService->setStorage($this->getAuthStorage());
            }
            $identity = $authResult->getIdentity();
            $authService->getStorage()->write($identity);
        } else {
            $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_INVALID_CREDENTIALS"]));
            return $this->redirect()->toRoute(self::ROUTE_LOGIN);
        }

        $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_WELCOME"]) . ', ' . $username);
        return $this->redirect()->toRoute(self::ROUTE_PRODUCTS);

    }

    /**
     * The logout action
     * Route: /logout
     *
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        if ($this->identity()) {
            $this->getAuthStorage()->forgetMe();
            $this->getAuthenticationService()->clearIdentity();
        }
        return $this->redirect()->toRoute(static::ROUTE_LOGIN);
    }

    /**
     * Get the authentication service
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (null === $this->authService) {
            $this->authService = $this->getServiceLocator()->get('auth_service');
        }
        return $this->authService;
    }

    /**
     * Get the auth storage
     *
     * @return \User\Model\AuthStorage
     */
    public function getAuthStorage()
    {
        if (null === $this->authStorage) {
            $this->authStorage = $this->getServiceLocator()->get('authStorage');
        }
        return $this->authStorage;
    }

    /**
     * Get the login form
     *
     * @return \Zend\Form\Form
     */
    public function getLoginForm()
    {
        if (null === $this->loginForm)
            $this->loginForm = $this->getServiceLocator()->get('login_form');
        return $this->loginForm;
    }
}
