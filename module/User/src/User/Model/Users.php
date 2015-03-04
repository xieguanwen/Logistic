<?php
namespace User\Model;

use Common\Model\Entity;

class Users extends Entity {
    public $user_id;
    public $aite_id;
    public $email;
    public $user_name;
    public $password;
    public $question;
    public $answer;
    public $sex;
    public $birthday;
    public $user_money;
    public $frozen_money;
    public $pay_points;
    public $creditsbak;
    public $rank_points;
    public $address_id;
    public $reg_time;
    public $last_login;
    public $last_time;
    public $last_ip;
    public $visit_count;
    public $user_rank;
    public $is_special;
    public $ec_salt;
    public $salt;
    public $parent_id;
    public $flag;
    public $alias;
    public $msn;
    public $qq;
    public $office_phone;
    public $home_phone;
    public $mobile_phone;
    public $is_validated;
    public $credit_line;
    public $passwd_question;
    public $passwd_answer;
}