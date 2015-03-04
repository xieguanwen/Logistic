<?php
namespace Timer\Model;

use Zend\Db\TableGateway\TableGateway;
use Common\Db\Table;

class SendOrderTable extends Table
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway, 'send_id');
    }

    public function getSendOrder($id)
    {
        $id = (int) $id;
        /**
         *
         * @var $rowset ResultSet
         */
        $rowset = $this->tableGateway->select(array(
            'send_id' => $id
        ));
        $row = $rowset->current();
        if (! $row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getSendOrderByOrderId($id)
    {
        $id = (int) $id;
        /**
         *
         * @var $rowset ResultSet
         */
        $rowset = $this->tableGateway->select(array(
            'order_id' => $id
        ));
        $row = $rowset->current();
        if (! $row) {
            return null;
        }
        return $row;
    }

    /**
     *
     * @param SendOrder $sendOrder            
     * @throws \Exception
     * @todo 还没有做好
     */
    public function saveSendOrder(SendOrder $sendOrder)
    {
//         $data = get_object_vars($sendOrder);
        foreach ($sendOrder as $key => $value) {
        	if ($sendOrder->$key !== null) {
        		$data[$key] = $value;
        	}
        }
        $id = (int) $sendOrder->send_id;
        if ($id == 0) {
          $this->tableGateway->insert($data);
        } else {
            if ($this->getSendOrder($id)) {
                $this->tableGateway->update($data, array(
                    'send_id' => $id
                ));
            } else {
                throw new \Exception('Shouji_Send_Order id does not exist');
            }
        }
    }
    
    public function deleteOrderInfo($id)
    {
        $this->tableGateway->delete(array(
            'order_id' => (int) $id
        ));
    }
    
    public function fetchNotSendOrder(){
        $resultSet = $this->tableGateway->select(array('status'=>0));
        return $resultSet;
    }
}