<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/14
 * Time: 23:24
 */

namespace Home\Controller;


use EasyWeChat\Foundation\Application;
use Think\App;
use Think\Controller;

class MemberController extends HomeController
{
    //个人中心，需要授权才能访问
    public function index(){
        //判断是否有open_id 有就不发起授权
        $this->display();
    }

    public function notes(){
        echo 22222;
    }
}