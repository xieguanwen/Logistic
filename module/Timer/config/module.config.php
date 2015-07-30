<?php
return array(
    'controllers' => array(
//        'invokables' => array(
//            'Timer\Controller\Timer' => 'Timer\Controller\TimerController',
//            'Timer\Controller\Test' => 'Timer\Controller\TestController'
//        )
    ),
    
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'timer' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/timer[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Timer\Controller\Timer',
                        'action' => 'index'
                    )
                )
            )
        ),
        'routes' => array(
            'test' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/test[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Timer\Controller\Test',
                        'action' => 'index'
                    )
                )
            )
        ),
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'timer' => __DIR__ . '/../view'
        )
    )
);