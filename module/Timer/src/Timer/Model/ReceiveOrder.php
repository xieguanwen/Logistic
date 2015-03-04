<?php
namespace Timer\Model;

use Common\Model\Entity;

class ReceiveOrder extends Entity
{
    public $receive_id;
    public $order_id;
    public $order_sn;
    public $count;
    public $status;
}