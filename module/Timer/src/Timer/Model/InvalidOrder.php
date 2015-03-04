<?php
namespace Timer\Model;

class InvalidOrder
{
    public $id;
    public $order_id;
    public $count;
    public $addtime;
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

?>