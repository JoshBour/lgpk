<?php
namespace League;

return array(
    'service_manager' => array(
        'invokables' => array(
            'league_service' => 'League\Service\League',
            'tutorial_service' => 'League\Service\Tutorial'
        ),
        'factories' => array(
            'summoner_form' => 'League\Factory\SummonerFormFactory'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'api' => array(
        'key' => '10355068-f98b-45e6-bb3c-5411d1721307',
        'key2' => '01d38eeb-363d-46d7-bf69-1126e576d9d4'
    ),
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
    )
);
