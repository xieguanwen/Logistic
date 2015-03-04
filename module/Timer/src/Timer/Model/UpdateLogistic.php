<?php
namespace Timer\Model;

use Common\Model\Entity;
class UpdateLogistic extends Entity
{
    public $id;
    public $order_id;
    public $order_sn;
    public $addTime;
    public $updateTime;
    public $count;
    public $status; //COMMENT '0:未发送; 1:发送功;2：表示发送失败，3：在发送中，4:不发送了'
    public $type; //COMMENT '0:更新的订单。1:无效的订单'
    
}

?>