<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/24
 * Time: 21:50
 */

namespace Home\Model;


use Think\Model;

class TestModel extends Model
{
    Protected $autoCheckFields = false;     //虚拟模型
    public function test(){
        $a=3;
        $b=5;
        if($a=0 || $b=8){
            $a++;
            $b++;
        }
        return $a."".$b;
    }
}