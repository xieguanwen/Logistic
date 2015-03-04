<?php
namespace Timer\Model;

use Zend\Db\TableGateway\TableGateway;
use Common\Model\Entity;
use Common\Db\Table;

class ReceiveOrderTable extends Table
{
    public function fetchNotReceiveOrder(){
        $resultSet = $this->tableGateway->select(array('status = 0','count <= 140'));
        return $resultSet;
    }
    
    public function fetchByOrderId($id){
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

}