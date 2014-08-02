<?php
namespace Application;

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
            'change_language' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/change-language/:lang',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'change-language',
                        'lang' => 'el'
                    ),
                ),
            ),
            'search' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/search/:summoner/:region[/:champion][/position/:position][/opponent/:opponent][/hasCC/:hasCC][/manaless/:hasMana]',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'search',
                        'position' => '',
                        'opponent' => '',
                        'hasCC' => '',
                        'hasMana' => '',
                    ),
                    'constraints' => array(
                        'position' => 'top|middle|jungle|marksman|support',
                        'hasCC' => '0|1',
                        'hasMana' => '0|1'
                    )
                ),
            ),
            'result' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/results/:summoner/:region[/position/:position][/opponent/:opponent][/hasCC/:hasCC][/manaless/:hasMana]',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'result',
                        'position' => '',
                        'opponent' => '',
                        'hasCC' => '',
                        'hasMana' => '',
                    ),
                    'constraints' => array(
                        'position' => 'top|middle|jungle|marksman|support',
                        'hasCC' => '0|1',
                        'hasMana' => '0|1'
                    )
                ),
            ),
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/[ref/:referral]',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'home',
                        'referral' => ''
                    ),
                ),
            ),
            'referral' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/referrals',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'referral',
                    ),
                ),
            ),
            'sitemap_direct' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/sitemap.xml',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'sitemap',
                    ),
                ),
            ),
            'sitemap' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/sitemap[/:type[/:index]]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'sitemap',
                    ),
                ),
            ),
//            'contact' => array(
//                'type' => 'Zend\Mvc\Router\Http\Literal',
//                'options' => array(
//                    'route' => '/contact',
//                    'defaults' => array(
//                        'controller' => __NAMESPACE__ . '\Controller\Index',
//                        'action' => 'contact',
//                    ),
//                ),
//            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'search_form' => __NAMESPACE__ . '\Factory\SearchFormFactory',
            'referral_form' => __NAMESPACE__ . '\Factory\ReferralFormFactory',
        ),
        'invokables' => array(
            'search_service' => __NAMESPACE__ . '\Service\Search'
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'controller_plugins' => array(
        'factories' => array(
            'translate' => __NAMESPACE__ . '\Factory\TranslatePluginFactory',
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'routeName' => __NAMESPACE__ . '\Factory\ActionNameHelperFactory'
        ),
        'invokables' => array(
            'mobile' => __NAMESPACE__ . '\View\Helper\Mobile',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
        ),
        'initializers' => array(
            'entityManager' => __NAMESPACE__ . '\Factory\EntityManagerInitializer',
            'vocabulary' => __NAMESPACE__ . '\Factory\VocabularyInitializer'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'header' => __DIR__ . '/../view/partial/header.phtml',
            'footer' => __DIR__ . '/../view/partial/footer.phtml',
            'results' => __DIR__ . '/../view/application/index/result.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    ),
);
