<?php
namespace Timer;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Timer\Model\SendOrderTable;
use Timer\Model\SendOrder;
use Timer\Model\ReceiveOrderTable;
use Timer\Model\ReceiveOrder;
use Timer\Model\UpdateLogisticTable;
use Timer\Model\UpdateLogistic;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements ControllerProviderInterface
{ 
    // getAutoloaderConfig() and getConfig() methods here
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    // Add this method:
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Timer\Model\SendOrderTable' => function ($sm)
                {
                	$tableGateway = $sm->get('SendOrderTable');
                	$table = new SendOrderTable($tableGateway);
                	return $table;
                },
                'SendOrderTable' => function ($sm)
                {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new SendOrder());
                	return new TableGateway('shouji_send_order', $dbAdapter, null, $resultSetPrototype);
                },
                'Timer\Model\ReceiveOrderTable' => function ($sm)
                {
                	$tableGateway = $sm->get('ReceiveOrderTable');
                	return new ReceiveOrderTable($tableGateway, 'receive_id');
                },
                'ReceiveOrderTable' => function ($sm)
                {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new ReceiveOrder());
                	return new TableGateway('shouji_receive_order', $dbAdapter, null, $resultSetPrototype);
                },
                'Timer\Model\UpdateLogisticTable' => function ($sm)
                {
                	$tableGateway = $sm->get('UpdateLogisticTable');
                	$table = new UpdateLogisticTable($tableGateway, 'id');
                	return $table;
                },
                'UpdateLogisticTable' => function ($sm)
                {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new UpdateLogistic());
                	return new TableGateway('shouji_updatelogistic_order', $dbAdapter, null, $resultSetPrototype);
                },
            )
            
        );
    }

    public function getControllerConfig(){
        return [
            'invokables' => [
                'Timer\Controller\Timer' => 'Timer\Controller\TimerController',
                'Timer\Controller\Test' => 'Timer\Controller\TestController'
            ]
        ];
    }
}