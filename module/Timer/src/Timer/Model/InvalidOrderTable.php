<?php
namespace Timer\Model;

class InvalidOrderTable
{

    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
    	$this->tableGateway = $tableGateway;
    }
    
    public function fetchAll()
    {
    	$resultSet = $this->tableGateway->select();
    	return $resultSet;
    }
    
    public function getInvalidOrder($id)
    {
    	$id = (int) $id;
    	/**
    	 *
    	 * @var $rowset ResultSet
    	 */
    	$rowset = $this->tableGateway->select(array(
    			'id' => $id
    	));
    	$row = $rowset->current();
    	if (! $row) {
    		throw new \Exception("Could not find row $id");
    	}
    	return $row;
    }
    
    /**
     *
     * @param SendOrder $sendOrder
     * @throws \Exception
     */
    public function saveInvalidOrder(InvalidOrder $invalidOrder)
    {
    	foreach ($invalidOrder as $key => $value) {
    		if ($invalidOrder->$key !== null) {
    			$data[$key] = $value;
    		}
    	}
    	$id = (int) $invalidOrder->id;
    	if ($id == 0) {
    		$this->tableGateway->insert($data);
    	} else {
    		if ($this->getInvalidOrder($id)) {
    			$this->tableGateway->update($data, array(
    					'id' => $id
    			));
    		} else {
    			throw new \Exception('Shouji_Send_Order id does not exist');
    		}
    	}
    }
}

?>