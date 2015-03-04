<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Logistic\Controller\Logistic' => 'Logistic\Controller\LogisticController',
            'Logistic\Controller\Apilogistic' => 'Logistic\Controller\ApilogisticController'
        )
    ),
    
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'logistic' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/logistic[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Logistic\Controller\Logistic',
                        'action' => 'index'
                    )
                )
            ),
            'ApiLogistic' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/apilogistic[/:id]',
                    'constraints' => array(
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Logistic\Controller\Apilogistic'
                    )
                )
            )
        )
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'logistic' => __DIR__ . '/../view'
        )
    )
);