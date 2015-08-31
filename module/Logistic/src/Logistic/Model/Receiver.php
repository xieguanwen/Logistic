<?php
namespace Logistic\Model;

use Order\Model\Common;
use Order\Model\OrderInfo;
use Order\Model\OrderGoodsTable;
use Zend\Debug\Debug;
use Zend\Json\Json;
use Timer\Model\UpdateLogisticTable;
use Timer\Model\UpdateLogistic;
use Zend\Db\Sql\Sql;
use Zend\Mvc\Controller\AbstractController;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class Receiver
{ 
    private $controller;
    
    public function __construct(AbstractController $controller){
        $this->controller = $controller;
    }
    
    /**
     * 无效订单数据处理
     * @param OrderInfo $orderInfo
     * @return string
     */
    public function invalidOrderData(OrderInfo $orderInfo){
        $object = new \stdClass();
        $object->businessMemo = $orderInfo->to_buyer.'这个订单作废。';
        $object->custVoucherId = $orderInfo->order_sn;
        return json_encode($object,JSON_UNESCAPED_UNICODE);
    }

    /**
     * 发送订单数据处理
     * @param OrderInfo $orderInfo
     * @return string
     */
    public function receiveOrderData(OrderInfo $orderInfo){
        $data = [];
//        $data['condition'] = 'outer_tid='.$orderInfo->order_sn;
//        $data['condition'] = 'DJBH='.$orderInfo->order_sn;
//        $data['condition'] = "DJBH='DD00034320'";
//        $data['condition'] = "lydh='2015081791966'";
        $data['condition'] = "lydh='".$orderInfo->order_sn."'";
        return $data;
    }

    /**
     * 发送订单数据处理
     * @param OrderInfo $orderInfo
     * @param OrderGoodsTable $orderGoodsTable
     * @return array
     */
    public function sendOrderData(OrderInfo $orderInfo,OrderGoodsTable $orderGoodsTable){
        $data = array();
        $productTable = $this->controller->getServiceLocator()->get('Order\Model\ProductTable');
        $shippingTable = $this->controller->getServiceLocator()->get('Order\Model\ShippingTable');

        $data['mail'] = 'hyly0922';
        $data['itemsns'] = Common::getItemsns($orderGoodsTable,$productTable,$orderInfo->order_id);
//        $data['itemsns'] = 'ICNL600';
        $data['prices'] = Common::getPrices($orderGoodsTable,$orderInfo->order_id);
        $data['nums'] = Common::getNumbers($orderGoodsTable,$orderInfo->order_id);
        $data['receiver_name'] = $orderInfo->consignee;
        $data['receiver_address'] = $orderInfo->address;
        $data['receiver_state'] = $this->getRegionName($orderInfo->province);
        $data['receiver_city'] = $this->getRegionName($orderInfo->city);
        $data['receiver_district'] = $this->getRegionName($orderInfo->district);
        $data['logistics_type'] = strtoupper(Common::getShippingCode($shippingTable,$orderInfo->shipping_id));
        $data['outer_tid'] = $orderInfo->order_sn;
        $data['outer_shop_code'] = "yxgw";
        $data['receiver_mobile'] = $orderInfo->mobile;
        $data['pay_datatimes'] = date("Y-m-d",$orderInfo->pay_time);
        $data['pay_moneys'] = $orderInfo->order_amount;
        $data['pay_codes'] = '001';
        $data['pay_trade_ids'] = ' ';
        $data['pay_accounts'] = ' ';
        $data['pay_memos'] = ' ';
        return $data;
    }
    
    /**
     * 返回
     * @param UpdateLogisticTable $updateLogisticTable
     * @param UpdateLogistic $updateLogistic
     * @param bool $result
     */
    public function receive(UpdateLogisticTable $updateLogisticTable,UpdateLogistic $updateLogistic,$status){
        $row = new UpdateLogistic();
        $row->id = $updateLogistic->id;
        if ($status == 0) {
        	$row->status = 1;
        } else {
            if ($updateLogistic->count > 3) {
            	$row->status = 4;
            }            
        }
        $row->count = $updateLogistic->count + 1;
        $updateLogisticTable->save($row);
        return $row->status == 1;
    }
     
    /**
     * @param string $province
     * @param string $city
     * @param string $district
     * @return string
     */
    private function getAddress($province,$city,$district){
    	$adapter = $this->controller->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    	$sql = new Sql($adapter);
    	$select = $sql->select();
    	$select->columns(array('region_name'));
    	$select->from('shouji_region');
    	$where = new Where();
    	$where->in('region_id',array($province,$city,$district));
    	$select->where($where);
    
    	$selectString = $sql->getSqlStringForSqlObject($select);
    
    	$resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    	$address = '';
    	foreach ($resultSet as $row) {
    		$address = $address.$row['region_name'];
    	}
    	return $address;
    }
    
    /**
     * @param ResultSet $resultSet
     * @return multitype:\stdClass
     */
    private function mergeArray(ResultSet $resultSet){
    	$rows = iterator_to_array($resultSet);
    	$items = array();
    	$itemName = array();
    	//合并相同的物料号(价格和数量相加)
    	foreach ($rows as $key => $row) {
    		if (in_array($row->goods_sn, $itemName)) {
    			$items[$row->goods_sn]->goods_price = $items[$row->goods_sn]->goods_price + $row->goods_price;
    			$items[$row->goods_sn]->goods_number = $items[$row->goods_sn]->goods_number + $row->goods_number;
    		} else {
    			$itemName[] = $row->goods_sn;
    			$items[$row->goods_sn] = $row;
    		}
    	}
    
    	$returnArray = array();
    	foreach ($items as $item) {
    		$itemObject = new \stdClass();
    		$itemObject->productId= trim($item->goods_sn);
    		$itemObject->unitPrice= $item->goods_price;
    		$itemObject->planQty= $item->goods_number;
    		$itemObject->stockStatus = 'OK';
    		$itemObject->serialSign= '001';
    		$returnArray[] = $itemObject;
    	}
    	return $returnArray;
    }

    private function getRegionName($regionId){
        $regionTable = $this->controller->getServiceLocator()->get("Order\Model\RegionTable");
        $row = $regionTable->fetch($regionId);
        return $row->region_name;
    }
}

?>