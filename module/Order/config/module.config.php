<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Order\Controller\Index' => 'Order\Controller\IndexController'
        )
    ),
    
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'order' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/order[/][:action][[/:id][/page/:page]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'page'=>'[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Order\Controller\Index',
                        'action' => 'index'
                    )
                )
            )
        )
    ),
    
    'view_manager' => array(
        'template_map' => array(
        		'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
//         		'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
//         		'error/404' => __DIR__ . '/../view/error/404.phtml',
//         		'error/index' => __DIR__ . '/../view/error/index.phtml'
        ),
        'template_path_stack' => array(
            'order' => __DIR__ . '/../view'
        )
    )
);