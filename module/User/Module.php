<?php
namespace User;

use User\Model\Users;
use User\Model\UsersTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

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
        return array();
//        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * 服务对象初始化
     * @return multitype:multitype:NULL
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                 'User\Model\UsersTable' => function ($sm){
                     $tableGateway = $sm->get('UsersTableGateway');
                     $table = new UsersTable($tableGateway,'user_id');
                     return $table;
                 },
                 'UsersTableGateway' => function ($sm){
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Users());
                     return new TableGateway('shouji_users', $dbAdapter, null, $resultSetPrototype);
                 },
            )
        );
    }
}