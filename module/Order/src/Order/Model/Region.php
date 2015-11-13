<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/27
 * Time: 14:40
 */

namespace Order\Model;


use Common\Model\Entity;

class Region extends Entity {
    public $region_id;
    public $parent_id;
    public $region_name;
    public $region_type;
    public $agency_id;
    public $region_code;
}