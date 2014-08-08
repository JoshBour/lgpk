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
                )
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'add_tutorial_form' => __NAMESPACE__ . '\Factory\AddTutorialFormFactory',
        ),
//        'invokables' => array(
//            'search_service' => __NAMESPACE__ . '\Service\Search'
//        ),
    ),
    'controllers' => array(
        'invokables' => array(
            __NAMESPACE__ . '\Controller\Index' => __NAMESPACE__ . '\Controller\IndexController',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
//            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    ),
);
