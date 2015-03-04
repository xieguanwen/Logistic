<?php
namespace Timer\Model;

use Common\Model\Entity;

class SendOrder extends Entity
{
    public $send_id;
    public $order_id;
    public $send_time;
    public $send_count;
    public $send_update_time;
    public $status;

    /**
     *
     * @param array $data            
     */
    public function exchangeArray($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}