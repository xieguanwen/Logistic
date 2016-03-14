<?php
namespace Logistic\Controller;

use Logistic\Model\LogisticXml;
use Zend\Mvc\Controller\AbstractActionController;
use Logistic\Model\CommandTransport;
use Logistic\Model\Receiver;
use Logistic\Model\Sender;
use Zend\Uri\Uri;
use Zend\Json\Json;

class LogisticController extends AbstractActionController {
    private $commandTransport;
    
    /**
     * 构造方法
     */
    public function __construct(){
        set_time_limit(1800); //设置访问时间
        date_default_timezone_set('Asia/Shanghai');//设置时区 
        $this->commandTransport = new CommandTransport(new Receiver($this),new Sender(),$this->getEventManager());
//         $this->getEventManager()->attach('att',function ($sm){print_r($sm->getParams());});
//         $this->getEventManager()->trigger('att',null,array('aksdfka'));
    }
    
    public function indexAction(){
        $sender = new Sender();
        $object = new \stdClass();
        $object->businessMemo = '要作废123';
        $object->custVoucherId = '23542354';
        $jsonData = json_encode($object,JSON_UNESCAPED_UNICODE);
        $result = $sender->send($jsonData, 'deposeSmSalerOrder');
        print_r($result);
//         print_r(json_encode(array('中国'),JSON_UNESCAPED_UNICODE));
        exit;
    }
    
    /**
     * 发送作废订单
     */
    public function invalidAction(){
    }
    
    /**
     * 接收发货订单信息
     */
    public function receiveshipAction(){
        $orderInfoTable = $this->getServiceLocator()->get('Order\Model\OrderInfoTable');
        $where = array();
        if($this->getRequest()->getQuery("order_sn",null) == null){
            $where['order_sn'] = '2016011627531';
        } else {
            $where['order_sn'] = $this->getRequest()->getQuery("order_sn",null);
        }
        $orderInfo = $orderInfoTable->fetchOne($where);
        $invoiceNo = $this->commandTransport->receiveOrder($orderInfo);
        print_r($this->commandTransport->getResponse()->getBody());
        exit(0);
    }

    public function receiveShippingAction(){
        $orderInfoTable = $this->getServiceLocator()->get('Order\Model\OrderInfoTable');
        $receiveOrderTable = $this->getServiceLocator()->get('Timer\Model\ReceiveOrderTable');
        $where = array();
        $where['order_sn'] = $this->getRequest()->getQuery("order_sn",null);
        $orderInfo = $orderInfoTable->fetchOne($where);

        $receiveOrder = $receiveOrderTable->fetchOne(array("order_id"=>$orderInfo->order_id));
        if($receiveOrder->status == 0){
            $invoiceNo = $this->commandTransport->receiveOrder($orderInfo);
            if($invoiceNo){
                $this->commandTransport->changeOrder($orderInfo,$orderInfoTable,$invoiceNo);
                $this->commandTransport->changeReceiveStatus($receiveOrder,$receiveOrderTable);
                $this->commandTransport->sendSms($orderInfo);
                $this->commandTransport->sendKuaiDi($orderInfo);
                print_r("物流号：".$invoiceNo);
            }
        } elseif($receiveOrder->status == 1){
            print_r("已经接收过了");
        }
        print_r("没有成功");
        exit(0);
    }



    
    /**
     * 发送订单信息
     */
    public function sendAction(){
        $orderInfoTable = $this->getServiceLocator()->get('Order\Model\OrderInfoTable');
        $orderGoodsTable = $this->getServiceLocator()->get('Order\Model\OrderGoodsTable');
        if ($this->getRequest()->getQuery('order_sn')) {
            $orderInfo = $orderInfoTable->fetchOne(array('order_sn'=>$this->getRequest()->getQuery('order_sn')));
            $result = $this->commandTransport->sendOrder($orderInfo, $orderGoodsTable);
            if ($result) {
            	echo '发送成功';
            } else {
                echo '没有发送成功,请查看日志';
            }
        }
        exit;
    }
    
    /**
     * 发送更新订单信息
     */
    public function updateAction(){
        
    }
    
    /**
     * 批量发送作废订单
     */
    public function batchinvalidAction(){
        /**
         * @var UpdateLogisticTable $updateLogisticTable
         */
        $updateLogisticTable = $this->getServiceLocator()->get('Timer\Model\UpdateLogisticTable');
        $OrderInfoTable = $this->getServiceLocator()->get('Order\Model\OrderInfoTable');
        $this->commandTransport->batchSendInvalidOrder($updateLogisticTable,$OrderInfoTable);
        exit;
    }
    
    /**
     * 批量接收发货订单信息
     */
    public function batchreceiveshipAction(){
        $receiveOrderTable = $this->getServiceLocator()->get('Timer\Model\ReceiveOrderTable');
        $orderInfoTable = $this->getServiceLocator()->get('Order\Model\OrderInfoTable');
//        $orderGoodsTable = $this->getServiceLocator()->get('Order\Model\OrderGoodsTable');
        $this->commandTransport->receiveOrderPretreatment($orderInfoTable,$receiveOrderTable);//pretreatment
        $this->commandTransport->batchReceiveOrder($receiveOrderTable, $orderInfoTable);
        exit;
    }
    
    /**
     * 批量发送订单信息
     */
    public function batchsendAction(){
        $sendOrderTable = $this->getServiceLocator()->get('Timer\Model\SendOrderTable');
        $orderInfoTable = $this->getServiceLocator()->get('Order\Model\OrderInfoTable');
        $orderGoodsTable = $this->getServiceLocator()->get('Order\Model\OrderGoodsTable');
        $this->commandTransport->sendOrderPretreatment($orderInfoTable, $sendOrderTable);//pretreatment
        $this->commandTransport->batchSendOrder($sendOrderTable, $orderInfoTable, $orderGoodsTable);
        exit;
    }
    
    /**
     * 批量发送更新订单信息
     */
    public function batchupdateAction(){
        $updateLogisticTable = $this->getServiceLocator()->get('Timer\Model\UpdateLogisticTable');
        $orderInfoTable = $this->getServiceLocator()->get('Order\Model\OrderInfoTable');
        $orderGoodsTable = $this->getServiceLocator()->get('Order\Model\OrderGoodsTable');
        $this->commandTransport->batchSendUpdateOrder($updateLogisticTable, $orderInfoTable, $orderGoodsTable);
        exit;
    }


    public function testxmlAction(){
        $stringXml = '<?xml version="1.0" encoding="utf-8" ?><TradeOrdersNew><trade_orders_response><trade><created>2012-8-11 18:46:19</created><tid>DD00006772</tid></trade></trade_orders_response></TradeOrdersNew>';
        $stringXml = '<?xml version="1.0" encoding="utf-8" ?><TradeOrdersNew> <ERROR>itemsns:ECS000032不存在,或者已经停用</ERROR></TradeOrdersNew>';
        $logisticXml = new LogisticXml();
        $array = $logisticXml->getTid($stringXml);
//        print_r($array['trade_orders_response']['trade']['tid']);
        print_r($array['ERROR']);
        exit(0);
    }
}