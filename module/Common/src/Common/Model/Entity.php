<?php
namespace Common\Model;

class Entity
{
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
