<?php
namespace Logistic;

class Module
{

    /**
     * 自动加载命名空间的
     * 
     * @return multitype:multitype:multitype:string
     */
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

    /**
     * 加载配置文件
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * 服务对象初始化
     * @return multitype:multitype:NULL
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
//                 'Order\Model\OrderInfoTable' => function ($sm){
//                     $tableGateway = $sm->get('OrderInfoTableGateway');
//                     $table = new OrderInfoTable($tableGateway);
//                     return $table;
//                 },
//                 'OrderInfoTableGateway' => function ($sm){
//                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
//                     $resultSetPrototype = new ResultSet();
//                     $resultSetPrototype->setArrayObjectPrototype(new OrderInfo());
//                     return new TableGateway('shouji_order_info', $dbAdapter, null, $resultSetPrototype);
//                 },
            )
        );
    }
}