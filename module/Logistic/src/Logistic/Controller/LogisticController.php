<?php
namespace Logistic\Controller;

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
            if ($result == 0) {
            	echo '发送成功';
            } else {
                echo $result;
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
        
}