<?php
namespace Common\Db;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Common\Model\Entity;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Where;
use Zend\Filter\StaticFilter;

class Table
{
    protected $privateKey;
    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * @param TableGateway $tableGateway
     * @param String $privateKey
     */
    public function __construct(TableGateway $tableGateway, $privateKey)
    {
        $this->tableGateway = $tableGateway;
        $this->privateKey = $privateKey;
    }

    /**
     * @param mixd $where
     * @return ResultSet
     */
    public function fetchAll($where = null)
    {
        $resultSet = $this->tableGateway->select($where);
        return $resultSet;
    }

    public function fetch($id)
    {
        $id = (int)$id;
        /**
         *
         * @var $rowset ResultSet
         */
        $rowset = $this->tableGateway->select(array(
            $this->privateKey => $id
        ));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     * @param string|array|Where $where
     */
    public function fetchOne($where)
    {
        $rowset = $this->tableGateway->select($where);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row");
        }
        return $row;
    }

    /**
     * list data for table
     *
     * @param $offset
     * @param $limit
     * @param array $order
     * @param string $columns
     * @param Where|\Closure|string|array $where
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetchList($offset, $limit, $order = null, $columns = null, $where = null)
    {
        $select = new Select();
        $select->from($this->tableGateway->getTable());
        $select->offset($offset);
        $select->limit($limit);
        if ($where !== null) $select->where($where);
        if ($columns !== null) $select->columns($columns);
        if ($order !== null) $select->order($order);
        return $this->tableGateway->selectWith($select);
    }

    /**
     *
     * @param Entity $entity
     * @throws \Exception
     */
    public function save(Entity $entity)
    {
        foreach ($entity as $key => $value) {
            if ($entity->$key !== null) {
                $value = StaticFilter::execute($value, 'HtmlEntities', array(">", "<", "&"));
                $data[$key] = $value;
            }
        }
        $id = (int)$entity->{$this->privateKey};
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->fetch($id)) {
                $this->tableGateway->update($data, array(
                    $this->privateKey => $id
                ));
            } else {
                throw new \Exception('table id does not exist');
            }
        }
    }

    /**
     * @param int $id
     */
    public function delete($id)
    {
        $this->tableGateway->delete(array(
            $this->privateKey => (int)$id
        ));
    }

}

?>