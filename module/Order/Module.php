<?php
namespace Order;

use Order\Model\OrderInfo;
use Order\Model\OrderInfoTable;
use Order\Model\Product;
use Order\Model\ProductTable;
use Order\Model\Region;
use Order\Model\RegionTable;
use Order\Model\Shipping;
use Order\Model\ShippingTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Order\Model\OrderGoods;
use Order\Model\OrderGoodsTable;

class Module
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
    
    /**
     * @return multitype:multitype:NULL  |\Order\OrderInfoTable|\Order\TableGateway|\Order\OrderGoodsTable
     */
    public function getServiceConfig()
    {
    	return array(
			'factories' => array(
				'Order\Model\OrderInfoTable' => function ($sm)
				{
					$tableGateway = $sm->get('OrderInfoTableGateway');
					$table = new OrderInfoTable($tableGateway);
					return $table;
				},
				'OrderInfoTableGateway' => function ($sm)
				{
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new OrderInfo());
					return new TableGateway('shouji_order_info', $dbAdapter, null, $resultSetPrototype);
				},
				'Order\Model\OrderGoodsTable' => function ($sm)
				{
					$tableGateway = $sm->get('OrderGoodsTableGateway');
					$table = new OrderGoodsTable($tableGateway);
					return $table;
				},
				'OrderGoodsTableGateway' => function ($sm)
				{
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new OrderGoods());
					return new TableGateway('shouji_order_goods', $dbAdapter, null, $resultSetPrototype);
				},
				'Order\Model\ProductTable' => function ($sm)
				{
					$tableGateway = $sm->get('ProductTableGateway');
					$table = new ProductTable($tableGateway,'product_id');
					return $table;
				},
				'ProductTableGateway' => function ($sm)
				{
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Product());
					return new TableGateway('shouji_products', $dbAdapter, null, $resultSetPrototype);
				},
                'Order\Model\RegionTable' => function ($sm)
                {
                    $tableGateway = $sm->get('RegionTableGateway');
                    $table = new RegionTable($tableGateway,'region_id');
                    return $table;
                },
                'RegionTableGateway' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Region());
                    return new TableGateway('shouji_region', $dbAdapter, null, $resultSetPrototype);
                },

                'Order\Model\ShippingTable' => function ($sm)
                {
                    $tableGateway = $sm->get('ShippingTableGateway');
                    $table = new ShippingTable($tableGateway,'shipping_id');
                    return $table;
                },
                'ShippingTableGateway' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Shipping());
                    return new TableGateway('shouji_shipping', $dbAdapter, null, $resultSetPrototype);
                },
    		)
    
    	);
    }
}