<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-9-2
 * Time: 下午2:39
 */
return array(
    'controllers' => array(
        'invokables' => array(
            'Analytics\Controller\Analytics' => 'Analytics\Controller\AnalyticsController'
        )
    ),
    
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'analytics' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/analytics[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Analytics\Controller\Analytics',
                        'action' => 'index'
                    )
                )
            )
        )
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'analytics' => __DIR__ . '/../view'
        )
    )
);