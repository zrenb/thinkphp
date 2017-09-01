<?php

namespace Admin\Model;

use Think\Model;

class PropertyModel extends Model
{
    protected $_validate = array(
        array('name', 'require', '修改人', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('tel', '#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$#', '电话', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('address', 'require', '地址', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('prob', 'require', '问题', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', 'require', '标题', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        //array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );


}