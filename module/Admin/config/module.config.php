<?php
namespace Admin;

return array(
    'doctrine' => array(
        'driver' => array(
            'entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => 'entity',
                ),
            ),
        ),
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Admin\Entity\Admin',
                'identity_property' => 'username',
                'credential_property' => 'password',
                'credential_callable' => 'Admin\Entity\Admin::hashPassword'
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                    ),
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'tutorials' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/tutorials',
                        ),
                        'may_terminate' => false, // for now
                        'child_routes' => array(
                            'add' => array(
                                'type' => 'Zend\Mvc\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/add',
                                    'defaults' => array(
                                        'action' => 'add-tutorials',
                                    ),
                                ),
                            ),
                            'generate' => array(
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route' => '/generate[/:token]',
                                    'defaults' => array(
                                        'action' => 'generate',
                                        'nextToken' => null
                                    ),
                                ),
                            ),
                            'save' => array(
                                'type' => 'Zend\Mvc\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/save',
                                    'defaults' => array(
                                        'action' => 'save',
                                    ),
                                ),
                            ),
                        )
                    ),
                    'login' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '[/login]',
                            'defaults' => array(
                                'controller' => __NAMESPACE__ . '\Controller\Auth',
                                'action' => 'login',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'controller' => __NAMESPACE__ . '\Controller\Auth',
                                'action' => 'logout',
                            ),
                        ),
                    ),
                )
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'add_tutorial_form' => __NAMESPACE__ . '\Factory\AddTutorialFormFactory',
            'login_form' => __NAMESPACE__ . '\Factory\LoginFormFactory',
            'Zend\Authentication\AuthenticationService' => __NAMESPACE__ . '\Factory\AuthFactory',
        ),
        'invokables' => array(
            'authStorage' => __NAMESPACE__ . '\Model\AuthStorage',
        ),
        'aliases' => array(
            'auth_service' => 'Zend\Authentication\AuthenticationService'
        ),
    ),
    'controller_plugins' => array(
        'factories' => array(
            'admin' => __NAMESPACE__ . '\Factory\AdminPluginFactory',
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'admin' => __NAMESPACE__ . '\Factory\AdminViewHelperFactory',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            __NAMESPACE__ . '\Controller\Index' => __NAMESPACE__ . '\Controller\IndexController',
            __NAMESPACE__ . '\Controller\Auth' => __NAMESPACE__ . '\Controller\AuthController'
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'layout/admin' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    ),
);
