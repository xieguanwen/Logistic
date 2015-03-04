<?php
namespace Logistic\Model;

use Order\Model\OrderInfo;
use Order\Model\OrderGoodsTable;
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
     * @param OrderGoodsTable $orderGoodsTable
     * @return string
     */
    public function sendOrderData(OrderInfo $orderInfo,OrderGoodsTable $orderGoodsTable){
        $dataObject = new \stdClass();
        
        $dataObject->businessMemo = $orderInfo->postscript.'#####'.$orderInfo->shipping_name.'#####'.$orderInfo->best_time;
        
        $dataObject->receiveOrderTime = date('Y-m-d H:i:s',$orderInfo->pay_time);
        
        $dataObject->custVoucherId = $orderInfo->order_sn;
//         $dataObject->customerCorpId = 'YXGWCS';// @todo 店铺编号
        $dataObject->customerCorpId = 'YXGW';
        $dataObject->receiver = $orderInfo->consignee;
        $dataObject->receiverContact = $orderInfo->consignee;
        $dataObject->receiverContactTel = $orderInfo->mobile;
        $zipcode = $orderInfo->zipcode;
        if (empty($zipcode)) {
        	$dataObject->receiverPostcode = '518000';
        } else {
            $dataObject->receiverPostcode = $orderInfo->zipcode;
        }
        $dataObject->receiverAddress = $this->getAddress($orderInfo->province,$orderInfo->city,$orderInfo->district);//合成地址
        $dataObject->receiverStreet = $orderInfo->address;
        $dataObject->transpostSum = $orderInfo->shipping_fee;
        $dataObject->salesModel = '1'; //销售模式 1：零售,2：代发3：批发,4：自提
        $dataObject->orderSource = '17';        
        $goods = $orderGoodsTable->fetchAll(array('order_id'=>$orderInfo->order_id));

        $dataObject->items = $this->mergeArray($goods);
//         print_r($dataObject);exit;
//         return preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $this->json->encode($dataObject));
        return json_encode($dataObject,JSON_UNESCAPED_UNICODE);
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
}

?>