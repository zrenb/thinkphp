<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 18:16
 */

namespace Home\Controller;


use Home\Model\PropertyModel;
use Home\Model\TestModel;
use Think\Controller;

class TestController extends Controller
{

    public function test(){

        $simplexml=simplexml_load_file('http://flash.weather.com.cn/wmaps/xml/sichuan.xml');
        $weather=[];

        foreach ($simplexml as $name=>$value){
            $weather[(string)$value['cityname']]=(string)$value['stateDetailed'];
        }
        echo "<pre>";
        var_dump($weather["南充"]);
    }


    public function testa(){
       $a=10/2;
       $b=floor($a/2);
       $c=floor(($a+$b)/4);
       $num=$a+$b+$c;
       var_dump($num);
    }



    public function index(){
        //var_dump(phpinfo());
        $a=3;
        $b=5;
        if($a=5 || $b=7){
            $a++;
            $b++;
        }
        echo $a." ".$b;
    }
}